<?php
require __DIR__ . '/db.php';
require __DIR__ . '/tmpay-api.php';

$message = ''; // ข้อความที่แสดงผลลัพธ์
$user_id = '1'; // รหัสผู้ใช้งานในฐานข้อมูล (สำหรับการทดสอบ)

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $card_code = $_POST['card_code'] ?? '';
    $channel = $_POST['channel'] ?? 'truemoney';
    
    $result = refill_tmpay(['card_code' => $card_code], $user_id, $channel);
    
    if ($result === true) {
        $message = 'ส่งข้อมูลบัตรเรียบร้อย กรุณารอการตรวจสอบ';
    } else {
        $message = $result;
    }
} else {
    $result = false;
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TMPAY เติมเงิน</title>
</head>
<body>
    <h1>ระบบเติมเงิน TMPAY</h1>
    
    <?php if ($message): ?>
        <p style="color: <?php echo $result === true ? 'green' : 'red'; ?>;">
            <?php echo htmlspecialchars($message); ?>
        </p>
    <?php endif; ?>
    
    <form method="POST">
        <p>
            <label>รหัสบัตร (14 หลัก):</label><br>
            <input type="text" name="card_code" maxlength="14" pattern="\d{14}" required placeholder="กรอกเลขบัตร 14 หลัก">
        </p>
        
        <fieldset>
            <legend>ช่องทาง:</legend>
            <p>
                <label>
                    <input type="radio" name="channel" value="truemoney" checked required>
                    บัตร TrueMoney
                </label>
            </p>
            <p>
                <label>
                    <input type="radio" name="channel" value="razer_gold_pin" required>
                    Razer Gold Pin
                </label>
            </p>
        </fieldset>
        
        <p>
            <input type="submit" value="เติมเงิน">
        </p>
    </form>
    
    <hr>
    <p><a href="check-credit.php">ดูเครดิตและประวัติการเติมเงิน</a></p>
</body>
</html>
