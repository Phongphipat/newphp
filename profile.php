<?php
session_start();
require 'config.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
$user_id = $_SESSION['user_id'];
$errors = [];
$success = "";

// ✅ ดึงข้อมูลสมาชิก
$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// ✅ เมื่อส่งฟอร์ม
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($full_name) || empty($email)) {
        $errors[] = "กรุณากรอกชื่อ-นามสกุลและอีเมล";
    }

    // ตรวจสอบอีเมลซ้ำ
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND user_id != ?");
    $stmt->execute([$email, $user_id]);
    if ($stmt->rowCount() > 0) {
        $errors[] = "อีเมลนี้ถูกใช้งานแล้ว";
    }

    // ตรวจสอบรหัสผ่าน
    if (!empty($current_password) || !empty($new_password) || !empty($confirm_password)) {
        if (!password_verify($current_password, $user['password'])) {
            $errors[] = "รหัสผ่านเดิมไม่ถูกต้อง";
        } elseif (strlen($new_password) < 6) {
            $errors[] = "รหัสผ่านใหม่ต้องมีอย่างน้อย 6 ตัวอักษร";
        } elseif ($new_password !== $confirm_password) {
            $errors[] = "รหัสผ่านใหม่และยืนยันไม่ตรงกัน";
        } else {
            $new_hashed = password_hash($new_password, PASSWORD_DEFAULT);
        }
    }

    // ✅ อัปเดตข้อมูล
    if (empty($errors)) {
        if (!empty($new_hashed)) {
            $stmt = $conn->prepare("UPDATE users SET full_name = ?, email = ?, password = ? WHERE user_id = ?");
            $stmt->execute([$full_name, $email, $new_hashed, $user_id]);
        } else {
            $stmt = $conn->prepare("UPDATE users SET full_name = ?, email = ? WHERE user_id = ?");
            $stmt->execute([$full_name, $email, $user_id]);
        }
        $success = "✅ บันทึกข้อมูลเรียบร้อยแล้ว";
        $_SESSION['username'] = $user['username'];
        $user['full_name'] = $full_name;
        $user['email'] = $email;
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>โปรไฟล์สมาชิก</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;600&display=swap" rel="stylesheet">
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

    .profile-card {
        max-width: 800px;
        margin: 60px auto;
        background: rgba(255, 255, 255, 0.92);
        border-radius: 20px;
        backdrop-filter: blur(10px);
        box-shadow: 0 10px 30px rgba(0, 123, 255, 0.25);
        padding: 40px 30px;
        transition: 0.3s ease;
    }

    .profile-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 40px rgba(0, 123, 255, 0.35);
    }

    h2 {
        color: #007bff;
        font-weight: 600;
        margin-bottom: 24px;
    }

    label {
        font-weight: 500;
        color: #0f172a;
    }

    input.form-control {
        border-radius: 10px;
        border: 1px solid #d0e4ff;
        padding: 10px;
        transition: 0.2s;
    }

    input.form-control:focus {
        border-color: #00aaff;
        box-shadow: 0 0 8px rgba(0, 170, 255, 0.3);
    }

    .btn-primary {
        background: linear-gradient(90deg, #007bff, #00bfff);
        border: none;
        color: #fff;
        border-radius: 10px;
        font-weight: 500;
        transition: all 0.3s ease;
        box-shadow: 0 4px 14px rgba(0, 123, 255, 0.4);
    }
    .btn-primary:hover {
        background: linear-gradient(90deg, #005ce6, #00aaff);
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(0, 100, 255, 0.6);
    }

    .btn-secondary {
        background: linear-gradient(90deg, #6dd5fa, #2980b9);
        border: none;
        border-radius: 10px;
        color: white;
        transition: all 0.3s ease;
        box-shadow: 0 4px 10px rgba(41, 128, 185, 0.4);
    }
    .btn-secondary:hover {
        background: linear-gradient(90deg, #56ccf2, #2f80ed);
        transform: translateY(-2px);
    }

    .alert {
        border-radius: 12px;
    }

    hr {
        border-color: rgba(0,0,0,0.1);
    }
</style>
</head>
<body>

<div class="profile-card">
    <h2><i class="bi bi-person-circle me-2"></i>โปรไฟล์ของคุณ</h2>
    <a href="index.php" class="btn btn-secondary mb-3">← กลับหน้าหลัก</a>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php elseif (!empty($success)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="post" class="row g-3">
        <div class="col-md-6">
            <label for="full_name" class="form-label">ชื่อ - นามสกุล</label>
            <input type="text" name="full_name" class="form-control" required value="<?= htmlspecialchars($user['full_name']) ?>">
        </div>
        <div class="col-md-6">
            <label for="email" class="form-label">อีเมล</label>
            <input type="email" name="email" class="form-control" required value="<?= htmlspecialchars($user['email']) ?>">
        </div>

        <div class="col-12"><hr><h5>🔒 เปลี่ยนรหัสผ่าน (ไม่จำเป็น)</h5></div>

        <div class="col-md-6">
            <label for="current_password" class="form-label">รหัสผ่านเดิม</label>
            <input type="password" name="current_password" id="current_password" class="form-control">
        </div>
        <div class="col-md-6">
            <label for="new_password" class="form-label">รหัสผ่านใหม่ </label>
            <input type="password" name="new_password" id="new_password" class="form-control">
        </div>
        <div class="col-md-6">
            <label for="confirm_password" class="form-label">ยืนยันรหัสผ่านใหม่</label>
            <input type="password" name="confirm_password" id="confirm_password" class="form-control">
        </div>

        <div class="col-12 mt-3">
            <button type="submit" class="btn btn-primary w-100 py-2">💾 บันทึกการเปลี่ยนแปลง</button>
        </div>
    </form>
</div>

</body>
</html>
