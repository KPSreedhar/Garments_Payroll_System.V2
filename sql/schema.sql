-- Garment Payroll System — database schema
-- Import this once in phpMyAdmin (or `mysql -u root -p < schema.sql`)

CREATE DATABASE IF NOT EXISTS garment_payroll CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE garment_payroll;

-- ------------------------------------------------------
-- Admins (login accounts)
-- ------------------------------------------------------
CREATE TABLE IF NOT EXISTS admins (
  id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(191) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Default admin login:
--   email:    sreedhar@gmail.com
--   password: kpn1234
-- (This hash was generated with PHP's password_hash(); change the password
--  after your first login by updating this row or adding a change-password page.)
INSERT INTO admins (email, password_hash)
VALUES ('sreedhar@gmail.com', '$2y$10$Il0PmESMYE.XjzRh1n7H7eK9ukeqthFIVKZX5KGMS6ex2dABiDN3O')
ON DUPLICATE KEY UPDATE email = email;

-- ------------------------------------------------------
-- Workers
-- ------------------------------------------------------
CREATE TABLE IF NOT EXISTS workers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(191) NOT NULL,
  payment_type ENUM('shift','piece') NOT NULL,
  rate DECIMAL(10,2) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ------------------------------------------------------
-- Work entries (daily production/shift records)
-- ------------------------------------------------------
CREATE TABLE IF NOT EXISTS work_entries (
  id INT AUTO_INCREMENT PRIMARY KEY,
  worker_id INT NOT NULL,
  entry_date DATE NOT NULL,
  quantity INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (worker_id) REFERENCES workers(id) ON DELETE CASCADE,
  INDEX idx_entry_date (entry_date),
  INDEX idx_worker_id (worker_id)
) ENGINE=InnoDB;
