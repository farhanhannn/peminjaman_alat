-- Database: peminjaman_alat
-- Target: MySQL 8.0+
-- File ini melengkapi migration Laravel dengan procedure, function, trigger,
-- operasi relasional, dan contoh transaksi COMMIT/ROLLBACK sesuai tugas.

CREATE DATABASE IF NOT EXISTS peminjaman_alat CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE peminjaman_alat;

-- Struktur tabel utama untuk deployment MySQL.
CREATE TABLE IF NOT EXISTS users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL UNIQUE,
    role VARCHAR(20) NOT NULL DEFAULT 'peminjam', is_active TINYINT(1) NOT NULL DEFAULT 1,
    email_verified_at TIMESTAMP NULL, password VARCHAR(255) NOT NULL, remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS categories (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, name VARCHAR(255) NOT NULL UNIQUE, description TEXT NULL,
    created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS tools (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, category_id BIGINT UNSIGNED NOT NULL, code VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(255) NOT NULL, description TEXT NULL, stock INT UNSIGNED NOT NULL DEFAULT 0, available_stock INT UNSIGNED NOT NULL DEFAULT 0,
    `condition` VARCHAR(20) NOT NULL DEFAULT 'baik', daily_fine DECIMAL(12,2) NOT NULL DEFAULT 5000, is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL,
    CONSTRAINT fk_tools_category FOREIGN KEY(category_id) REFERENCES categories(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS loans (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, loan_code VARCHAR(100) NOT NULL UNIQUE, borrower_id BIGINT UNSIGNED NOT NULL,
    tool_id BIGINT UNSIGNED NOT NULL, quantity INT UNSIGNED NOT NULL DEFAULT 1, loan_date DATE NOT NULL, due_date DATE NOT NULL, return_date DATE NULL,
    approved_by BIGINT UNSIGNED NULL, returned_by BIGINT UNSIGNED NULL, status VARCHAR(20) NOT NULL DEFAULT 'pending', fine DECIMAL(12,2) NOT NULL DEFAULT 0,
    notes TEXT NULL, created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL,
    CONSTRAINT fk_loans_borrower FOREIGN KEY(borrower_id) REFERENCES users(id) ON DELETE RESTRICT,
    CONSTRAINT fk_loans_tool FOREIGN KEY(tool_id) REFERENCES tools(id) ON DELETE RESTRICT,
    CONSTRAINT fk_loans_approver FOREIGN KEY(approved_by) REFERENCES users(id) ON DELETE SET NULL,
    CONSTRAINT fk_loans_returner FOREIGN KEY(returned_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS activity_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, user_id BIGINT UNSIGNED NULL, module VARCHAR(50) NOT NULL, action VARCHAR(50) NOT NULL,
    description TEXT NOT NULL, ip_address VARCHAR(45) NULL, user_agent TEXT NULL, created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL,
    CONSTRAINT fk_logs_user FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Operasi relasional contoh
SELECT u.name AS peminjam, t.name AS alat, l.status, l.due_date
FROM loans l
JOIN users u ON u.id = l.borrower_id
JOIN tools t ON t.id = l.tool_id
WHERE l.status IN ('pending', 'borrowed', 'overdue')
ORDER BY l.due_date ASC;

-- Function menghitung denda berdasarkan keterlambatan.
DELIMITER $$
DROP FUNCTION IF EXISTS hitung_denda $$
CREATE FUNCTION hitung_denda(p_loan_id BIGINT)
RETURNS DECIMAL(12,2)
READS SQL DATA
BEGIN
    DECLARE v_days INT DEFAULT 0;
    DECLARE v_fine DECIMAL(12,2) DEFAULT 0;
    SELECT GREATEST(DATEDIFF(COALESCE(return_date, CURDATE()), due_date), 0) * t.daily_fine * l.quantity
      INTO v_fine
      FROM loans l JOIN tools t ON t.id = l.tool_id
     WHERE l.id = p_loan_id;
    RETURN COALESCE(v_fine, 0);
END $$

-- Procedure persetujuan peminjaman.
DROP PROCEDURE IF EXISTS setujui_peminjaman $$
CREATE PROCEDURE setujui_peminjaman(IN p_loan_id BIGINT, IN p_user_id BIGINT)
BEGIN
    DECLARE v_tool_id BIGINT;
    DECLARE v_qty INT;
    DECLARE v_available INT;
    SELECT tool_id, quantity INTO v_tool_id, v_qty FROM loans WHERE id=p_loan_id AND status='pending' FOR UPDATE;
    SELECT available_stock INTO v_available FROM tools WHERE id=v_tool_id FOR UPDATE;
    IF v_available >= v_qty THEN
        UPDATE tools SET available_stock = available_stock - v_qty WHERE id=v_tool_id;
        UPDATE loans SET status='borrowed', approved_by=p_user_id WHERE id=p_loan_id;
    ELSE
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Stok alat tidak mencukupi';
    END IF;
END $$

-- Trigger validasi dan sinkronisasi status keterlambatan.
DROP TRIGGER IF EXISTS trg_loans_before_insert $$
CREATE TRIGGER trg_loans_before_insert BEFORE INSERT ON loans
FOR EACH ROW
BEGIN
    IF NEW.quantity < 1 THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Jumlah harus minimal 1'; END IF;
    IF NEW.due_date < NEW.loan_date THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Tanggal jatuh tempo tidak boleh sebelum tanggal pinjam'; END IF;
END $$

DROP TRIGGER IF EXISTS trg_loans_before_update $$
CREATE TRIGGER trg_loans_before_update BEFORE UPDATE ON loans
FOR EACH ROW
BEGIN
    IF NEW.quantity < 1 THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Jumlah harus minimal 1'; END IF;
    IF NEW.due_date < NEW.loan_date THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Tanggal jatuh tempo tidak boleh sebelum tanggal pinjam'; END IF;
END $$
DELIMITER ;

-- Contoh transaksi COMMIT.
START TRANSACTION;
UPDATE tools SET description = CONCAT(COALESCE(description,''), ' | diperbarui') WHERE code='ALT-001';
COMMIT;

-- Contoh transaksi ROLLBACK (tidak dijalankan sebagai bagian transaksi di atas):
-- START TRANSACTION;
-- UPDATE tools SET stock = stock + 1 WHERE code='ALT-001';
-- ROLLBACK;
