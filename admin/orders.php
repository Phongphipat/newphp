<?php
require_once '../config.php';
require_once 'auth_admin.php';
require '../function.php';

// ✅ ดึงคำสั่งซื้อทั้งหมด
$stmt = $conn->query("
    SELECT o.*, u.username
    FROM orders o
    LEFT JOIN users u ON o.user_id = u.user_id
    ORDER BY o.order_date DESC
");
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ✅ อัปเดตสถานะ
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_status'])) {
        $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE order_id = ?");
        $stmt->execute([$_POST['status'], $_POST['order_id']]);
        header("Location: orders.php");
        exit;
    }
    if (isset($_POST['update_shipping'])) {
        $stmt = $conn->prepare("UPDATE shipping SET shipping_status = ? WHERE shipping_id = ?");
        $stmt->execute([$_POST['shipping_status'], $_POST['shipping_id']]);
        header("Location: orders.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>จัดการคำสั่งซื้อ | Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;600&display=swap" rel="stylesheet">

<style>
    body {
        font-family: 'Kanit', sans-serif;
        background: linear-gradient(135deg, #00b4db, #0083b0);
        background-size: 400% 400%;
        animation: gradient-move 15s ease infinite;
        min-height: 100vh;
        color: #0f172a;
        padding-bottom: 60px;
    }

    @keyframes gradient-move {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }

    h2 {
        color: #fff;
        text-shadow: 0 2px 10px rgba(0, 0, 0, 0.25);
        margin-bottom: 24px;
    }

    .card, .accordion-item {
        border: none;
        border-radius: 20px;
        backdrop-filter: blur(10px);
        background: rgba(255, 255, 255, 0.95);
        box-shadow: 0 10px 25px rgba(0, 123, 255, 0.25);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .accordion-button {
        border-radius: 20px !important;
        font-weight: 600;
        color: #0f172a;
        background: linear-gradient(90deg, #dff3ff, #f0f9ff);
    }

    .accordion-button:not(.collapsed) {
        color: #fff;
        background: linear-gradient(90deg, #007bff, #00bfff);
        box-shadow: 0 4px 15px rgba(0, 123, 255, 0.4);
    }

    .accordion-button:focus {
        box-shadow: none;
    }

    .accordion-button::after {
        filter: brightness(0) invert(1);
    }

    .accordion-body {
        background: rgba(255, 255, 255, 0.96);
        border-radius: 15px;
    }

    .list-group-item {
        border: none;
        border-radius: 10px;
        background: #f8fbff;
        margin-bottom: 6px;
    }

    .list-group-item span {
        font-weight: 600;
        color: #007bff;
    }

    .badge {
        font-size: 0.9rem;
        padding: 6px 10px;
        border-radius: 8px;
    }

    .btn {
        border-radius: 10px;
        font-weight: 500;
        transition: all 0.25s ease;
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(0, 150, 255, 0.3);
    }

    .btn-primary {
        background: linear-gradient(90deg, #007bff, #00bfff);
        border: none;
        color: #fff;
    }

    .btn-success {
        background: linear-gradient(90deg, #28a745, #6dd5fa);
        border: none;
        color: #fff;
    }

    .btn-secondary {
        background: linear-gradient(90deg, #6dd5fa, #2980b9);
        border: none;
        color: #fff;
    }

    .container {
        margin-top: 40px;
    }

    footer {
        text-align: center;
        color: rgba(255, 255, 255, 0.8);
        margin-top: 40px;
        font-size: 0.9rem;
    }
</style>
</head>

<body class="container">

<h2 class="text-center">📦 จัดการคำสั่งซื้อทั้งหมด</h2>
<div class="text-center mb-3">
    <a href="index.php" class="btn btn-secondary">← กลับหน้าผู้ดูแล</a>
</div>

<div class="accordion" id="ordersAccordion">

<?php foreach ($orders as $index => $order): ?>
    <?php $shipping = getShippingInfo($conn, $order['order_id']); ?>

    <div class="accordion-item mb-3">
        <h2 class="accordion-header" id="heading<?= $index ?>">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= $index ?>">
                <strong>คำสั่งซื้อ #<?= $order['order_id'] ?></strong> — <?= htmlspecialchars($order['username']) ?> 
                | <?= date('d/m/Y H:i', strtotime($order['order_date'])) ?> 
                | <span class="badge bg-info text-dark"><?= ucfirst($order['status']) ?></span>
            </button>
        </h2>

        <div id="collapse<?= $index ?>" class="accordion-collapse collapse" data-bs-parent="#ordersAccordion">
            <div class="accordion-body">
                <h5 class="mt-2">🛍️ รายการสินค้า</h5>
                <ul class="list-group mb-3">
                    <?php foreach (getOrderItems($conn, $order['order_id']) as $item): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <?= htmlspecialchars($item['product_name']) ?> × <?= $item['quantity'] ?>
                            <span><?= number_format($item['quantity'] * $item['price'], 2) ?> บาท</span>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <p class="fw-bold">💰 ยอดรวม: <span class="text-primary"><?= number_format($order['total_amount'], 2) ?> บาท</span></p>

                <!-- อัปเดตสถานะ -->
                <form method="post" class="row g-2 mb-3">
                    <input type="hidden" name="order_id" value="<?= $order['order_id'] ?>">
                    <div class="col-md-4">
                        <select name="status" class="form-select">
                            <?php
                            $statuses = ['pending', 'processing', 'shipped', 'completed', 'cancelled'];
                            foreach ($statuses as $status) {
                                $selected = ($order['status'] === $status) ? 'selected' : '';
                                echo "<option value=\"$status\" $selected>$status</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" name="update_status" class="btn btn-primary">อัปเดตสถานะ</button>
                    </div>
                </form>

                <?php if ($shipping): ?>
                    <h5>🚚 ข้อมูลการจัดส่ง</h5>
                    <p><strong>ที่อยู่:</strong> <?= htmlspecialchars($shipping['address']) ?>, <?= htmlspecialchars($shipping['city']) ?> <?= htmlspecialchars($shipping['postal_code']) ?></p>
                    <p><strong>เบอร์โทร:</strong> <?= htmlspecialchars($shipping['phone']) ?></p>

                    <form method="post" class="row g-2">
                        <input type="hidden" name="shipping_id" value="<?= $shipping['shipping_id'] ?>">
                        <div class="col-md-4">
                            <select name="shipping_status" class="form-select">
                                <?php
                                $s_statuses = ['not_shipped', 'shipped', 'delivered'];
                                foreach ($s_statuses as $s) {
                                    $selected = ($shipping['shipping_status'] === $s) ? 'selected' : '';
                                    echo "<option value=\"$s\" $selected>$s</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" name="update_shipping" class="btn btn-success">อัปเดตการจัดส่ง</button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php endforeach; ?>
</div>

<footer>© <?= date('Y') ?> WARMZ Studio Admin Panel</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
