<?php
require_once '../config.php';
require_once 'auth_admin.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ผู้ดูแลระบบ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
      body {
          background: linear-gradient(-45deg, #0d47a1, #1565c0, #1e88e5, #42a5f5);
          background-size: 400% 400%;
          animation: gradient-animation 20s ease infinite;
          font-family: 'Kanit', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
          min-height: 100vh;
          display: flex;
          align-items: center;
          justify-content: center;
          padding: 20px;
      }
      @keyframes gradient-animation {
          0% { background-position: 0% 50%; }
          50% { background-position: 100% 50%; }
          100% { background-position: 0% 50%; }
      }
      .admin-card {
          background: rgba(255, 255, 255, 0.9);
          backdrop-filter: blur(14px);
          border-radius: 26px;
          box-shadow: 0 12px 32px rgba(0,0,0,0.12);
          padding: 2rem;
          max-width: 720px;
          width: 100%;
          text-align: center;
      }
      .admin-card h2 {
          font-weight: 700;
          margin-bottom: .5rem;
          color: #1565c0;
      }
      .admin-card p {
          margin-bottom: 2rem;
          color: #444;
      }
      .dashboard-grid {
          display: grid;
          grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
          gap: 20px;
          margin-bottom: 1.5rem;
      }
      .dash-btn {
          display: flex;
          flex-direction: column;
          align-items: center;
          justify-content: center;
          border-radius: 18px;
          padding: 20px;
          font-weight: 600;
          color: #fff;
          text-decoration: none;
          transition: all 0.3s ease;
      }
      .dash-btn i {
          font-size: 2rem;
          margin-bottom: 10px;
      }
      .dash-btn:hover {
          transform: translateY(-5px);
          box-shadow: 0 10px 24px rgba(0,0,0,0.25);
      }
      /* ปุ่มโทน */
      .btn-users    { background: linear-gradient(135deg, #ffca28, #ffb300); color:#212121; }
      .btn-category { background: linear-gradient(135deg, #424242, #212121); }
      .btn-products { background: linear-gradient(135deg, #42a5f5, #1565c0); }
      .btn-orders   { background: linear-gradient(135deg, #43a047, #2e7d32); }
      .logout-btn {
          display: inline-block;
          border-radius: 999px;
          padding: 12px 24px;
          background: #6c757d;
          color: #fff;
          text-decoration: none;
          transition: all 0.3s ease;
      }
      .logout-btn:hover {
          background: #5a6268;
          transform: translateY(-2px);
      }
    </style>
</head>
<body>
    <div class="admin-card">
        <h2><i class="fas fa-tools me-2"></i>ระบบผู้ดูแลระบบ</h2>
        <p>ยินดีต้อนรับ, <?= htmlspecialchars($_SESSION['username']) ?></p>
        
        <div class="dashboard-grid">
            <a href="users.php" class="dash-btn btn-users">
                <i class="fas fa-users-cog"></i>
                จัดการสมาชิก
            </a>
            <a href="category.php" class="dash-btn btn-category">
                <i class="fas fa-sitemap"></i>
                จัดการหมวดหมู่
            </a>
            <a href="products.php" class="dash-btn btn-products">
                <i class="fas fa-box"></i>
                จัดการสินค้า
            </a>
            <a href="orders.php" class="dash-btn btn-orders">
                <i class="fas fa-receipt"></i>
                จัดการคำสั่งซื้อ
            </a>
        </div>
        
        <a href="../logout.php" class="logout-btn"><i class="fas fa-sign-out-alt me-2"></i>ออกจากระบบ</a>
    </div>
</body>
</html>
