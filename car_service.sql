CREATE DATABASE IF NOT EXISTS car_service;
USE car_service;

-- Customer
CREATE TABLE customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(25) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(55) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    city VARCHAR(55) NOT NULL,
    district VARCHAR(55) NOT NULL
);

-- Car Services
CREATE TABLE services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(25) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(55) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    city VARCHAR(55) NOT NULL,
    district VARCHAR(55) NOT NULL,
    address TEXT NOT NULL
);

-- Vehicle Types
CREATE TABLE vehicle_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type VARCHAR(50) NOT NULL
);

-- Service Vehicle Types
CREATE TABLE service_vehicle_types (
    service_id INT NOT NULL,
    vehicle_type_id INT NOT NULL,
    FOREIGN KEY (service_id) REFERENCES services(id),
    FOREIGN KEY (vehicle_type_id) REFERENCES vehicle_types(id),
    PRIMARY KEY (service_id, vehicle_type_id)
);

-- Working Hours
CREATE TABLE working_hours (
    id INT AUTO_INCREMENT PRIMARY KEY,
    service_id INT NOT NULL,
    day ENUM('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday') NOT NULL,
    start_hour TIME NOT NULL,
    end_hour TIME NOT NULL,
    FOREIGN KEY (service_id) REFERENCES services(id)
);

-- Appointments
CREATE TABLE appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    service_id INT NOT NULL,
    customer_id INT NOT NULL,
    date DATE NOT NULL,
    time TIME NOT NULL,
    vehicle_type_id INT NOT NULL,
    status ENUM('Beklemede', 'Onaylandı', 'Reddedildi', 'İptal edildi') DEFAULT 'Beklemede',
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (customer_id) REFERENCES customers(id),
    FOREIGN KEY (vehicle_type_id) REFERENCES vehicle_types(id)
);

-- Inserting vehicle types
INSERT INTO vehicle_types (type) VALUES ('Otomobil'), ('SUV'), ('Kamyonet');