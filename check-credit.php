<?php
require __DIR__ . '/db.php';

$user_id = '1';

// ดึงข้อมูลเครดิตของผู้ใช้
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// ถ้าไม่มีผู้ใช้ให้สร้างใหม่
if (!$user) {
    $stmt = $pdo->prepare("INSERT INTO users (id, username, credit) VALUES (?, ?, 0)");
    $stmt->execute([$user_id, 'test']);
    $user = [
        'id' => $user_id,
        'username' => 'test',
        'credit' => 0
    ];
}

// ดึงประวัติการเติมเงิน
$stmt = $pdo->prepare("SELECT * FROM refill_log WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$transactions = $stmt->fetchAll();

// แปลงสถานะเป็นภาษาไทย
function getStatusText($status) {
    switch ($status) {
        case 0: return 'รอตรวจสอบ';
        case 1: return 'สำเร็จ';
        case 2: return 'ไม่สำเร็จ';
        case 3: return 'รอตรวจสอบ';
        default: return 'ไม่ระบุ';
    }
}

function getStatusColor($status) {
    switch ($status) {
        case 0: return 'orange';
        case 1: return 'green';
        case 2: return 'red';
        case 3: return 'orange';
        default: return 'gray';
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ดูเครดิตและประวัติการเติมเงิน</title>
</head>
<body>
    <h1>ดูเครดิตและประวัติการเติมเงิน</h1>
    
    <h2>ข้อมูลผู้ใช้</h2>
    <p>
        <strong>User ID:</strong> <?php echo htmlspecialchars($user['id']); ?><br>
        <strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?><br>
        <strong>เครดิต:</strong> <?php echo number_format($user['credit']); ?> บาท
    </p>
    
    <hr>
    
    <h2>ประวัติการเติมเงิน</h2>
    
    <?php if (empty($transactions)): ?>
        <p>ยังไม่มีประวัติการเติมเงิน</p>
    <?php else: ?>
        <table border="1" cellpadding="10" cellspacing="0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>รหัสบัตร</th>
                    <th>Transaction ID</th>
                    <th>จำนวนเงิน</th>
                    <th>สถานะ</th>
                    <th>ช่องทาง</th>
                    <th>วันที่</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($transactions as $tx): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($tx['id']); ?></td>
                        <td><?php echo htmlspecialchars(substr($tx['card_code'], 0, 4) . '********' . substr($tx['card_code'], -4)); ?></td>
                        <td><?php echo htmlspecialchars($tx['transaction_id']); ?></td>
                        <td><?php echo number_format($tx['amount'], 2); ?> บาท</td>
                        <td style="color: <?php echo getStatusColor($tx['status']); ?>;">
                            <?php echo getStatusText($tx['status']); ?>
                        </td>
                        <td><?php echo htmlspecialchars($tx['pay_type']); ?></td>
                        <td><?php echo htmlspecialchars($tx['created_at']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
    
    <hr>
    <p><a href="index.php">กลับหน้าเติมเงิน</a></p>
</body>
</html>
