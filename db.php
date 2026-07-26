<?php
/**
 * Database Configuration
 * เชื่อมต่อฐานข้อมูลและตั้งค่า TMPAY
 */

// Database Configuration
$db_host = 'localhost';
$db_name = 'tmpay';
$db_user = 'root';
$db_pass = '';

// TMPAY Configuration
$merchant_id = 'YOUR_MERCHANT_ID'; // แก้ไขเป็น Merchant ID ของคุณ
$callback_url = 'http://localhost/tmpay-callback.php'; // แก้ไข URL ให้ตรงกับเซิร์ฟเวอร์ของคุณ

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
