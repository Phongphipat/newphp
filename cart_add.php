<?php
session_start();
require 'config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'กรุณาเข้าสู่ระบบก่อน']);
    exit;
}

$user_id = $_SESSION['user_id'];
$product_id = intval($_POST['product_id'] ?? 0);
if ($product_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'สินค้าไม่ถูกต้อง']);
    exit;
}

// ตรวจสอบว่าสินค้าอยู่ในตะกร้าแล้วหรือยัง
$stmt = $conn->prepare("SELECT * FROM cart WHERE user_id = ? AND product_id = ?");
$stmt->execute([$user_id, $product_id]);
$item = $stmt->fetch(PDO::FETCH_ASSOC);

if ($item) {
    $stmt = $conn->prepare("UPDATE cart SET quantity = quantity + 1 WHERE cart_id = ?");
    $stmt->execute([$item['cart_id']]);
} else {
    $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, 1)");
    $stmt->execute([$user_id, $product_id]);
}

echo json_encode(['success' => true, 'message' => 'เพิ่มสินค้าในตะกร้าเรียบร้อยแล้ว!']);
