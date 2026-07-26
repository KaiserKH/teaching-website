-- Migration: add updated_at to payments and create payment_audit table
SET FOREIGN_KEY_CHECKS=0;
-- Add updated_at column if not exists (MySQL 8+ supports IF NOT EXISTS)
ALTER TABLE payments ADD COLUMN IF NOT EXISTS updated_at TIMESTAMP NULL;

-- Create audit table if not exists
CREATE TABLE IF NOT EXISTS payment_audit (
  id INT AUTO_INCREMENT PRIMARY KEY,
  payment_id INT NOT NULL,
  admin_id INT NULL,
  action VARCHAR(50) NOT NULL,
  note TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (payment_id) REFERENCES payments(id) ON DELETE CASCADE,
  FOREIGN KEY (admin_id) REFERENCES admin(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

SET FOREIGN_KEY_CHECKS=1;

-- If your MySQL version does not support ALTER ... ADD COLUMN IF NOT EXISTS,
-- run the following instead (uncomment and execute manually):
-- ALTER TABLE payments ADD COLUMN updated_at TIMESTAMP NULL;
