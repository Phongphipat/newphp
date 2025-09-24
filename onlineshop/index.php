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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>หน้าหลัก</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
       body {
    background: linear-gradient(-45deg, #0d47a1, #1565c0, #1e88e5, #42a5f5);
    background-size: 400% 400%;
    animation: gradient-animation 18s ease infinite;
    font-family: 'Kanit','Segoe UI',Tahoma,Geneva,Verdana,sans-serif;
    min-height: 100vh;
}
@keyframes gradient-animation {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

/* card สินค้า */
.card {
    backdrop-filter: blur(12px);
    background: rgba(255,255,255,0.95);
    border: none;
    border-radius: 22px;
    box-shadow: 0 10px 28px rgba(21,101,192,0.18);
    transition: all 0.3s ease;
}
.card:hover {
    transform: translateY(-6px);
    box-shadow: 0 16px 40px rgba(21,101,192,0.25);
}

/* ส่วนหัว */
h1 {
    color: #fff;
    font-weight: 700;
    text-shadow: 0 3px 6px rgba(0,0,0,0.3);
}

/* ปุ่ม */
.btn-success {
    background: linear-gradient(135deg,#43a047,#2e7d32);
    border: none;
    color: #fff;
    transition: all .3s ease;
}
.btn-success:hover {
    filter: brightness(1.1);
    box-shadow: 0 6px 18px rgba(46,125,50,0.4);
}
.btn-primary {
    background: linear-gradient(135deg,#42a5f5,#1565c0);
    border: none;
    color: #fff;
}
.btn-primary:hover {
    filter: brightness(1.1);
    box-shadow: 0 6px 18px rgba(21,101,192,0.4);
}
.btn-outline-primary {
    border-color: #1565c0;
    color: #1565c0;
}
.btn-outline-primary:hover {
    background-color: #1565c0;
    color: #fff;
}

/* ป้าย badge */
.badge-top-left {
    position: absolute;
    top: .6rem; left: .6rem;
    border-radius: .4rem;
    font-weight: 600;
    padding: .35rem .6rem;
    font-size: .75rem;
}

/* meta + title */
.product-meta { 
    font-size: .75rem; 
    letter-spacing:.05em; 
    color:#607d8b; 
    text-transform: uppercase;
}
.product-title {
    font-size: 1rem;
    margin:.25rem 0 .5rem;
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

/* rating ดาว */
.rating i { color:#ffc107; }

/* wishlist icon */
.wishlist { color:#90a4ae; transition:.2s; }
.wishlist:hover { color:#1565c0; }

/* success popup */
.success-message {
    position: fixed;
    top: 2rem;
    left: 50%;
    transform: translateX(-50%);
    padding: 1rem 2rem;
    background: #43a047;
    color: white;
    border-radius: 999px;
    box-shadow: 0 6px 12px rgba(0,0,0,0.2);
    opacity: 0;
    transition: opacity 0.5s ease-in-out;
    z-index: 1000;
}
.success-message.show { opacity: 1; }

    </style>
</head>
<body class="container mt-4">
    <div id="success-message" class="success-message">
        <i class="fa-solid fa-check-circle mr-2"></i>เพิ่มสินค้าลงในตะกร้าแล้ว!
    </div>
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>รายการสินค้า</h1>
            <div>
                <?php if ($isLoggedIn): ?>
                    <span class="me-3">ยินดีต้อนรับ, <?= htmlspecialchars($_SESSION['username']) ?> (<?=
                    $_SESSION['role'] ?>)</span>
                    <a href="profile.php" class="btn btn-info">ข้อมูลส่วนตัว</a>
                    <a href="cart.php" class="btn btn-warning">ดูตะกร้า</a>
                    <a href="logout.php" class="btn btn-secondary">ออกจากระบบ</a>
                    <?php else: ?>
                    <a href="login.php" class="btn btn-success">เข้าสู่ระบบ</a>
                    <a href="register.php" class="btn btn-primary">สมัครสมาชิก</a>
                    <?php endif; ?>
            </div>
    </div>
    <!-- <div class="row">
        <?php foreach ($products as $product): ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($product['product_name']) ?></h5>
                            <h6 class="card-subtitle mb-2 text-muted"><?= htmlspecialchars($product['category_name'])
                                ?></h6>
                                <p class="card-text"><?= nl2br(htmlspecialchars($product['description'])) ?></p>
                                    <p><strong>ราคา:</strong> <?= number_format($product['price'], 2) ?> บาท</p>
                                        <?php if ($isLoggedIn): ?>
                                            <form action="cart.php" method="post" class="d-inline" onsubmit="showSuccessMessage(event)">
                                                <input type="hidden" name="product_id" value="<?= $product['product_id']?>">
                                                <input type="hidden" name="quantity" value="1">
                                                <button type="submit" class="btn btn-sm btn-success">เพิ่มในตะกร้า</button>
                                            </form>
                                    <?php else: ?>
                                <small class="text-muted">เข้าสู่ระบบเพื่อสั่งซื้อ</small>
                            <?php endif; ?>
                        <a href="product_detail.php?id=<?= $product['product_id'] ?>" class="btn btn-sm btn-outline-primary float-end">ดูรายละเอียด</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div> -->
    <div class="row g-4"> <!-- EDIT C -->
        <?php foreach ($products as $p): ?>
        <!-- TODO==== เตรียมรูป / ตกแต่ง badge / ดำวรีวิว ==== -->
        <?php
        // เตรียมรูป
        $img = !empty($p['image'])
        ? 'product_images/' . rawurlencode($p['image'])
        : 'product_images/no-image.jpg';
        // ตกแต่ง badge: NEW ภำยใน 7 วัน / HOT ถ ้ำสต็อกน้อยกว่ำ 5
        $isNew = isset($p['created_at']) && (time() - strtotime($p['created_at']) <= 7*24*3600);
        $isHot = (int)$p['stock'] > 0 && (int)$p['stock'] < 5;
        // ดำวรีวิว (ถ ้ำไม่มีใน DB จะโชว์ 4.5 จ ำลอง; ถ ้ำมี $p['rating'] ให้แทน)
        $rating = isset($p['rating']) ? (float)$p['rating'] : 4.5;
        $full = floor($rating); // จ ำนวนดำวเต็ม (เต็ม 1 ดวง) , floor ปัดลง
        $half = ($rating - $full) >= 0.5 ? 1 : 0; // มีดำวครึ่งดวงหรือไม่
        ?>
        <div class="col-12 col-sm-6 col-lg-3"> <!-- EDIT C -->
        <div class="card product-card h-100 position-relative"> <!-- EDIT C -->
        <!-- TODO====check $isNew / $isHot ==== -->
        <?php if ($isNew): ?>
        <span class="badge bg-success badge-top-left">NEW</span>
        <?php elseif ($isHot): ?>
        <span class="badge bg-danger badge-top-left">HOT</span>
        <?php endif; ?>
        <!-- TODO====show Product images ==== -->
        <a href="product_detail.php?id=<?= (int)$p['product_id'] ?>" class="p-3 d-block">
        <img src="<?= htmlspecialchars($img) ?>"
        alt="<?= htmlspecialchars($p['product_name']) ?>"
        class="img-fluid w-100 product-thumb">
        </a>
        <div class="px-3 pb-3 d-flex flex-column"> <!-- EDIT C -->
        <!-- TODO====div for category, heart ==== -->
        <div class="d-flex justify-content-between align-items-center mb-1">
        <div class="product-meta">
        <?= htmlspecialchars($p['category_name'] ?? 'Category') ?>
        </div>
        <button class="btn btn-link p-0 wishlist" title="Add to wishlist" type="button">
        <i class="bi bi-heart"></i>
        </button>
        </div>
        <!-- TODO====link, div for product name ==== -->
        <a class="text-decoration-none" href="product_detail.php?id=<?= (int)$p['product_id'] ?>">
        <div class="product-title">
        <?= htmlspecialchars($p['product_name']) ?>
        </div>
        </a>
        <!-- TODO====div for rating ==== -->
        <!-- ดำวรีวิว -->
        <div class="rating mb-2">
        <?php for ($i=0; $i<$full; $i++): ?><i class="bi bi-star-fill"></i><?php endfor; ?>
        <?php if ($half): ?><i class="bi bi-star-half"></i><?php endif; ?>
        <?php for ($i=0; $i<5-$full-$half; $i++): ?><i class="bi bi-star"></i><?php endfor; ?>
        </div>
        <!-- TODO====div for price ==== -->
        <div class="price mb-3">
        <?= number_format((float)$p['price'], 2) ?> บาท
        </div>
        <!-- TODO====div for button check login ==== -->
        <div class="mt-auto d-flex gap-2">
        <?php if ($isLoggedIn): ?>
        <form action="cart.php" method="post" class="d-inline-flex gap-2">
        <input type="hidden" name="product_id" value="<?= (int)$p['product_id'] ?>">
        <input type="hidden" name="quantity" value="1">
        <button type="submit" class="btn btn-sm btn-success">เพิ่มในตะกร้า</button>
        </form>
        <?php else: ?>
        <small class="text-muted">เข้าสู่ระบบเพื่อสั่งซื้อ</small>
        <?php endif; ?>
        <a href="product_detail.php?id=<?= (int)$p['product_id'] ?>"
        class="btn btn-sm btn-outline-primary ms-auto">ดูรายละเอียด</a>
        </div>
        </div>
        </div>
        </div>
        <?php endforeach; ?>
        </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function showSuccessMessage(event) {
            event.preventDefault();
            const message = document.getElementById('success-message');
            message.classList.add('show');
            setTimeout(() => {
                message.classList.remove('show');
                event.target.submit();
            }, 2000);
        }
    </script>
</body>
</html>
