-- Farmer registration + admin approval workflow. Run AFTER farmers.sql. Safe to re-run (MariaDB).
USE moncada_agri;

-- Existing rows keep status 'active' (= Approved). New records start as 'pending'.
-- Registration fields are NULL-able so older records still load; the form requires them.
ALTER TABLE farmers
  MODIFY address VARCHAR(255) NULL,
  MODIFY status ENUM('pending','active','rejected') NOT NULL DEFAULT 'pending',
  ADD COLUMN IF NOT EXISTS birthday DATE NULL AFTER full_name,
  ADD COLUMN IF NOT EXISTS id_number VARCHAR(50) NULL AFTER birthday,
  ADD COLUMN IF NOT EXISTS total_farm_area_ha DECIMAL(8,2) NULL AFTER gender,
  ADD COLUMN IF NOT EXISTS insured_area_ha DECIMAL(8,2) NULL AFTER total_farm_area_ha,
  ADD COLUMN IF NOT EXISTS crop_type VARCHAR(80) NULL AFTER insured_area_ha,
  ADD COLUMN IF NOT EXISTS farm_location VARCHAR(255) NULL AFTER crop_type,
  ADD COLUMN IF NOT EXISTS barangay VARCHAR(80) NULL AFTER farm_location,
  ADD COLUMN IF NOT EXISTS farm_ownership ENUM('Owner','Tenant','Lessee','Caretaker') NULL AFTER barangay,
  ADD COLUMN IF NOT EXISTS rejection_reason VARCHAR(500) NULL AFTER status,
  ADD COLUMN IF NOT EXISTS encoded_by INT UNSIGNED NULL AFTER rejection_reason,
  ADD COLUMN IF NOT EXISTS submitted_at DATETIME NULL AFTER encoded_by,
  ADD COLUMN IF NOT EXISTS reviewed_by INT UNSIGNED NULL AFTER submitted_at,
  ADD COLUMN IF NOT EXISTS reviewed_at DATETIME NULL AFTER reviewed_by,
  ADD UNIQUE INDEX IF NOT EXISTS uq_farmer_id_number (id_number),
  ADD INDEX IF NOT EXISTS idx_farmer_status (status);

-- Audit trail of every submission and admin decision.
CREATE TABLE IF NOT EXISTS farmer_approvals (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  farmer_id INT UNSIGNED NOT NULL,
  action ENUM('Submitted','Resubmitted','Approved','Rejected') NOT NULL,
  remarks VARCHAR(500) NULL,
  acted_by INT UNSIGNED NULL,
  acted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_fa_farmer (farmer_id),
  CONSTRAINT fk_fa_farmer FOREIGN KEY (farmer_id) REFERENCES farmers(id) ON DELETE CASCADE
) ENGINE=InnoDB;
