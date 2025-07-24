-- Creación de la base de datos para el Sistema de Análisis de Precios y Programa de Obra
-- MySQL 8.0+

-- Crear base de datos si no existe
CREATE DATABASE IF NOT EXISTS construccion_db 
    CHARACTER SET utf8mb4 
    COLLATE utf8mb4_unicode_ci;

USE construccion_db;

-- Tabla de usuarios
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    rol ENUM('admin', 'analista', 'visitante') NOT NULL DEFAULT 'visitante',
    activo BOOLEAN DEFAULT TRUE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    ultimo_acceso TIMESTAMP NULL,
    
    INDEX idx_email (email),
    INDEX idx_rol (rol),
    INDEX idx_activo (activo)
) ENGINE=InnoDB;

-- Tabla de obras/proyectos
CREATE TABLE obras (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(200) NOT NULL,
    descripcion TEXT,
    ubicacion VARCHAR(300),
    cliente VARCHAR(150),
    contratista VARCHAR(150),
    presupuesto_inicial DECIMAL(15,2) DEFAULT 0.00,
    presupuesto_actual DECIMAL(15,2) DEFAULT 0.00,
    avance_fisico DECIMAL(5,2) DEFAULT 0.00,
    avance_financiero DECIMAL(5,2) DEFAULT 0.00,
    fecha_inicio DATE,
    fecha_fin_programada DATE,
    fecha_fin_real DATE NULL,
    estado ENUM('activo', 'pausado', 'completado', 'cancelado') DEFAULT 'activo',
    usuario_responsable_id INT,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (usuario_responsable_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_estado (estado),
    INDEX idx_fecha_inicio (fecha_inicio),
    INDEX idx_responsable (usuario_responsable_id)
) ENGINE=InnoDB;

-- Tabla de categorías de conceptos
CREATE TABLE categorias_conceptos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    codigo VARCHAR(20) UNIQUE,
    activo BOOLEAN DEFAULT TRUE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_codigo (codigo),
    INDEX idx_activo (activo)
) ENGINE=InnoDB;

-- Tabla de conceptos de obra
CREATE TABLE conceptos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    obra_id INT NOT NULL,
    categoria_id INT,
    codigo VARCHAR(50),
    nombre VARCHAR(300) NOT NULL,
    descripcion TEXT,
    unidad VARCHAR(20) NOT NULL,
    cantidad DECIMAL(12,4) DEFAULT 0.0000,
    precio_unitario DECIMAL(12,4) DEFAULT 0.0000,
    importe DECIMAL(15,2) GENERATED ALWAYS AS (cantidad * precio_unitario) STORED,
    avance_cantidad DECIMAL(12,4) DEFAULT 0.0000,
    avance_importe DECIMAL(15,2) GENERATED ALWAYS AS (avance_cantidad * precio_unitario) STORED,
    fecha_inicio DATE,
    fecha_fin DATE,
    activo BOOLEAN DEFAULT TRUE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (obra_id) REFERENCES obras(id) ON DELETE CASCADE,
    FOREIGN KEY (categoria_id) REFERENCES categorias_conceptos(id) ON DELETE SET NULL,
    INDEX idx_obra (obra_id),
    INDEX idx_categoria (categoria_id),
    INDEX idx_codigo (codigo),
    INDEX idx_activo (activo)
) ENGINE=InnoDB;

-- Tabla de materiales
CREATE TABLE materiales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(50) UNIQUE,
    nombre VARCHAR(200) NOT NULL,
    descripcion TEXT,
    unidad VARCHAR(20) NOT NULL,
    precio_unitario DECIMAL(12,4) DEFAULT 0.0000,
    proveedor_principal VARCHAR(150),
    stock_minimo DECIMAL(10,2) DEFAULT 0.00,
    stock_actual DECIMAL(10,2) DEFAULT 0.00,
    activo BOOLEAN DEFAULT TRUE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_codigo (codigo),
    INDEX idx_nombre (nombre),
    INDEX idx_activo (activo)
) ENGINE=InnoDB;

-- Tabla de mano de obra
CREATE TABLE mano_obra (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(50) UNIQUE,
    nombre VARCHAR(200) NOT NULL,
    descripcion TEXT,
    salario_base DECIMAL(10,2) DEFAULT 0.00,
    prestaciones_porcentaje DECIMAL(5,2) DEFAULT 0.00,
    costo_hora DECIMAL(10,4) GENERATED ALWAYS AS (
        (salario_base * (1 + prestaciones_porcentaje/100)) / 8
    ) STORED,
    activo BOOLEAN DEFAULT TRUE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_codigo (codigo),
    INDEX idx_nombre (nombre),
    INDEX idx_activo (activo)
) ENGINE=InnoDB;

-- Tabla de maquinaria y equipo
CREATE TABLE maquinaria (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(50) UNIQUE,
    nombre VARCHAR(200) NOT NULL,
    descripcion TEXT,
    costo_hora DECIMAL(10,4) DEFAULT 0.0000,
    costo_operacion_hora DECIMAL(10,4) DEFAULT 0.0000,
    costo_total_hora DECIMAL(10,4) GENERATED ALWAYS AS (costo_hora + costo_operacion_hora) STORED,
    activo BOOLEAN DEFAULT TRUE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_codigo (codigo),
    INDEX idx_nombre (nombre),
    INDEX idx_activo (activo)
) ENGINE=InnoDB;

-- Tabla de análisis de precios unitarios
CREATE TABLE analisis_precios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    concepto_id INT NOT NULL,
    tipo_recurso ENUM('material', 'mano_obra', 'maquinaria', 'otro') NOT NULL,
    recurso_id INT, -- ID del material, mano de obra o maquinaria
    descripcion VARCHAR(300),
    unidad VARCHAR(20),
    cantidad DECIMAL(12,6) DEFAULT 0.000000,
    precio_unitario DECIMAL(12,4) DEFAULT 0.0000,
    importe DECIMAL(15,4) GENERATED ALWAYS AS (cantidad * precio_unitario) STORED,
    porcentaje_indirectos DECIMAL(5,2) DEFAULT 0.00,
    porcentaje_utilidad DECIMAL(5,2) DEFAULT 0.00,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (concepto_id) REFERENCES conceptos(id) ON DELETE CASCADE,
    INDEX idx_concepto (concepto_id),
    INDEX idx_tipo_recurso (tipo_recurso),
    INDEX idx_recurso (recurso_id)
) ENGINE=InnoDB;

-- Tabla de proveedores
CREATE TABLE proveedores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    razon_social VARCHAR(200) NOT NULL,
    nombre_comercial VARCHAR(200),
    rfc VARCHAR(15),
    email VARCHAR(150),
    telefono VARCHAR(20),
    direccion TEXT,
    ciudad VARCHAR(100),
    estado VARCHAR(100),
    codigo_postal VARCHAR(10),
    contacto_principal VARCHAR(150),
    activo BOOLEAN DEFAULT TRUE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_razon_social (razon_social),
    INDEX idx_rfc (rfc),
    INDEX idx_activo (activo)
) ENGINE=InnoDB;

-- Tabla de cotizaciones de proveedores
CREATE TABLE cotizaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    proveedor_id INT NOT NULL,
    material_id INT NOT NULL,
    precio_unitario DECIMAL(12,4) NOT NULL,
    moneda VARCHAR(3) DEFAULT 'MXN',
    fecha_cotizacion DATE NOT NULL,
    fecha_vigencia DATE,
    condiciones_pago VARCHAR(100),
    tiempo_entrega VARCHAR(100),
    activa BOOLEAN DEFAULT TRUE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (proveedor_id) REFERENCES proveedores(id) ON DELETE CASCADE,
    FOREIGN KEY (material_id) REFERENCES materiales(id) ON DELETE CASCADE,
    INDEX idx_proveedor (proveedor_id),
    INDEX idx_material (material_id),
    INDEX idx_fecha_cotizacion (fecha_cotizacion),
    INDEX idx_activa (activa)
) ENGINE=InnoDB;

-- Tabla de programa de obra
CREATE TABLE programa_obra (
    id INT AUTO_INCREMENT PRIMARY KEY,
    obra_id INT NOT NULL,
    concepto_id INT NOT NULL,
    periodo INT NOT NULL, -- Número de período (semana, quincena, mes)
    tipo_periodo ENUM('semanal', 'quincenal', 'mensual') DEFAULT 'mensual',
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE NOT NULL,
    cantidad_programada DECIMAL(12,4) DEFAULT 0.0000,
    cantidad_ejecutada DECIMAL(12,4) DEFAULT 0.0000,
    porcentaje_avance DECIMAL(5,2) GENERATED ALWAYS AS (
        CASE 
            WHEN cantidad_programada > 0 THEN (cantidad_ejecutada / cantidad_programada) * 100
            ELSE 0
        END
    ) STORED,
    observaciones TEXT,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (obra_id) REFERENCES obras(id) ON DELETE CASCADE,
    FOREIGN KEY (concepto_id) REFERENCES conceptos(id) ON DELETE CASCADE,
    INDEX idx_obra (obra_id),
    INDEX idx_concepto (concepto_id),
    INDEX idx_periodo (periodo),
    INDEX idx_fecha_inicio (fecha_inicio),
    UNIQUE KEY unique_obra_concepto_periodo (obra_id, concepto_id, periodo)
) ENGINE=InnoDB;

-- Tabla de reportes de avance
CREATE TABLE reportes_avance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    obra_id INT NOT NULL,
    fecha_reporte DATE NOT NULL,
    periodo_reportado VARCHAR(50),
    avance_fisico_total DECIMAL(5,2) DEFAULT 0.00,
    avance_financiero_total DECIMAL(5,2) DEFAULT 0.00,
    observaciones_generales TEXT,
    usuario_reporte_id INT,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (obra_id) REFERENCES obras(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_reporte_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_obra (obra_id),
    INDEX idx_fecha_reporte (fecha_reporte),
    INDEX idx_usuario (usuario_reporte_id)
) ENGINE=InnoDB;

-- Tabla de sesiones de usuario (opcional, para mejor control de sesiones)
CREATE TABLE sesiones_usuario (
    id VARCHAR(128) PRIMARY KEY,
    usuario_id INT NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    datos_sesion TEXT,
    ultima_actividad TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_usuario (usuario_id),
    INDEX idx_ultima_actividad (ultima_actividad)
) ENGINE=InnoDB;

-- Tabla de logs de auditoría
CREATE TABLE logs_auditoria (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    accion VARCHAR(100) NOT NULL,
    tabla_afectada VARCHAR(50),
    registro_id INT,
    datos_anteriores JSON,
    datos_nuevos JSON,
    ip_address VARCHAR(45),
    user_agent TEXT,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_usuario (usuario_id),
    INDEX idx_accion (accion),
    INDEX idx_tabla (tabla_afectada),
    INDEX idx_fecha (fecha_creacion)
) ENGINE=InnoDB;