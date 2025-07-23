-- Mechanical FIX Database Schema
-- Sistema de Mecánicos a Domicilio

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

-- Database: mechanical_fix
CREATE DATABASE IF NOT EXISTS mechanical_fix CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE mechanical_fix;

-- Table: users
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'coordinator', 'mechanic', 'client') NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    city VARCHAR(100),
    state VARCHAR(100),
    zip_code VARCHAR(20),
    is_active BOOLEAN DEFAULT TRUE,
    email_verified BOOLEAN DEFAULT FALSE,
    email_verification_token VARCHAR(255),
    password_reset_token VARCHAR(255),
    password_reset_expires TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table: service_types
CREATE TABLE service_types (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    base_price DECIMAL(10,2),
    estimated_duration INT, -- in minutes
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table: vehicles
CREATE TABLE vehicles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    client_id INT,
    make VARCHAR(50) NOT NULL,
    model VARCHAR(50) NOT NULL,
    year INT NOT NULL,
    license_plate VARCHAR(20),
    vin VARCHAR(50),
    color VARCHAR(30),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Table: service_requests
CREATE TABLE service_requests (
    id INT PRIMARY KEY AUTO_INCREMENT,
    folio VARCHAR(20) UNIQUE NOT NULL,
    client_id INT,
    vehicle_id INT,
    service_type_id INT,
    status ENUM('nueva', 'en_proceso', 'asignada', 'en_camino', 'en_servicio', 'finalizada', 'cancelada') DEFAULT 'nueva',
    priority ENUM('baja', 'media', 'alta', 'urgente') DEFAULT 'media',
    problem_description TEXT NOT NULL,
    location_address TEXT NOT NULL,
    location_latitude DECIMAL(10, 8),
    location_longitude DECIMAL(11, 8),
    preferred_date DATE,
    preferred_time TIME,
    estimated_cost DECIMAL(10,2),
    final_cost DECIMAL(10,2),
    mechanic_id INT NULL,
    coordinator_id INT NULL,
    assigned_at TIMESTAMP NULL,
    started_at TIMESTAMP NULL,
    completed_at TIMESTAMP NULL,
    cancelled_at TIMESTAMP NULL,
    cancellation_reason TEXT,
    client_notes TEXT,
    internal_notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES users(id),
    FOREIGN KEY (vehicle_id) REFERENCES vehicles(id),
    FOREIGN KEY (service_type_id) REFERENCES service_types(id),
    FOREIGN KEY (mechanic_id) REFERENCES users(id),
    FOREIGN KEY (coordinator_id) REFERENCES users(id)
);

-- Table: service_files
CREATE TABLE service_files (
    id INT PRIMARY KEY AUTO_INCREMENT,
    service_request_id INT,
    file_type ENUM('image', 'video', 'document') NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_size INT,
    uploaded_by INT,
    upload_stage ENUM('initial', 'diagnostic', 'progress', 'completion') DEFAULT 'initial',
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (service_request_id) REFERENCES service_requests(id) ON DELETE CASCADE,
    FOREIGN KEY (uploaded_by) REFERENCES users(id)
);

-- Table: mechanic_assignments
CREATE TABLE mechanic_assignments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    service_request_id INT,
    mechanic_id INT,
    assigned_by INT,
    assignment_type ENUM('manual', 'automatic') DEFAULT 'manual',
    status ENUM('assigned', 'accepted', 'rejected', 'completed') DEFAULT 'assigned',
    assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    responded_at TIMESTAMP NULL,
    notes TEXT,
    FOREIGN KEY (service_request_id) REFERENCES service_requests(id) ON DELETE CASCADE,
    FOREIGN KEY (mechanic_id) REFERENCES users(id),
    FOREIGN KEY (assigned_by) REFERENCES users(id)
);

-- Table: mechanic_locations
CREATE TABLE mechanic_locations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    mechanic_id INT,
    service_request_id INT,
    latitude DECIMAL(10, 8) NOT NULL,
    longitude DECIMAL(11, 8) NOT NULL,
    location_type ENUM('check_in', 'check_out', 'progress') DEFAULT 'progress',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (mechanic_id) REFERENCES users(id),
    FOREIGN KEY (service_request_id) REFERENCES service_requests(id)
);

-- Table: service_reports
CREATE TABLE service_reports (
    id INT PRIMARY KEY AUTO_INCREMENT,
    service_request_id INT,
    mechanic_id INT,
    work_performed TEXT NOT NULL,
    parts_used TEXT,
    parts_cost DECIMAL(10,2) DEFAULT 0,
    labor_cost DECIMAL(10,2) DEFAULT 0,
    additional_costs DECIMAL(10,2) DEFAULT 0,
    total_cost DECIMAL(10,2) NOT NULL,
    work_duration INT, -- in minutes
    client_signature TEXT, -- base64 encoded signature
    mechanic_notes TEXT,
    before_photos TEXT, -- JSON array of photo paths
    after_photos TEXT, -- JSON array of photo paths
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (service_request_id) REFERENCES service_requests(id),
    FOREIGN KEY (mechanic_id) REFERENCES users(id)
);

-- Table: quotations
CREATE TABLE quotations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    service_request_id INT,
    created_by INT,
    quotation_number VARCHAR(20) UNIQUE NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    tax_amount DECIMAL(10,2) DEFAULT 0,
    total_amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'approved', 'rejected', 'expired') DEFAULT 'pending',
    valid_until DATE,
    notes TEXT,
    client_approved_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (service_request_id) REFERENCES service_requests(id),
    FOREIGN KEY (created_by) REFERENCES users(id)
);

-- Table: quotation_items
CREATE TABLE quotation_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    quotation_id INT,
    item_type ENUM('part', 'labor', 'other') NOT NULL,
    description VARCHAR(255) NOT NULL,
    quantity DECIMAL(8,2) NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    notes TEXT,
    FOREIGN KEY (quotation_id) REFERENCES quotations(id) ON DELETE CASCADE
);

-- Table: payments
CREATE TABLE payments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    service_request_id INT,
    quotation_id INT,
    payment_type ENUM('advance', 'partial', 'full') NOT NULL,
    payment_method ENUM('cash', 'card', 'transfer', 'online') NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    transaction_id VARCHAR(100),
    payment_gateway VARCHAR(50),
    status ENUM('pending', 'completed', 'failed', 'refunded') DEFAULT 'pending',
    processed_by INT,
    notes TEXT,
    processed_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (service_request_id) REFERENCES service_requests(id),
    FOREIGN KEY (quotation_id) REFERENCES quotations(id),
    FOREIGN KEY (processed_by) REFERENCES users(id)
);

-- Table: client_ratings
CREATE TABLE client_ratings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    service_request_id INT,
    client_id INT,
    mechanic_id INT,
    rating INT CHECK (rating >= 1 AND rating <= 5),
    review TEXT,
    service_quality_rating INT CHECK (service_quality_rating >= 1 AND service_quality_rating <= 5),
    punctuality_rating INT CHECK (punctuality_rating >= 1 AND punctuality_rating <= 5),
    professionalism_rating INT CHECK (professionalism_rating >= 1 AND professionalism_rating <= 5),
    would_recommend BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (service_request_id) REFERENCES service_requests(id),
    FOREIGN KEY (client_id) REFERENCES users(id),
    FOREIGN KEY (mechanic_id) REFERENCES users(id)
);

-- Table: notifications
CREATE TABLE notifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    type ENUM('email', 'sms', 'push', 'system') NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    status ENUM('pending', 'sent', 'failed') DEFAULT 'pending',
    read_at TIMESTAMP NULL,
    sent_at TIMESTAMP NULL,
    metadata JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Table: activity_logs
CREATE TABLE activity_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    action VARCHAR(100) NOT NULL,
    entity_type VARCHAR(50),
    entity_id INT,
    description TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Insert default service types
INSERT INTO service_types (name, description, base_price, estimated_duration) VALUES
('Cambio de aceite', 'Cambio de aceite del motor y filtro', 500.00, 60),
('Revisión de frenos', 'Inspección y mantenimiento del sistema de frenos', 800.00, 90),
('Cambio de llantas', 'Montaje y balanceo de llantas', 300.00, 45),
('Revisión eléctrica', 'Diagnóstico del sistema eléctrico del vehículo', 600.00, 120),
('Cambio de batería', 'Sustitución de batería del vehículo', 400.00, 30),
('Diagnóstico general', 'Revisión completa del vehículo', 1000.00, 180),
('Reparación de motor', 'Reparaciones varias del motor', 1500.00, 240),
('Sistema de enfriamiento', 'Mantenimiento del sistema de enfriamiento', 700.00, 90);

-- Insert default admin user (password: admin123)
INSERT INTO users (email, password, role, first_name, last_name, phone, is_active, email_verified) VALUES
('admin@mechanicalfix.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'Administrador', 'Sistema', '555-0000', TRUE, TRUE);

COMMIT;