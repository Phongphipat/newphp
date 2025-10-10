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
    SELECT cart.cart_id, cart.quantity, cart.product_id,
           products.product_name, products.price
    FROM cart
    JOIN products ON cart.product_id = products.product_id
    WHERE cart.user_id = ?
");
$stmt->execute([$user_id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$items) {
    echo "<div style='text-align:center;margin-top:100px;font-family:sans-serif;'>
            <h3>🛒 ไม่มีสินค้าในตะกร้า</h3>
            <a href='index.php' style='color:#1565c0;text-decoration:none;'>กลับไปหน้าหลัก</a>
          </div>";
    exit;
}

// ✅ คำนวณยอดรวม
$total = 0;
foreach ($items as $item) {
    $total += $item['quantity'] * $item['price'];
}

$errors = [];

// ✅ เมื่อผู้ใช้ยืนยันสั่งซื้อ
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $address = trim($_POST['address']);
    $city = trim($_POST['city']);
    $postal_code = trim($_POST['postal_code']);
    $phone = trim($_POST['phone']);

    if (empty($address) || empty($city) || empty($postal_code) || empty($phone)) {
        $errors[] = "กรุณากรอกข้อมูลให้ครบถ้วน";
    }

    if (empty($errors)) {
        $conn->beginTransaction();
        try {
            // บันทึกคำสั่งซื้อ
            $stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, status) VALUES (?, ?, 'pending')");
            $stmt->execute([$user_id, $total]);
            $order_id = $conn->lastInsertId();

            // รายการสินค้า
            $stmtItem = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            foreach ($items as $item) {
                $stmtItem->execute([$order_id, $item['product_id'], $item['quantity'], $item['price']]);
            }

            // ที่อยู่จัดส่ง
            $stmt = $conn->prepare("INSERT INTO shipping (order_id, address, city, postal_code, phone)
                                    VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$order_id, $address, $city, $postal_code, $phone]);

            // ล้างตะกร้า
            $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
            $stmt->execute([$user_id]);

            $conn->commit();
            header("Location: orders.php?success=1");
            exit;
        } catch (Exception $e) {
            $conn->rollBack();
            $errors[] = "เกิดข้อผิดพลาด: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>🛍 ยืนยันการสั่งซื้อ</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;600&display=swap" rel="stylesheet">
<style>
body {
  background: linear-gradient(135deg, #1e3c72, #2a5298);
  background-size: 400% 400%;
  animation: bgmove 12s ease infinite;
  font-family: 'Kanit', sans-serif;
  min-height: 100vh;
  color: #0f172a;
}
@keyframes bgmove {
  0% {background-position: 0% 50%;}
  50% {background-position: 100% 50%;}
  100% {background-position: 0% 50%;}
}
.checkout-card {
  background: rgba(255, 255, 255, 0.95);
  border-radius: 20px;
  box-shadow: 0 10px 30px rgba(0,0,0,0.25);
  padding: 35px 30px;
  margin-top: 60px;
  backdrop-filter: blur(10px);
}
h2 {
  color: #1565c0;
  font-weight: 700;
  margin-bottom: 25px;
}
.table {
  background: white;
  border-radius: 12px;
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
.total-bar {
  background: #1565c0;
  color: white;
  border-radius: 10px;
  padding: 12px 20px;
  text-align: right;
  font-weight: 600;
  font-size: 1.1rem;
}
.btn-success {
  background: linear-gradient(90deg, #00b09b, #96c93d);
  border: none;
  box-shadow: 0 4px 10px rgba(0, 150, 136, 0.3);
  transition: 0.3s ease;
  border-radius: 50px;
  padding: 10px 25px;
}
.btn-success:hover {
  background: linear-gradient(90deg, #00a085, #88b82b);
  transform: translateY(-2px);
}
.btn-secondary {
  background: linear-gradient(90deg, #6dd5fa, #2980b9);
  border: none;
  color: white;
  border-radius: 50px;
  padding: 10px 25px;
}
.alert-danger {
  border-radius: 10px;
  font-weight: 500;
}
label { font-weight: 600; color: #0d47a1; }
</style>
</head>
<body>
<div class="container">
  <div class="checkout-card">
    <h2><i class="bi bi-bag-check-fill me-2"></i>ยืนยันการสั่งซื้อ</h2>

    <?php if (!empty($errors)): ?>
      <div class="alert alert-danger">
        <ul class="mb-0">
          <?php foreach ($errors as $e): ?>
            <li><?= htmlspecialchars($e) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <!-- แสดงสินค้าในตะกร้า -->
    <h5 class="mt-4 mb-3 text-primary">📦 รายการสินค้าในตะกร้า</h5>
    <div class="table-responsive mb-3">
      <table class="table table-bordered align-middle">
        <thead>
          <tr>
            <th>ชื่อสินค้า</th>
            <th>จำนวน</th>
            <th>ราคาต่อหน่วย</th>
            <th>รวม</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($items as $item): ?>
          <tr>
            <td><?= htmlspecialchars($item['product_name']) ?></td>
            <td><?= (int)$item['quantity'] ?></td>
            <td><?= number_format($item['price'], 2) ?> บาท</td>
            <td><strong><?= number_format($item['quantity'] * $item['price'], 2) ?> บาท</strong></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <div class="total-bar mb-4">
      💰 รวมทั้งสิ้น: <?= number_format($total, 2) ?> บาท
    </div>

    <!-- ฟอร์มที่อยู่จัดส่ง -->
    <h5 class="text-primary mb-3">📮 ที่อยู่จัดส่ง</h5>
    <form method="post" class="row g-3">
      <div class="col-md-6">
        <label for="address" class="form-label">ที่อยู่</label>
        <input type="text" name="address" id="address" class="form-control" placeholder="123 หมู่ 5 ต.บางรัก" required>
      </div>
      <div class="col-md-4">
        <label for="city" class="form-label">จังหวัด</label>
        <input type="text" name="city" id="city" class="form-control" placeholder="กรุงเทพมหานคร" required>
      </div>
      <div class="col-md-2">
        <label for="postal_code" class="form-label">รหัสไปรษณีย์</label>
        <input type="text" name="postal_code" id="postal_code" class="form-control" placeholder="10110" required>
      </div>
      <div class="col-md-6">
        <label for="phone" class="form-label">เบอร์โทรศัพท์</label>
        <input type="text" name="phone" id="phone" class="form-control" placeholder="0801234567" required>
      </div>
      <div class="col-12 mt-3 text-end">
        <button type="submit" class="btn btn-success me-2"><i class="bi bi-check-circle me-1"></i> ยืนยันการสั่งซื้อ</button>
        <a href="cart.php" class="btn btn-secondary"><i class="bi bi-arrow-left me-1"></i> กลับไปตะกร้า</a>
      </div>
    </form>
  </div>
</div>
</body>
</html>
