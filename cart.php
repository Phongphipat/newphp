<?php 
session_start();
require 'config.php';

// ✅ ตรวจสอบล็อกอิน
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
$user_id = $_SESSION['user_id'];

// ✅ ดึงรายการสินค้าในตะกร้า
$stmt = $conn->prepare("
    SELECT cart.cart_id, cart.quantity, products.product_name, products.price
    FROM cart
    JOIN products ON cart.product_id = products.product_id
    WHERE cart.user_id = ?
");
$stmt->execute([$user_id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ✅ ลบสินค้าออกจากตะกร้า
if (isset($_GET['remove'])) {
    $cart_id = $_GET['remove'];
    $stmt = $conn->prepare("DELETE FROM cart WHERE cart_id = ? AND user_id = ?");
    $stmt->execute([$cart_id, $user_id]);
    header("Location: cart.php");
    exit;
}

// ✅ คำนวณยอดรวม
$total = 0;
foreach ($items as $item) {
    $total += $item['quantity'] * $item['price'];
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>ตะกร้าสินค้า</title>
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
    }
    @keyframes bg-move {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }
    .cart-container {
        background: rgba(255, 255, 255, 0.92);
        border-radius: 20px;
        backdrop-filter: blur(10px);
        box-shadow: 0 10px 30px rgba(0, 123, 255, 0.25);
        padding: 40px 30px;
        margin-top: 60px;
        transition: 0.3s ease;
    }
    .cart-container:hover {
        box-shadow: 0 15px 40px rgba(0, 123, 255, 0.35);
    }
    h2 {
        color: #007bff;
        font-weight: 700;
        margin-bottom: 24px;
    }
    table {
        background: rgba(255, 255, 255, 0.98);
        border-radius: 15px;
        overflow: hidden;
    }
    th {
        background-color: #e3f2fd;
        color: #0d47a1;
        text-align: center;
        font-weight: 600;
    }
    td {
        vertical-align: middle;
        text-align: center;
    }
    .btn {
        border-radius: 10px;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    .btn:hover {
        transform: translateY(-2px);
    }
    .btn-success {
        background: linear-gradient(90deg, #00b09b, #96c93d);
        border: none;
        box-shadow: 0 4px 10px rgba(0, 150, 136, 0.3);
    }
    .btn-success:hover {
        background: linear-gradient(90deg, #00a085, #88b82b);
        box-shadow: 0 6px 16px rgba(0, 150, 136, 0.5);
    }
    .btn-secondary {
        background: linear-gradient(90deg, #6dd5fa, #2980b9);
        border: none;
        color: white;
        box-shadow: 0 4px 10px rgba(41, 128, 185, 0.3);
    }
    .btn-secondary:hover {
        background: linear-gradient(90deg, #56ccf2, #2f80ed);
        box-shadow: 0 6px 18px rgba(47, 128, 237, 0.5);
    }
    .btn-danger {
        background: linear-gradient(90deg, #ff416c, #ff4b2b);
        border: none;
        box-shadow: 0 4px 10px rgba(255, 65, 108, 0.4);
    }
    .btn-danger:hover {
        background: linear-gradient(90deg, #ff2a5d, #ff6433);
        box-shadow: 0 6px 18px rgba(255, 65, 108, 0.6);
    }
    .alert-warning {
        background: #fff9c4;
        color: #795548;
        border-radius: 10px;
        font-weight: 500;
    }
</style>
</head>
<body>
<div class="container">
    <div class="cart-container">
        <h2><i class="bi bi-cart-check-fill me-2"></i>ตะกร้าสินค้าของคุณ</h2>
        <a href="index.php" class="btn btn-secondary mb-3">
            <i class="bi bi-arrow-left me-1"></i> กลับไปเลือกสินค้า
        </a>

        <?php if (count($items) === 0): ?>
            <div class="alert alert-warning text-center py-3">
                <i class="bi bi-exclamation-triangle me-2"></i>ยังไม่มีสินค้าในตะกร้า
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table align-middle table-bordered mb-4">
                    <thead>
                        <tr>
                            <th>ชื่อสินค้า</th>
                            <th>จำนวน</th>
                            <th>ราคาต่อหน่วย</th>
                            <th>ราคารวม</th>
                            <th>จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                        <tr>
                            <td><?= htmlspecialchars($item['product_name']) ?></td>
                            <td><?= $item['quantity'] ?></td>
                            <td><?= number_format($item['price'], 2) ?> บาท</td>
                            <td><strong><?= number_format($item['price'] * $item['quantity'], 2) ?> บาท</strong></td>
                            <td>
                                <a href="cart.php?remove=<?= $item['cart_id'] ?>" 
                                   class="btn btn-sm btn-danger"
                                   onclick="return confirm('คุณต้องการลบสินค้านี้ออกจากตะกร้าหรือไม่?')">
                                   <i class="bi bi-trash3-fill me-1"></i> ลบ
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <tr>
                            <td colspan="3" class="text-end"><strong>รวมทั้งหมด:</strong></td>
                            <td colspan="2" class="fw-bold text-primary"><?= number_format($total, 2) ?> บาท</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="text-end">
                <a href="checkout.php" class="btn btn-success px-4 py-2">
                    <i class="bi bi-bag-check-fill me-1"></i> สั่งซื้อสินค้า
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
