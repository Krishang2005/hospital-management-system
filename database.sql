CREATE DATABASE IF NOT EXISTS hospital_system;
USE hospital_system;

CREATE TABLE specializations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    specialization_name VARCHAR(255) NOT NULL UNIQUE
);

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    phone VARCHAR(20) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('patient', 'admin') DEFAULT 'patient',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE doctors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    doctor_name VARCHAR(255) NOT NULL,
    specialization VARCHAR(255) NOT NULL,
    experience INT NOT NULL,
    qualification VARCHAR(255) NOT NULL,
    hospital VARCHAR(255) NOT NULL,
    available_days VARCHAR(255) NOT NULL,
    available_time VARCHAR(255) NOT NULL,
    status ENUM('Available', 'Busy') DEFAULT 'Available',
    photo VARCHAR(255) DEFAULT 'default_doctor.png'
);

CREATE TABLE appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    doctor_id INT NOT NULL,
    appointment_date DATE NOT NULL,
    appointment_time VARCHAR(50) NOT NULL,
    status ENUM('Pending', 'Confirmed', 'Completed', 'Cancelled', 'Ongoing', 'No-Show') DEFAULT 'Pending',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE
);

-- Insert Default Admin
INSERT INTO users (name, email, phone, password, role) 
VALUES ('System Admin', 'admin@hospital.com', '1234567890', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');
-- Password is 'password'

INSERT INTO specializations (specialization_name) VALUES 
('Cardiologist'),
('Dentist'),
('Dermatologist'),
('Neurologist'),
('Orthopedic'),
('Pediatrician');

CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    message TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
