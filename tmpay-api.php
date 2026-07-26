<?php
require __DIR__ . '/db.php';
// TMPAY.net API
/**
 * ระบบเติมเงินบัตร TrueMoney และ razer gold pin
 */
function refill_tmpay($data, $user_id, $channel)
{
    global $pdo, $merchant_id, $callback_url;
    $card_code = (string)($data['card_code'] ?? '');
    if (!preg_match('/^\d{14}$/', $card_code)) {
        return 'รหัสบัตรต้องเป็นตัวเลข 14 หลัก';
    }

    // ตรวจสอบว่าบัตรนี้ถูกใช้ไปแล้วหรือไม่
    $check_stmt = $pdo->prepare("SELECT id FROM refill_log WHERE card_code = ? AND pay_type = ? AND status IN (1, 3) LIMIT 1");
    $check_stmt->execute([$card_code, $channel]);
    if ($check_stmt->fetch()) {
        return 'บัตรนี้ถูกใช้ไปแล้ว หรืออยู่ระหว่างการตรวจสอบ';
    }

    $url = "https://www.tmpay.net/TPG/backend.php?" . http_build_query([
        'merchant_id' => $merchant_id,
        'password'    => $card_code,
        'resp_url'    => $callback_url,
        'channel'     => $channel // 'truemoney' หรือ 'razer_gold_pin'
    ]);

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_TIMEOUT        => 15
    ]);
    $response = curl_exec($ch);
    curl_close($ch);

    if (strpos($response, 'SUCCEED') !== false) {
        $trans_id = substr($response, 8);
        $stmt = $pdo->prepare("INSERT INTO refill_log (user_id, card_code, transaction_id, status, pay_type) VALUES (?, ?, ?, 0, ?)");
        $stmt->execute([(int)$user_id, $card_code, $trans_id, $channel]);

        return true;
    }

    return 'เกิดข้อผิดพลาด: ' . htmlspecialchars((string)$response);
}