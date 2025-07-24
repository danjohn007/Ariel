-- Inserción de datos de prueba
-- Sistema de Análisis de Precios y Programa de Obra

USE construccion_db;

-- Insertar usuarios de prueba
INSERT INTO usuarios (nombre, apellidos, email, password, rol, activo) VALUES
('Administrador', 'del Sistema', 'admin@construccion.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', TRUE),
('Analista', 'de Precios', 'analista@construccion.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'analista', TRUE),
('Usuario', 'Visitante', 'visitante@construccion.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'visitante', TRUE);

-- Insertar categorías de conceptos
INSERT INTO categorias_conceptos (nombre, descripcion, codigo) VALUES
('Preliminares', 'Trabajos preliminares y preparación del sitio', 'PREL'),
('Cimentación', 'Trabajos de cimentación y estructura', 'CIM'),
('Estructura', 'Estructura de concreto y acero', 'EST'),
('Albañilería', 'Trabajos de albañilería y muros', 'ALB'),
('Instalaciones', 'Instalaciones eléctricas, hidráulicas y especiales', 'INST'),
('Acabados', 'Trabajos de acabados y terminaciones', 'ACAB'),
('Obras Exteriores', 'Trabajos exteriores y urbanización', 'EXT');

-- Insertar materiales básicos
INSERT INTO materiales (codigo, nombre, descripcion, unidad, precio_unitario, proveedor_principal) VALUES
('CEM-001', 'Cemento Portland Gris', 'Cemento Portland Gris CPC 30R', 'Ton', 2800.00, 'CEMEX'),
('AGR-001', 'Arena', 'Arena de río cribada', 'M3', 350.00, 'Agregados del Centro'),
('AGR-002', 'Grava', 'Grava de 3/4" triturada', 'M3', 420.00, 'Agregados del Centro'),
('VAR-001', 'Varilla #3', 'Varilla corrugada #3 (3/8")', 'Ton', 18500.00, 'DeAcero'),
('VAR-002', 'Varilla #4', 'Varilla corrugada #4 (1/2")', 'Ton', 18200.00, 'DeAcero'),
('VAR-003', 'Varilla #5', 'Varilla corrugada #5 (5/8")', 'Ton', 18000.00, 'DeAcero'),
('BLO-001', 'Block 15x20x40', 'Block hueco de concreto 15x20x40 cm', 'Pza', 18.50, 'Blocks del Valle'),
('TAB-001', 'Tabique rojo recocido', 'Tabique rojo recocido 7x14x28 cm', 'Millar', 450.00, 'Ladrillera San José'),
('CAL-001', 'Cal hidratada', 'Cal hidratada tipo N', 'Ton', 3200.00, 'Cal Mexicana'),
('YES-001', 'Yeso', 'Yeso para construcción', 'Ton', 2100.00, 'Yesos Monterrey');

-- Insertar mano de obra
INSERT INTO mano_obra (codigo, nombre, descripcion, salario_base, prestaciones_porcentaje) VALUES
('PEO-001', 'Peón', 'Ayudante general de construcción', 350.00, 35.00),
('ALB-001', 'Albañil', 'Oficial albañil especializado', 450.00, 35.00),
('CAR-001', 'Carpintero', 'Oficial carpintero', 480.00, 35.00),
('FIE-001', 'Fierrero', 'Oficial fierrero armador', 470.00, 35.00),
('ELE-001', 'Electricista', 'Oficial electricista', 520.00, 35.00),
('PLO-001', 'Plomero', 'Oficial plomero', 500.00, 35.00),
('OPE-001', 'Operador', 'Operador de equipo pesado', 550.00, 35.00),
('SOB-001', 'Sobrestante', 'Sobrestante de obra', 600.00, 35.00);

-- Insertar maquinaria
INSERT INTO maquinaria (codigo, nombre, descripcion, costo_hora, costo_operacion_hora) VALUES
('EXC-001', 'Excavadora', 'Excavadora hidráulica 20-25 ton', 850.00, 120.00),
('COM-001', 'Compresor', 'Compresor portátil 185 CFM', 180.00, 35.00),
('VIB-001', 'Vibrador', 'Vibrador para concreto', 45.00, 8.00),
('REV-001', 'Revolvedora', 'Revolvedora de concreto 1 saco', 85.00, 15.00),
('MON-001', 'Montacargas', 'Montacargas telescópico', 420.00, 65.00),
('GRU-001', 'Grúa', 'Grúa móvil 25 ton', 1200.00, 180.00),
('CAM-001', 'Camión volteo', 'Camión volteo 7 m3', 320.00, 80.00),
('BOM-001', 'Bomba concreto', 'Bomba estacionaria de concreto', 380.00, 55.00);

-- Insertar proveedores
INSERT INTO proveedores (razon_social, nombre_comercial, rfc, email, telefono, direccion, ciudad, estado, contacto_principal) VALUES
('CEMEX México S.A. de C.V.', 'CEMEX', 'CME000101AAA', 'ventas@cemex.com', '81-8888-8888', 'Av. Constitución 444 Pte.', 'Monterrey', 'Nuevo León', 'Carlos Hernández'),
('Deacero S.A.P.I. de C.V.', 'DeAcero', 'DEA000101BBB', 'comercial@deacero.com', '81-7777-7777', 'Av. del Acero 100', 'Monterrey', 'Nuevo León', 'Ana Rodríguez'),
('Agregados del Centro S.A.', 'Agregados del Centro', 'ADC000101CCC', 'ventas@agregados.com', '55-5555-5555', 'Carretera México-Querétaro Km 45', 'Tepotzotlán', 'Estado de México', 'Roberto Sánchez'),
('Blocks del Valle S.A.', 'Blocks del Valle', 'BDV000101DDD', 'info@blocksvalle.com', '55-4444-4444', 'Av. Industrial 200', 'Tlalnepantla', 'Estado de México', 'María González');

-- Insertar obra de ejemplo
INSERT INTO obras (nombre, descripcion, ubicacion, cliente, contratista, presupuesto_inicial, fecha_inicio, fecha_fin_programada, usuario_responsable_id) VALUES
('Edificio Corporativo Plaza Norte', 'Construcción de edificio corporativo de 8 niveles con estacionamiento subterráneo', 'Av. Insurgentes Norte 1500, Ciudad de México', 'Inmobiliaria Plaza Norte S.A.', 'Constructora Moderna S.A. de C.V.', 25000000.00, '2024-02-01', '2025-08-31', 2);

-- Insertar conceptos de ejemplo para la obra
INSERT INTO conceptos (obra_id, categoria_id, codigo, nombre, unidad, cantidad, precio_unitario, fecha_inicio, fecha_fin) VALUES
(1, 1, 'PREL-001', 'Limpieza y trazo del terreno', 'M2', 2500.00, 8.50, '2024-02-01', '2024-02-15'),
(1, 1, 'PREL-002', 'Excavación para cimentación', 'M3', 1800.00, 145.00, '2024-02-16', '2024-03-15'),
(1, 2, 'CIM-001', 'Zapatas de cimentación', 'M3', 120.00, 2850.00, '2024-03-16', '2024-04-30'),
(1, 2, 'CIM-002', 'Contratrabes de cimentación', 'M3', 85.00, 3200.00, '2024-04-15', '2024-05-15'),
(1, 3, 'EST-001', 'Columnas de concreto armado', 'M3', 180.00, 3800.00, '2024-05-01', '2024-07-31'),
(1, 3, 'EST-002', 'Losas de entrepiso', 'M2', 6000.00, 450.00, '2024-06-01', '2024-08-31'),
(1, 4, 'ALB-001', 'Muros de block', 'M2', 4500.00, 185.00, '2024-07-01', '2024-10-31'),
(1, 6, 'ACAB-001', 'Acabado en muros interiores', 'M2', 8000.00, 95.00, '2024-09-01', '2024-12-15');

-- Insertar algunos análisis de precios básicos
INSERT INTO analisis_precios (concepto_id, tipo_recurso, recurso_id, descripcion, unidad, cantidad, precio_unitario) VALUES
-- Para zapatas de cimentación (concepto_id = 3)
(3, 'material', 1, 'Cemento Portland', 'Ton', 0.350, 2800.00),
(3, 'material', 2, 'Arena', 'M3', 0.650, 350.00),
(3, 'material', 3, 'Grava', 'M3', 0.980, 420.00),
(3, 'material', 4, 'Varilla #4', 'Ton', 0.085, 18200.00),
(3, 'mano_obra', 1, 'Peón', 'Jor', 1.200, 473.25),
(3, 'mano_obra', 2, 'Albañil', 'Jor', 0.800, 607.50),
(3, 'mano_obra', 4, 'Fierrero', 'Jor', 0.600, 634.50),
(3, 'maquinaria', 4, 'Revolvedora', 'Hr', 2.500, 100.00),
(3, 'maquinaria', 3, 'Vibrador', 'Hr', 1.000, 53.00);

-- Insertar programa de obra básico (primeros 6 meses)
INSERT INTO programa_obra (obra_id, concepto_id, periodo, tipo_periodo, fecha_inicio, fecha_fin, cantidad_programada) VALUES
-- Limpieza y trazo (concepto 1)
(1, 1, 1, 'mensual', '2024-02-01', '2024-02-29', 2500.00),
-- Excavación (concepto 2)
(1, 2, 2, 'mensual', '2024-02-01', '2024-02-29', 900.00),
(1, 2, 3, 'mensual', '2024-03-01', '2024-03-31', 900.00),
-- Zapatas (concepto 3)
(1, 3, 3, 'mensual', '2024-03-01', '2024-03-31', 60.00),
(1, 3, 4, 'mensual', '2024-04-01', '2024-04-30', 60.00),
-- Contratrabes (concepto 4)
(1, 4, 4, 'mensual', '2024-04-01', '2024-04-30', 42.50),
(1, 4, 5, 'mensual', '2024-05-01', '2024-05-31', 42.50),
-- Columnas (concepto 5)
(1, 5, 5, 'mensual', '2024-05-01', '2024-05-31', 60.00),
(1, 5, 6, 'mensual', '2024-06-01', '2024-06-30', 60.00),
(1, 5, 7, 'mensual', '2024-07-01', '2024-07-31', 60.00);

-- Actualizar algunos avances de ejemplo
UPDATE programa_obra SET cantidad_ejecutada = cantidad_programada WHERE periodo <= 2;
UPDATE programa_obra SET cantidad_ejecutada = cantidad_programada * 0.75 WHERE periodo = 3;
UPDATE programa_obra SET cantidad_ejecutada = cantidad_programada * 0.40 WHERE periodo = 4;

-- Actualizar avances en conceptos
UPDATE conceptos SET avance_cantidad = 2500.00 WHERE id = 1;
UPDATE conceptos SET avance_cantidad = 1800.00 WHERE id = 2;
UPDATE conceptos SET avance_cantidad = 90.00 WHERE id = 3;
UPDATE conceptos SET avance_cantidad = 34.00 WHERE id = 4;