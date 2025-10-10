<?php
session_start();
require_once 'config.php';
$isLoggedIn = isset($_SESSION['user_id']);

$stmt = $conn->query("SELECT p.*, c.category_name
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.category_id
    ORDER BY p.created_at DESC");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="th">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>หน้าหลัก</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
body {
  background: linear-gradient(-45deg, #0d47a1, #1565c0, #1e88e5, #42a5f5);
  background-size: 400% 400%;
  animation: gradient-animation 18s ease infinite;
  font-family: 'Kanit', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  min-height: 100vh;
}
@keyframes gradient-animation {
  0% { background-position: 0% 50%; }
  50% { background-position: 100% 50%; }
  100% { background-position: 0% 50%; }
}
.card {
  backdrop-filter: blur(12px);
  background: rgba(255, 255, 255, 0.95);
  border: none;
  border-radius: 22px;
  box-shadow: 0 10px 28px rgba(21, 101, 192, 0.18);
  transition: all 0.3s ease;
}
.card:hover {
  transform: translateY(-6px);
  box-shadow: 0 16px 40px rgba(21, 101, 192, 0.25);
}
h1 {
  color: #fff;
  font-weight: 700;
  text-shadow: 0 3px 6px rgba(0, 0, 0, 0.3);
}
.btn-success {
  background: linear-gradient(135deg, #43a047, #2e7d32);
  border: none;
  color: #fff;
  transition: all .3s ease;
}
.btn-success:hover {
  filter: brightness(1.1);
  box-shadow: 0 6px 18px rgba(46, 125, 50, 0.4);
}
.btn-outline-primary {
  border-color: #1565c0;
  color: #1565c0;
}
.btn-outline-primary:hover {
  background-color: #1565c0;
  color: #fff;
}
.badge-top-left {
  position: absolute;
  top: .6rem;
  left: .6rem;
  border-radius: .4rem;
  font-weight: 600;
  padding: .35rem .6rem;
  font-size: .75rem;
}
.product-meta {
  font-size: .75rem;
  letter-spacing: .05em;
  color: #607d8b;
  text-transform: uppercase;
}
.product-title {
  font-size: 1rem;
  margin: .25rem 0 .5rem;
  font-weight: 700;
  color: #0d47a1;
}
.price {
  font-weight: 800;
  color: #1565c0;
  background: #e3f2fd;
  border-radius: 999px;
  padding: .25rem .8rem;
  display: inline-block;
}
.rating i { color: #ffc107; }
.login-required {
  color: #0d47a1;
  font-weight: 600;
  cursor: pointer;
  text-shadow: 0 0 6px rgba(33, 150, 243, 0.4);
}
</style>
</head>

<body class="container mt-4">

<div class="d-flex justify-content-between align-items-center mb-4">
  <h1>รายการสินค้า</h1>
  <div>
    <?php if ($isLoggedIn): ?>
      <span class="me-3 text-light">ยินดีต้อนรับ, <?= htmlspecialchars($_SESSION['username']) ?></span>
      <a href="profile.php" class="btn btn-info">ข้อมูลส่วนตัว</a>
      <a href="cart.php" class="btn btn-warning">ดูตะกร้า</a>
      <a href="orders.php" class="btn btn-primary">ประวัติการสั่งซื้อ</a>
      <a href="logout.php" class="btn btn-secondary">ออกจากระบบ</a>
    <?php else: ?>
      <a href="login.php" class="btn btn-success">เข้าสู่ระบบ</a>
      <a href="register.php" class="btn btn-primary">สมัครสมาชิก</a>
    <?php endif; ?>
  </div>
</div>

<div class="row g-4">
<?php foreach ($products as $p): 
  $img = !empty($p['image']) ? 'product_images/' . rawurlencode($p['image']) : 'product_images/no-image.jpg';
  $isNew = isset($p['created_at']) && (time() - strtotime($p['created_at']) <= 7 * 24 * 3600);
  $isHot = (int)$p['stock'] > 0 && (int)$p['stock'] < 5;
  $rating = isset($p['rating']) ? (float)$p['rating'] : 4.5;
  $full = floor($rating); $half = ($rating - $full) >= 0.5 ? 1 : 0;
?>
  <div class="col-12 col-sm-6 col-lg-3">
    <div class="card h-100 position-relative">
      <?php if ($isNew): ?><span class="badge bg-success badge-top-left">NEW</span>
      <?php elseif ($isHot): ?><span class="badge bg-danger badge-top-left">HOT</span><?php endif; ?>

      <a href="product_detail.php?id=<?= (int)$p['product_id'] ?>" class="p-3 d-block">
        <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($p['product_name']) ?>" class="img-fluid w-100">
      </a>

      <div class="px-3 pb-3 d-flex flex-column">
        <div class="product-meta mb-1"><?= htmlspecialchars($p['category_name'] ?? 'Category') ?></div>
        <a class="text-decoration-none" href="product_detail.php?id=<?= (int)$p['product_id'] ?>">
          <div class="product-title"><?= htmlspecialchars($p['product_name']) ?></div>
        </a>

        <div class="rating mb-2">
          <?php for ($i=0;$i<$full;$i++): ?><i class="bi bi-star-fill"></i><?php endfor; ?>
          <?php if ($half): ?><i class="bi bi-star-half"></i><?php endif; ?>
          <?php for ($i=0;$i<5-$full-$half;$i++): ?><i class="bi bi-star"></i><?php endfor; ?>
        </div>

        <div class="price mb-3"><?= number_format((float)$p['price'], 2) ?> บาท</div>

        <div class="mt-auto d-flex gap-2 align-items-center">
          <?php if ($isLoggedIn): ?>
            <button type="button" class="btn btn-sm btn-success add-to-cart-btn" data-id="<?= (int)$p['product_id'] ?>">
              เพิ่มในตะกร้า
            </button>
          <?php else: ?>
            <small class="login-required" onclick="alertLogin()">
              <i class="bi bi-lock-fill me-1"></i>เข้าสู่ระบบเพื่อสั่งซื้อ
            </small>
          <?php endif; ?>
          <a href="product_detail.php?id=<?= (int)$p['product_id'] ?>" class="btn btn-sm btn-outline-primary ms-auto">ดูรายละเอียด</a>
        </div>
      </div>
    </div>
  </div>
<?php endforeach; ?>
</div>

<script>
document.querySelectorAll('.add-to-cart-btn').forEach(btn => {
  btn.addEventListener('click', async () => {
    const productId = btn.dataset.id;
    const res = await fetch('cart_add.php', {
      method: 'POST',
      headers: {'Content-Type': 'application/x-www-form-urlencoded'},
      body: 'product_id=' + encodeURIComponent(productId) + '&quantity=1'
    });
    const data = await res.json();
    Swal.fire({
      icon: data.success ? 'success' : 'error',
      title: data.success ? 'เพิ่มสินค้าสำเร็จ!' : 'ผิดพลาด!',
      text: data.message,
      confirmButtonColor: '#1565c0',
      timer: 1800,
      showConfirmButton: false
    });
  });
});

function alertLogin() {
  Swal.fire({
    icon: 'info',
    title: 'กรุณาเข้าสู่ระบบก่อน',
    text: 'คุณต้องเข้าสู่ระบบเพื่อสั่งซื้อสินค้า',
    confirmButtonText: 'เข้าสู่ระบบ',
    confirmButtonColor: '#1565c0',
    background: '#f8f9fa'
  }).then((result) => {
    if (result.isConfirmed) window.location.href = 'login.php';
  });
}
</script>
</body>
</html>
