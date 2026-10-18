-- Run AFTER schema.sql. Adds the remaining tables for the full system.
USE moncada_agri;
CREATE TABLE IF NOT EXISTS farms (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  farmer_id INT UNSIGNED NOT NULL,
  farm_name VARCHAR(120) NOT NULL,
  barangay VARCHAR(80) NOT NULL,
  location VARCHAR(255) NULL,
  area_ha DECIMAL(8,2) NOT NULL,
  ownership ENUM('Owner','Tenant','Lessee','Caretaker') NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_farm_farmer (farmer_id),
  CONSTRAINT fk_farm_farmer FOREIGN KEY (farmer_id) REFERENCES farmers(id) ON DELETE RESTRICT
) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS crops (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  farm_id INT UNSIGNED NOT NULL,
  crop_type VARCHAR(80) NOT NULL,
  variety VARCHAR(80) NULL,
  season ENUM('Wet','Dry') NOT NULL,
  planting_date DATE NOT NULL,
  expected_harvest DATE NULL,
  area_planted_ha DECIMAL(8,2) NOT NULL,
  status ENUM('Planted','Harvested','Failed') NOT NULL DEFAULT 'Planted',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_crop_farm (farm_id), INDEX idx_crop_type (crop_type),
  CONSTRAINT fk_crop_farm FOREIGN KEY (farm_id) REFERENCES farms(id) ON DELETE RESTRICT
) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS applications (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  app_no VARCHAR(30) NOT NULL UNIQUE,
  farmer_id INT UNSIGNED NOT NULL,
  farm_id INT UNSIGNED NOT NULL,
  crop_id INT UNSIGNED NOT NULL,
  date_applied DATE NOT NULL,
  status ENUM('Submitted','Under Review','Verified','Approved','Returned') NOT NULL DEFAULT 'Submitted',
  remarks VARCHAR(500) NULL,
  created_by INT UNSIGNED NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_app_status (status), INDEX idx_app_date (date_applied),
  CONSTRAINT fk_app_farmer FOREIGN KEY (farmer_id) REFERENCES farmers(id) ON DELETE RESTRICT,
  CONSTRAINT fk_app_farm FOREIGN KEY (farm_id) REFERENCES farms(id) ON DELETE RESTRICT,
  CONSTRAINT fk_app_crop FOREIGN KEY (crop_id) REFERENCES crops(id) ON DELETE RESTRICT
) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS application_documents (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  application_id INT UNSIGNED NOT NULL,
  doc_type VARCHAR(100) NOT NULL,
  submitted_date DATE NOT NULL,
  status ENUM('Submitted','Verified','Rejected') NOT NULL DEFAULT 'Submitted',
  remarks VARCHAR(255) NULL,
  uploaded_by INT UNSIGNED NULL,
  CONSTRAINT fk_doc_app FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE CASCADE
) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS verification_records (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  application_id INT UNSIGNED NOT NULL,
  old_status VARCHAR(20) NULL,
  new_status VARCHAR(20) NOT NULL,
  remarks VARCHAR(500) NULL,
  changed_by INT UNSIGNED NULL,
  changed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_ver_app (application_id),
  CONSTRAINT fk_ver_app FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE CASCADE
) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS activity_logs (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NULL,
  action VARCHAR(80) NOT NULL,
  entity VARCHAR(40) NULL,
  entity_id INT UNSIGNED NULL,
  details VARCHAR(255) NULL,
  ip_address VARCHAR(45) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_log_time (created_at), INDEX idx_log_user (user_id)
) ENGINE=InnoDB;
-- Production tip: give the app's MySQL user only INSERT/SELECT on activity_logs so logs cannot be altered.

-- Demo staff account: staff / Staff@123 (change it)
INSERT INTO users (username, password_hash, full_name, role)
VALUES ('staff', '$2y$10$wAsCY6e/SOIqZ2Tkh9uEMOYmuiqTFVvsyNGnlCmjVgEqaSLUCJOPO', 'MAO Staff (demo)', 'staff') ON DUPLICATE KEY UPDATE username = username;

-- DEMO DATA (replace with real records)
INSERT INTO farms (farmer_id, farm_name, barangay, location, area_ha, ownership)
SELECT id, 'Demo rice field', 'Poblacion 1', 'Near barangay hall', 1.50, 'Owner' FROM farmers WHERE farmer_id='MON-2026-0001'
AND NOT EXISTS (SELECT 1 FROM farms);
INSERT INTO crops (farm_id, crop_type, variety, season, planting_date, area_planted_ha)
SELECT id, 'Rice', 'Demo variety', 'Wet', '2026-06-15', 1.50 FROM farms WHERE farm_name='Demo rice field'
AND NOT EXISTS (SELECT 1 FROM crops);
