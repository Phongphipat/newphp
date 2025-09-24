<?php
require_once '../config.php';
require_once 'auth_admin.php'; 


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_category'])) {
$category_name = trim($_POST['category_name']);
if ($category_name) {
$stmt = $conn->prepare("INSERT INTO categories (category_name)VALUES (?)");
$stmt->execute([$category_name]);
header("Location: category.php");
exit;
}
}
// ลบหมวดหมู่
// ตรวจสอบวำ่ หมวดหมนู่ ี้ยังถกู ใชอ้ยหู่ รอื ไม่
if (isset($_GET['delete'])) {
$category_id = $_GET['delete'];
// ตรวจสอบวำ่ หมวดหมนู่ ยี้ ังถูกใชอ้ยหู่ รอื ไม่
$stmt = $conn->prepare("SELECT COUNT(*) FROM products WHERE category_id = ?");
$stmt->execute([$category_id]);
$productCount = $stmt->fetchColumn();
if ($productCount > 0) {
// ถำ้มสี นิ คำ้อยใู่ นหมวดหมนู่ ี้
$_SESSION['error'] = "ไม่สามารถลบหมวดหมู่นี้ได้เนื่องจากยังมีสินค้าที่ใช้งานหมวดหมู่นี้อยู่";
} else {
// ถำ้ไมม่ สี นิ คำ้ ใหล้ บได ้
$stmt = $conn->prepare("DELETE FROM categories WHERE category_id = ?");
$stmt->execute([$category_id]);
$_SESSION['success'] = "ลบหมวดหมู่เรียบร้อย";
}
header("Location: category.php");
exit;
}

// แก ้ไขหมวดหมู่
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_category'])) {
$category_id = $_POST['category_id'];
$category_name = trim($_POST['new_name']);
if ($category_name) {
$stmt = $conn->prepare("UPDATE categories SET category_name = ? WHERE category_id =
?");
$stmt->execute([$category_name, $category_id]);
header("Location: category.php");
exit;
}
}
// ดึงหมวดหมู่ทั้งหมด
$categories = $conn->query("SELECT * FROM categories ORDER BY category_id ASC")->fetchAll(PDO::FETCH_ASSOC);
// โคด้ นเี้ขยีนตอ่ กันยำวบรรทัดเดยี วไดเ้พรำะ ผลลัพธจ์ ำกเมธอดหนงึ่ สำมำรถสง่ ตอ่ (chaining) ให้เมธอดถัดไปทันที โดยไม่ต ้องแยกตัวแปรเก็บไว้ก่อน
// $pdo->query("...")->fetchAll(...);
// หำกเขียนแยกเป็นหลำยบรรทัดจะเป็นแบบนี้:
// $stmt = $pdo->query("SELECT * FROM categories ORDER BY category_id ASC");
// $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
// ควรเขยีนแยกบรรทัดเมอื่ จะ ใช ้$stmt ซ ้ำหลำยครัง้ (เชน่ fetch ทีละ row, ตรวจจ ำนวนแถว)
// หรือเขียนแบบ prepare , execute
// $stmt = $pdo->prepare("SELECT * FROM categories ORDER BY category_id ASC");
// $stmt->execute();
// $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>จัดการหมวดหมู่</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
    body {
        background: linear-gradient(135deg, #ff6b6b, #ee5a52);
        background-size: 400% 400%;
        animation: gradient-animation 15s ease infinite;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        min-height: 100vh;
    }
    @keyframes gradient-animation {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }
    .card {
        backdrop-filter: blur(10px);
        background: rgba(255, 255, 255, 0.95);
        border: none;
        border-radius: 20px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease;
    }
    .card:hover {
        transform: translateY(-5px);
    }
    .btn-secondary {
        background-color: #6c757d;
        border-color: #6c757d;
        transition: background-color 0.3s ease;
    }
    .btn-secondary:hover {
        background-color: #5c636a;
        border-color: #565e64;
    }
    .btn-primary {
        background-color: #0d6efd;
        border-color: #0d6efd;
        transition: background-color 0.3s ease;
    }
    .btn-primary:hover {
        background-color: #0b5ed7;
        border-color: #0a58ca;
    }
</style>
</head>
<body class="container mt-4">
    <div class="card shadow-lg p-4">
        <h2><i class="fas fa-tags me-2"></i>จัดการหมวดหมู่สินค้า</h2>
        <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><i class="fas fa-exclamation-triangle me-2"></i><?= $_SESSION['error'] ?></div>
        <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
        <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><i class="fas fa-check-circle me-2"></i><?= $_SESSION['success'] ?></div>
        <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        <a href="index.php" class="btn btn-secondary mb-3"><i class="fas fa-arrow-left me-1"></i>กลับหน้าผู้ดูแล</a>
        <form method="post" class="row g-3 mb-4">
            <div class="col-md-6">
                <input type="text" name="category_name" class="form-control" placeholder="ชื่อหมวดหมู่ใหม่" required>
            </div>
            <div class="col-md-2">
                <button type="submit" name="add_category" class="btn btn-primary"><i class="fas fa-plus me-1"></i>เพิ่มหมวดหมู่</button>
            </div>
        </form>
        <h5>รายการหมวดหมู่</h5>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ชื่อหมวดหมู่</th>
                        <th>แก้ไขชื่อ</th>
                        <th>จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $cat): ?>
                    <tr>
                        <td><?= htmlspecialchars($cat['category_name']) ?></td>
                        <td>
                            <form method="post" class="d-flex">
                                <input type="hidden" name="category_id" value="<?= $cat['category_id'] ?>">
                                <input type="text" name="new_name" class="form-control me-2" placeholder="ชื่อใหม่" required>
                                <button type="submit" name="update_category" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i> แก้ไข</button>
                            </form>
                        </td>
                        <td>
                            <a href="#" class="btn btn-sm btn-danger delete-btn" data-bs-toggle="modal" data-bs-target="#deleteModal" data-category-id="<?= $cat['category_id'] ?>">
                                <i class="fas fa-trash-alt"></i> ลบ
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">ยืนยันการลบ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    คุณต้องการลบหมวดหมู่นี้หรือไม่?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <a id="deleteCategoryLink" href="#" class="btn btn-danger">ยืนยันการลบ</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const deleteModal = document.getElementById('deleteModal');
            const deleteCategoryLink = document.getElementById('deleteCategoryLink');

            if (deleteModal) {
                deleteModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    const categoryId = button.getAttribute('data-category-id');
                    deleteCategoryLink.href = 'category.php?delete=' + categoryId;
                });
            }
        });
    </script>
</body>
</html>
