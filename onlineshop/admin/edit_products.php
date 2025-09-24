<?php
require_once '../config.php';
require_once 'auth_admin.php';

// ตรวจสอบ id
if (!isset($_GET['id'])) {
    header("Location: products.php");
    exit;
}

$product_id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM products WHERE product_id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$product) {
    echo "<h3 class='text-danger text-center mt-5'>❌ ไม่พบข้อมูลสินค้า</h3>";
    exit;
}

// ดึงหมวดหมู่ทั้งหมด
$categories = $conn->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);

// เมื่อบันทึกฟอร์ม
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['product_name']);
    $description = trim($_POST['description']);
    $price = (float)$_POST['price'];
    $stock = (int)$_POST['stock'];
    $category_id = (int)$_POST['category_id'];

    $oldImage = $_POST['old_image'] ?? null;
    $removeImage = isset($_POST['remove_image']);
    $newImageName = $oldImage;

    if ($removeImage) {
        $newImageName = null;
    }

    if (!empty($_FILES['product_image']['name'])) {
        $file = $_FILES['product_image'];
        $allowed = ['image/jpeg', 'image/png'];
        if (in_array($file['type'], $allowed, true) && $file['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $newImageName = 'product_' . time() . '.' . $ext;
            $uploadDir = realpath(__DIR__ . '/../product_images');
            $destPath = $uploadDir . DIRECTORY_SEPARATOR . $newImageName;
            if (!move_uploaded_file($file['tmp_name'], $destPath)) {
                $newImageName = $oldImage;
            }
        }
    }

    $sql = "UPDATE products
            SET product_name = ?, description = ?, price = ?, stock = ?, category_id = ?, image = ?
            WHERE product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$name, $description, $price, $stock, $category_id, $newImageName, $product_id]);

    if (!empty($oldImage) && $oldImage !== $newImageName) {
        $baseDir = realpath(__DIR__ . '/../product_images');
        $filePath = realpath($baseDir . DIRECTORY_SEPARATOR . $oldImage);
        if ($filePath && strpos($filePath, $baseDir) === 0 && is_file($filePath)) {
            @unlink($filePath);
        }
    }

    header("Location: products.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>แก้ไขสินค้า</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
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
    .card {
      background: rgba(255,255,255,0.95);
      backdrop-filter: blur(12px);
      border-radius: 22px;
      box-shadow: 0 12px 28px rgba(0,0,0,0.15);
      border: none;
    }
    .card-header {
      background: linear-gradient(135deg, #42a5f5, #1565c0);
      color: #fff;
      font-weight: 700;
      font-size: 1.3rem;
      text-align: center;
      border-top-left-radius: 22px !important;
      border-top-right-radius: 22px !important;
      padding: 1.2rem;
    }
    .btn-primary {
      background: linear-gradient(135deg,#42a5f5,#1565c0);
      border: none;
      border-radius: 50px;
      padding: 10px 24px;
      font-weight: 600;
    }
    .btn-primary:hover {
      filter: brightness(1.08);
      box-shadow: 0 6px 16px rgba(21,101,192,0.4);
    }
    .btn-secondary {
      border-radius: 50px;
      padding: 8px 20px;
    }
    label.form-label {
      font-weight: 600;
      color: #1565c0;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="card shadow-lg">
      <div class="card-header">
        <i class="fas fa-edit me-2"></i> แก้ไขสินค้า
      </div>
      <div class="card-body p-4">
        <a href="products.php" class="btn btn-secondary mb-3">
          <i class="fas fa-arrow-left me-1"></i> กลับไปยังรายการสินค้า
        </a>

        <form method="post" enctype="multipart/form-data" class="row g-3">
          <div class="col-md-6">
            <label class="form-label">ชื่อสินค้า</label>
            <input type="text" name="product_name" class="form-control" 
              value="<?= htmlspecialchars($product['product_name']) ?>" required>
          </div>
          <div class="col-md-3">
            <label class="form-label">ราคา</label>
            <input type="number" step="0.01" name="price" class="form-control" 
              value="<?= $product['price'] ?>" required>
          </div>
          <div class="col-md-3">
            <label class="form-label">จำนวนในคลัง</label>
            <input type="number" name="stock" class="form-control" 
              value="<?= $product['stock'] ?>" required>
          </div>

          <div class="col-md-6">
            <label class="form-label">หมวดหมู่</label>
            <select name="category_id" class="form-select" required>
              <option value="">เลือกหมวดหมู่</option>
              <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['category_id'] ?>" 
                  <?= ($product['category_id'] == $cat['category_id']) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($cat['category_name']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="col-12">
            <label class="form-label">รายละเอียดสินค้า</label>
            <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($product['description']) ?></textarea>
          </div>

          <div class="col-md-6">
            <label class="form-label d-block">รูปปัจจุบัน</label>
            <?php if (!empty($product['image'])): ?>
              <img src="../product_images/<?= htmlspecialchars($product['image']) ?>" width="140" class="rounded mb-2 shadow-sm">
            <?php else: ?>
              <span class="text-muted d-block mb-2">ไม่มีรูป</span>
            <?php endif; ?>
            <input type="hidden" name="old_image" value="<?= htmlspecialchars($product['image']) ?>">
          </div>

          <div class="col-md-6">
            <label class="form-label">อัปโหลดรูปใหม่ (jpg, png)</label>
            <input type="file" name="product_image" class="form-control">
            <div class="form-check mt-2">
              <input class="form-check-input" type="checkbox" name="remove_image" id="remove_image" value="1">
              <label class="form-check-label" for="remove_image">ลบรูปเดิม</label>
            </div>
          </div>

          <div class="col-12">
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-save me-1"></i> บันทึกการแก้ไข
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</body>
</html>
