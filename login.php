<?php
    session_start();
    require_once 'config.php';
    $error = '';
    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        $usernameOrEmail = trim($_POST['username_or_email']);            
        $password = $_POST['password'];

        $sql = "SELECT * FROM users WHERE (username = ? OR email = ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$usernameOrEmail, $usernameOrEmail]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if($user && password_verify($password, $user['password'])){

            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            if($user['role'] === 'admin'){
                header("Location: admin/index.php");
            }else{
                header("Location: index.php");
            }
            exit();
        }else {
            $error = "ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง";
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<style>
  body {
      background: linear-gradient(135deg, #4a90e2, #1565c0);
      min-height: 100vh;
      font-family: 'Kanit', sans-serif;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
  }

  .card {
      backdrop-filter: blur(12px);
      background: rgba(255, 255, 255, 0.9);
      border: none;
      border-radius: 24px;
      box-shadow: 0 12px 32px rgba(0, 0, 0, 0.15);
      transition: all 0.35s ease;
      overflow: hidden;
  }
  .card:hover {
      transform: translateY(-8px) scale(1.02);
      box-shadow: 0 20px 48px rgba(0, 0, 0, 0.25);
  }

  .card-header {
      background: linear-gradient(135deg, #4a90e2, #1565c0) !important;
      color: #fff;
      font-weight: 700;
      font-size: 1.2rem;
      text-align: center;
      padding: 16px;
      border-top-left-radius: 24px !important;
      border-top-right-radius: 24px !important;
  }

  .card-body {
      padding: 2rem;
      color: #333;
  }

  .btn-custom {
      border-radius: 50px;
      padding: 10px 20px;
      font-weight: 600;
      background: linear-gradient(135deg, #4a90e2, #1565c0);
      border: none;
      color: #fff;
      box-shadow: 0 6px 14px rgba(74, 144, 226, 0.4);
      transition: all 0.3s ease;
  }
  .btn-custom:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 18px rgba(21, 101, 192, 0.5);
      filter: brightness(1.08);
  }

  .card-title {
      font-size: 1.5rem;
      font-weight: 700;
      color: #1565c0;
      margin-bottom: 1rem;
  }

  .card-text {
      font-size: 1rem;
      line-height: 1.6;
      color: #555;
  }
</style>


</head>
<body>
    <div class="container d-flex align-items-center justify-content-center min-vh-100 py-4">
        <div class="col-lg-4 col-md-6 col-sm-8">
            <div class="card shadow-lg">
                <div class="card-header text-center text-white py-4">
                    <i class="fas fa-sign-in-alt fa-2x mb-2"></i>
                    <h2 class="mb-0">เข้าสู่ระบบ</h2>
                </div>
                <div class="card-body p-4">
                    <?php if (isset($_GET['register']) && $_GET['register'] === 'success'): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i>สมัครสมาชิกสำเร็จ กรุณาเข้าสู่ระบบ
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($error)): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i><?= htmlspecialchars($error) ?>
                    </div>
                    <?php endif; ?>

                    <form method="post">
                        <div class="mb-3">
                            <label for="username_or_email" class="form-label">ชื่อผู้ใช้หรืออีเมล</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fas fa-user text-danger"></i>
                                </span>
                                <input type="text" name="username_or_email" id="username_or_email" class="form-control border-start-0" placeholder="ชื่อผู้ใช้หรืออีเมล" required>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label for="password" class="form-label">รหัสผ่าน</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fas fa-lock text-danger"></i>
                                </span>
                                <input type="password" name="password" id="password" class="form-control border-start-0" placeholder="รหัสผ่าน" required>
                            </div>
                        </div>
                        
                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-danger btn-lg">
                                <i class="fas fa-sign-in-alt me-2"></i>เข้าสู่ระบบ
                            </button>
                        </div>
                        
                        <hr class="my-4">
                        
                        <div class="text-center">
                            <a href="register.php" class="btn btn-outline-danger">
                                <i class="fas fa-user-plus me-1"></i>สมัครสมาชิก
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>