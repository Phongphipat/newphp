<?php
require_once '../config.php';
require_once 'auth_admin.php'; // ตรวจสิทธิ์แอดมิน

if (!isset($_GET['id'])) {
    header("Location: users.php");
    exit;
}

$user_id = (int)$_GET['id'];

// ดึงข้อมูลสมาชิก (เฉพาะ role = member)
$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ? AND role = 'member'");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "<div style='text-align:center;margin-top:100px;font-family:sans-serif;'>
            <h3>❌ ไม่พบข้อมูลสมาชิก</h3>
            <a href='users.php' style='color:#1565c0;text-decoration:none;'>กลับหน้ารายชื่อสมาชิก</a>
          </div>";
    exit;
}

$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    // ตรวจสอบความถูกต้อง
    if ($username === '' || $email === '') {
        $error = "กรุณากรอกข้อมูลให้ครบถ้วน";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "รูปแบบอีเมลไม่ถูกต้อง";
    }

    // ตรวจสอบซ้ำ
    if (!$error) {
        $chk = $conn->prepare("SELECT 1 FROM users WHERE (username = ? OR email = ?) AND user_id != ?");
        $chk->execute([$username, $email, $user_id]);
        if ($chk->fetch()) {
            $error = "ชื่อผู้ใช้หรืออีเมลนี้มีอยู่แล้วในระบบ";
        }
    }

    // ตรวจรหัสผ่าน
    $updatePassword = false;
    $hashed = null;
    if (!$error && ($password !== '' || $confirm !== '')) {
        if (strlen($password) < 6) {
            $error = "รหัสผ่านต้องยาวอย่างน้อย 6 ตัวอักษร";
        } elseif ($password !== $confirm) {
            $error = "รหัสผ่านใหม่และยืนยันรหัสผ่านไม่ตรงกัน";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $updatePassword = true;
        }
    }

    // อัปเดตข้อมูล
    if (!$error) {
        if ($updatePassword) {
            $sql = "UPDATE users SET username=?, full_name=?, email=?, password=? WHERE user_id=?";
            $args = [$username, $full_name, $email, $hashed, $user_id];
        } else {
            $sql = "UPDATE users SET username=?, full_name=?, email=? WHERE user_id=?";
            $args = [$username, $full_name, $email, $user_id];
        }
        $upd = $conn->prepare($sql);
        $upd->execute($args);
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
              <script>
              Swal.fire({
                  icon:'success',
                  title:'บันทึกข้อมูลสำเร็จ!',
                  text:'ข้อมูลสมาชิกถูกอัปเดตเรียบร้อยแล้ว',
                  confirmButtonColor:'#0d6efd'
              }).then(()=>window.location='users.php');
              </script>";
        exit;
    }

    // คืนค่ากลับไปแสดงในฟอร์มกรณีมี error
    $user['username'] = $username;
    $user['full_name'] = $full_name;
    $user['email'] = $email;
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>แก้ไขข้อมูลสมาชิก</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;600&display=swap" rel="stylesheet">
<style>
body {
  background: linear-gradient(135deg, #4facfe, #f093fb);
  background-size: 400% 400%;
  animation: bgmove 12s ease infinite;
  font-family: 'Kanit', sans-serif;
  min-height: 100vh;
}
@keyframes bgmove {
  0% {background-position: 0% 50%;}
  50% {background-position: 100% 50%;}
  100% {background-position: 0% 50%;}
}
.container {
  max-width: 760px;
  padding-top: 60px;
}
.card {
  background: rgba(255, 255, 255, 0.95);
  border: none;
  border-radius: 20px;
  box-shadow: 0 15px 35px rgba(0,0,0,0.15);
  backdrop-filter: blur(10px);
  transition: transform 0.2s ease;
}
.card:hover { transform: translateY(-5px); }
.card h2 {
  font-weight: 700;
  color: #0d47a1;
  text-align: center;
  margin-bottom: 1.5rem;
}
.btn-primary {
  background: linear-gradient(135deg, #42a5f5, #1565c0);
  border: none;
  border-radius: 50px;
  padding: 10px 24px;
  font-weight: 600;
}
.btn-primary:hover {
  filter: brightness(1.1);
  box-shadow: 0 6px 18px rgba(21,101,192,0.4);
}
.btn-secondary {
  background: linear-gradient(135deg, #9e9e9e, #616161);
  border: none;
  border-radius: 50px;
  padding: 10px 24px;
}
label {
  font-weight: 600;
  color: #1565c0;
}
.alert-danger {
  border-radius: 10px;
  background: rgba(255, 77, 77, 0.1);
  border: 1px solid rgba(255,77,77,0.3);
  color: #d32f2f;
}
</style>
</head>
<body>
<div class="container">
  <div class="card p-4 shadow-lg">
    <h2><i class="fa-solid fa-user-pen me-2"></i>แก้ไขข้อมูลสมาชิก</h2>

    <a href="users.php" class="btn btn-secondary mb-3">
      <i class="fa-solid fa-arrow-left me-1"></i> กลับหน้ารายชื่อสมาชิก
    </a>

    <?php if ($error): ?>
      <div class="alert alert-danger">
        <i class="fa-solid fa-triangle-exclamation me-2"></i><?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <form method="post" class="row g-3">
      <div class="col-md-6">
        <label>ชื่อผู้ใช้</label>
        <input type="text" name="username" class="form-control" required
               value="<?= htmlspecialchars($user['username']) ?>">
      </div>
      <div class="col-md-6">
        <label>ชื่อ - นามสกุล</label>
        <input type="text" name="full_name" class="form-control"
               value="<?= htmlspecialchars($user['full_name']) ?>">
      </div>
      <div class="col-md-6">
        <label>อีเมล</label>
        <input type="email" name="email" class="form-control" required
               value="<?= htmlspecialchars($user['email']) ?>">
      </div>
      <div class="col-md-6">
        <label>รหัสผ่านใหม่ <small class="text-muted">(ถ้าไม่ต้องการเปลี่ยนให้เว้นว่าง)</small></label>
        <input type="password" name="password" class="form-control">
      </div>
      <div class="col-md-6">
        <label>ยืนยันรหัสผ่านใหม่</label>
        <input type="password" name="confirm_password" class="form-control">
      </div>
      <div class="col-12 mt-4 text-end">
        <button type="submit" class="btn btn-primary">
          <i class="fa-solid fa-floppy-disk me-1"></i> บันทึกการแก้ไข
        </button>
      </div>
    </form>
  </div>
</div>
</body>
</html>
