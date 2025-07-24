-- Database schema for Sistema Web de Análisis de Precios y Programa de Obra
-- Authentication and role-based access control system

-- Create database (uncomment if needed)
-- CREATE DATABASE IF NOT EXISTS ariel_sistema CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- USE ariel_sistema;

-- Users table with role-based access control
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    rol ENUM('admin', 'analista', 'visitante') NOT NULL DEFAULT 'visitante',
    activo BOOLEAN DEFAULT TRUE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    ultimo_acceso TIMESTAMP NULL,
    intentos_login INT DEFAULT 0,
    bloqueado_hasta TIMESTAMP NULL,
    
    INDEX idx_email (email),
    INDEX idx_rol (rol),
    INDEX idx_activo (activo)
) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Activity log table for security auditing
CREATE TABLE IF NOT EXISTS log_actividad (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NULL,
    accion VARCHAR(100) NOT NULL,
    descripcion TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_usuario_id (usuario_id),
    INDEX idx_fecha (fecha),
    INDEX idx_accion (accion)
) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Sessions table for better session management
CREATE TABLE IF NOT EXISTS user_sessions (
    id VARCHAR(128) PRIMARY KEY,
    usuario_id INT NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NOT NULL,
    
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_usuario_id (usuario_id),
    INDEX idx_expires_at (expires_at)
) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Insert default admin user (password: admin123)
-- Note: In production, change this password immediately
INSERT IGNORE INTO usuarios (email, password_hash, nombre, rol) VALUES 
('admin@empresa.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrador', 'admin');

-- Create indexes for better performance
CREATE INDEX idx_usuarios_email_activo ON usuarios(email, activo);
CREATE INDEX idx_log_usuario_fecha ON log_actividad(usuario_id, fecha);