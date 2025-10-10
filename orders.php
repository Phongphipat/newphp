<?php
session_start();
require 'config.php';
require_once 'function.php';

// ✅ ตรวจสอบล็อกอิน
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// ✅ ดึงคำสั่งซื้อของผู้ใช้
$stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>ประวัติการสั่งซื้อ</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;600&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>
    body {
        background: linear-gradient(135deg, #00b4db, #0083b0, #3a7bd5);
        background-size: 400% 400%;
        animation: bg-move 15s ease infinite;
        font-family: 'Kanit', sans-serif;
        min-height: 100vh;
        color: #0f172a;
        padding-bottom: 60px;
    }

    @keyframes bg-move {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }

    .container {
        max-width: 950px;
        margin-top: 60px;
    }

    .card {
        backdrop-filter: blur(10px);
        background: rgba(255, 255, 255, 0.93);
        border: none;
        border-radius: 20px;
        box-shadow: 0 10px 25px rgba(0, 123, 255, 0.25);
        transition: all 0.3s ease;
    }

    .card:hover {
        transform: translateY(-4px);
        box-shadow: 0 15px 35px rgba(0, 123, 255, 0.35);
    }

    .card-header {
        border-radius: 20px 20px 0 0 !important;
        background: linear-gradient(90deg, #2196f3, #00bfff);
        color: #fff;
        font-weight: 600;
        font-size: 1rem;
        padding: 15px 20px;
    }

    h2 {
        color: #fff;
        font-weight: 700;
        text-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
        margin-bottom: 24px;
    }

    .btn-secondary {
        background: linear-gradient(90deg, #6dd5fa, #2980b9);
        border: none;
        border-radius: 10px;
        color: #fff;
        font-weight: 500;
        box-shadow: 0 4px 12px rgba(41, 128, 185, 0.4);
        transition: all 0.3s ease;
    }

    .btn-secondary:hover {
        background: linear-gradient(90deg, #56ccf2, #2f80ed);
        box-shadow: 0 6px 16px rgba(47, 128, 237, 0.5);
        transform: translateY(-2px);
    }

    .alert {
        border-radius: 12px;
        font-weight: 500;
    }

    .list-group-item {
        border: none;
        border-radius: 12px;
        background: #f8fbff;
        margin-bottom: 8px;
    }

    .list-group-item strong {
        color: #1565c0;
    }

    p {
        margin-bottom: 0.5rem;
    }

    .order-total {
        font-size: 1.1rem;
        font-weight: 600;
        color: #0d47a1;
    }

    footer {
        text-align: center;
        color: rgba(255, 255, 255, 0.8);
        font-size: 0.9rem;
        margin-top: 40px;
    }
</style>
</head>
<body>
<div class="container">
    <h2><i class="bi bi-bag-check-fill me-2"></i>ประวัติการสั่งซื้อของคุณ</h2>
    <a href="index.php" class="btn btn-secondary mb-4">
        <i class="bi bi-arrow-left-circle me-1"></i> กลับหน้าหลัก
    </a>

    <?php if (isset($_GET['order_id'])): ?>
        <div class="alert alert-success">
            <i class="bi bi-check-circle-fill me-2"></i>ทำรายการสั่งซื้อเรียบร้อยแล้ว
        </div>
    <?php endif; ?>

    <?php if (count($orders) === 0): ?>
        <div class="alert alert-warning text-center py-3">
            <i class="bi bi-exclamation-triangle me-2"></i>คุณยังไม่เคยสั่งซื้อสินค้า
        </div>
    <?php else: ?>
        <?php foreach ($orders as $order): ?>
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-receipt-cutoff me-2"></i>
                    <strong>คำสั่งซื้อ #<?= $order['order_id'] ?></strong> |
                    วันที่: <?= date('d/m/Y H:i', strtotime($order['order_date'])) ?> |
                    สถานะ: <?= ucfirst($order['status']) ?>
                </div>
                <div class="card-body">
                    <ul class="list-group mb-3">
                        <?php foreach (getOrderItems($conn, $order['order_id']) as $item): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <?= htmlspecialchars($item['product_name']) ?> × <?= $item['quantity'] ?>
                                <span><?= number_format($item['quantity'] * $item['price'], 2) ?> บาท</span>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                    <p class="order-total">💰 รวมทั้งสิ้น: <?= number_format($order['total_amount'], 2) ?> บาท</p>

                    <?php $shipping = getShippingInfo($conn, $order['order_id']); ?>
                    <?php if ($shipping): ?>
                        <hr>
                        <h6 class="fw-bold text-primary mb-2"><i class="bi bi-truck me-1"></i>ข้อมูลการจัดส่ง</h6>
                        <p><strong>ที่อยู่:</strong> <?= htmlspecialchars($shipping['address']) ?>,
                            <?= htmlspecialchars($shipping['city']) ?> <?= htmlspecialchars($shipping['postal_code']) ?></p>
                        <p><strong>เบอร์โทร:</strong> <?= htmlspecialchars($shipping['phone']) ?></p>
                        <p><strong>สถานะการจัดส่ง:</strong> 
                            <span class="badge bg-info text-dark"><?= ucfirst($shipping['shipping_status']) ?></span>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<footer>© <?= date('Y') ?> WARMZ Studio – ระบบร้านค้าออนไลน์</footer>

</body>
</html>
