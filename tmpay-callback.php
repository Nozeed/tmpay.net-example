<?php
require __DIR__ . '/db.php';

$transaction_id = $_GET['transaction_id'] ?? '';
$password       = $_GET['password'] ?? '';            // รหัสบัตร (card_code)
$amount_raw     = (float)($_GET['real_amount'] ?? 0); // จำนวนเงินที่เติม (ดิบ)
$status         = (int)($_GET['status'] ?? 0);        // 1 = สำเร็จ

if ($status === 1) {
    $stmt = $pdo->prepare("SELECT user_id FROM refill_log WHERE transaction_id = ? AND card_code = ? AND status = 0");
    $stmt->execute([$transaction_id, $password]);
    $log = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($log) {
        $amount = (int)floor($amount_raw); // ปัดเศษจำนวนเงินที่เติม
        // เพิ่มเครดิตให้ผู้ใช้
        $pdo->prepare("UPDATE users SET credit = credit + ? WHERE id = ?")
            ->execute([$amount, (int)$log['user_id']]);

        // อัปเดตประวัติการเติมเงิน
        $pdo->prepare("UPDATE refill_log SET amount = ?, status = 1 WHERE transaction_id = ?")
            ->execute([$amount_raw, $transaction_id]);

        die("SUCCEED|TOPPED_UP_" . $amount);
    }
} else {
    // เติมเงินไม่สำเร็จ
    $pdo->prepare("UPDATE refill_log SET status = 2 WHERE transaction_id = ? AND card_code = ?")
        ->execute([$transaction_id, $password]);
    die("ERROR|STATUS_" . $status);
}

// หากไม่พบ log ให้ตอบ SUCCEED เพื่อป้องกัน TMPAY เรียก callback ซ้ำ
die("SUCCEED|LOG_NOT_FOUND");