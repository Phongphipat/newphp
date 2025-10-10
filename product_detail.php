<?php
session_start();
require_once 'config.php';
$isLoggedIn = isset($_SESSION['user_id']);

if (!isset($_GET['id'])) {
  header('Location: index.php');
  exit();
}

$product_id = $_GET['id'];
$stmt = $conn->prepare("SELECT p.*, c.category_name
FROM products p
LEFT JOIN categories c ON p.category_id = c.category_id
WHERE p.product_id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
  header('Location: index.php');
  exit();
}

$img = !empty($product['image'])
  ? 'product_images/' . rawurlencode($product['image'])
  : 'product_images/no-image.jpg';
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($product['product_name']) ?> - รายละเอียดสินค้า</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    body {
      background: linear-gradient(-45deg, #0d47a1, #1565c0, #1e88e5, #42a5f5);
      background-size: 400% 400%;
      animation: gradient-animation 18s ease infinite;
      font-family: 'Kanit','Segoe UI',Tahoma,sans-serif;
      min-height: 100vh;
      padding: 30px;
    }
    @keyframes gradient-animation {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }
    .product-card {
      backdrop-filter: blur(14px);
      background: rgba(255,255,255,0.95);
      border-radius: 26px;
      box-shadow: 0 12px 32px rgba(0,0,0,0.15);
      overflow: hidden;
      padding: 2rem;
    }
    .breadcrumb { background: transparent; font-size: 0.95rem; }
    .product-image {
      width: 100%;
      max-height: 420px;
      object-fit: cover;
      border-radius: 18px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.2);
    }
    .product-title { font-size: 2rem; font-weight: 700; color: #1565c0; }
    .price { font-size: 1.6rem; font-weight: 700; color: #2e7d32; }
    .stock { font-size: 1.1rem; color: #1565c0; }
    .btn-success {
      background: linear-gradient(135deg,#43a047,#2e7d32);
      border: none;
      padding: 12px 28px;
      font-size: 1rem;
      border-radius: 50px;
    }
    .btn-success:hover {
      filter: brightness(1.1);
      box-shadow: 0 6px 18px rgba(67,160,71,0.4);
    }
    .btn-primary {
      background: linear-gradient(135deg,#42a5f5,#1565c0);
      border: none;
      padding: 12px 28px;
      border-radius: 50px;
    }
    .btn-outline-danger { border-radius: 50px; }
  </style>
</head>
<body>
  <div class="container">
    
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
      <ol class="breadcrumb text-white">
        <li class="breadcrumb-item"><a href="index.php" class="text-white-50">หน้าหลัก</a></li>
        <li class="breadcrumb-item"><a href="#" class="text-white-50"><?= htmlspecialchars($product['category_name']) ?></a></li>
        <li class="breadcrumb-item active text-white"><?= htmlspecialchars($product['product_name']) ?></li>
      </ol>
    </nav>

    <div class="product-card">
      <div class="row g-4 align-items-center">
        
        <!-- รูปสินค้า -->
        <div class="col-md-6 text-center">
          <img src="<?= $img ?>" alt="Product Image" class="product-image">
        </div>
        
        <!-- รายละเอียดสินค้า -->
        <div class="col-md-6">
          <h2 class="product-title mb-3"><?= htmlspecialchars($product['product_name']) ?></h2>
          <p class="text-muted mb-3"><i class="fas fa-tag me-1"></i> <?= htmlspecialchars($product['category_name']) ?></p>
          <p class="mb-4"><?= nl2br(htmlspecialchars($product['description'])) ?></p>
          
          <div class="d-flex justify-content-between mb-4">
            <span class="price"><i class="fas fa-money-bill me-2"></i><?= number_format($product['price'], 2) ?> บาท</span>
            <span class="stock"><i class="fas fa-boxes me-2"></i>คงเหลือ <?= (int)$product['stock'] ?> ชิ้น</span>
          </div>

          <?php if ($isLoggedIn): ?>
            <div class="d-flex gap-2 align-items-center flex-wrap">
              <input type="number" id="qty" class="form-control" value="1" min="1" max="<?= $product['stock'] ?>" style="width:100px;">
              <button type="button" class="btn btn-success" id="addCartBtn" data-id="<?= $product['product_id'] ?>">
                <i class="fas fa-cart-plus me-1"></i> เพิ่มในตะกร้า
              </button>
            </div>
          <?php else: ?>
            <div class="alert alert-info">
              <i class="fas fa-info-circle me-2"></i> กรุณาเข้าสู่ระบบเพื่อสั่งซื้อ
              <a href="login.php" class="btn btn-primary btn-sm ms-2">เข้าสู่ระบบ</a>
            </div>
          <?php endif; ?>
          
          <a href="index.php" class="btn btn-outline-danger mt-3"><i class="fas fa-arrow-left me-1"></i> กลับหน้าหลัก</a>
        </div>
      </div>
    </div>

  </div>

  <script>
  document.addEventListener('DOMContentLoaded', () => {
      const addBtn = document.getElementById('addCartBtn');
      if (!addBtn) return;

      addBtn.addEventListener('click', async () => {
          const productId = addBtn.dataset.id;
          const qty = document.getElementById('qty').value || 1;

          const res = await fetch('cart_add.php', {
              method: 'POST',
              headers: {'Content-Type': 'application/x-www-form-urlencoded'},
              body: 'product_id=' + encodeURIComponent(productId) + '&quantity=' + encodeURIComponent(qty)
          });
          const data = await res.json();

          Swal.fire({
              icon: data.success ? 'success' : 'error',
              title: data.success ? 'เพิ่มสินค้าสำเร็จ!' : 'ผิดพลาด!',
              text: data.message,
              confirmButtonColor: '#1565c0',
              timer: 2000,
              showConfirmButton: false
          });
      });
  });
  </script>
</body>
</html>
