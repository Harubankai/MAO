-- Run this to add the password reset tokens table
USE moncada_agri;

CREATE TABLE IF NOT EXISTS password_resets (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id     INT UNSIGNED NOT NULL,
  token       VARCHAR(64) NOT NULL UNIQUE,
  expires_at  DATETIME NOT NULL,
  used        TINYINT(1) NOT NULL DEFAULT 0,
  created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_pr_token   (token),
  INDEX idx_pr_user    (user_id),
  CONSTRAINT fk_pr_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;
