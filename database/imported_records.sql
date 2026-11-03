-- Allows records imported from barangay lists, which have no gender, contact number or farm ownership.
-- Run AFTER farmer_approval.sql. Safe to re-run. The forms still require these fields when a record is edited.
USE moncada_agri;

ALTER TABLE farmers
  MODIFY contact_number VARCHAR(20) NULL,
  MODIFY gender ENUM('Male','Female','Other') NULL;

ALTER TABLE farms
  MODIFY ownership ENUM('Owner','Tenant','Lessee','Caretaker') NULL;
