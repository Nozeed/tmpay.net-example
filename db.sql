-- Database Schema for TMPAY Payment System
-- สร้างฐานข้อมูล tmpay

CREATE DATABASE IF NOT EXISTS tmpay DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE tmpay;

-- ตาราง users: เก็บข้อมูลผู้ใช้และเครดิต
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    credit INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ตาราง refill_log: เก็บประวัติการเติมเงิน
CREATE TABLE IF NOT EXISTS refill_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    card_code VARCHAR(14) NOT NULL,
    transaction_id VARCHAR(50) NOT NULL,
    amount DECIMAL(10, 2) DEFAULT 0,
    status TINYINT DEFAULT 0 COMMENT '0 = รอตรวจสอบ, 1 = สำเร็จ, 2 = ไม่สำเร็จ, 3 = รอตรวจสอบ',
    pay_type VARCHAR(20) NOT NULL COMMENT 'truemoney หรือ razer_gold_pin',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Index สำหรับการค้นหาที่รวดเร็ว
CREATE INDEX idx_card_code ON refill_log(card_code);
CREATE INDEX idx_transaction_id ON refill_log(transaction_id);
CREATE INDEX idx_user_id ON refill_log(user_id);

-- ข้อมูลตัวอย่าง
INSERT INTO users (id, username, credit) VALUES
(1, 'test', 0),
(2, 'test2', 500),
(3, 'test3', 1200);

INSERT INTO refill_log (user_id, card_code, transaction_id, amount, status, pay_type) VALUES
(1, '12345678901234', 'TX10001', 50.00, 1, 'truemoney'),
(1, '99999999999999', 'TX10002', 100.00, 2, 'truemoney'),
(2, '88888888888888', 'TX10003', 500.00, 1, 'razer_gold_pin');
