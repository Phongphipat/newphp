<?php

require_once '../config.php';
require_once 'auth_admin.php';

if (isset($_GET['delete'])) {
$user_id = $_GET['delete'];

if ($user_id != $_SESSION['user_id']) {
$stmt = $conn->prepare("DELETE FROM users WHERE user_id = ? AND role = 'member'");
$stmt->execute([$user_id]);
}
header("Location: users.php");
exit;
}
$stmt = $conn->prepare("SELECT * FROM users WHERE role = 'member' ORDER BY created_at DESC");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>จัดการสมาชิก</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- DataTable CSS -->
    <link href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.min.css" rel="stylesheet">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
 <style>
    /* 🌊 พื้นหลังไล่เฉดฟ้าน้ำทะเล */
    body {
        background: linear-gradient(135deg, #00b4db, #0083b0, #00aaff);
        background-size: 400% 400%;
        animation: gradient-animation 15s ease infinite;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        min-height: 100vh;
        color: #0f172a;
    }

    @keyframes gradient-animation {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }

    /* 🧊 กล่อง Card โปร่งใสหรู */
    .card {
        backdrop-filter: blur(12px);
        background: rgba(255, 255, 255, 0.92);
        border: none;
        border-radius: 20px;
        box-shadow: 0 20px 40px rgba(0, 150, 255, 0.25);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .card:hover {
        transform: translateY(-6px);
        box-shadow: 0 25px 50px rgba(0, 170, 255, 0.35);
    }

    /* 🔹 ปุ่มสีเทา (secondary) */
    .btn-secondary {
        background: linear-gradient(90deg, #6dd5fa, #2980b9);
        border: none;
        color: white;
        transition: all 0.3s ease;
        box-shadow: 0 4px 10px rgba(41, 128, 185, 0.4);
    }

    .btn-secondary:hover {
        background: linear-gradient(90deg, #56ccf2, #2f80ed);
        box-shadow: 0 6px 16px rgba(47, 128, 237, 0.5);
        transform: translateY(-2px);
    }

    /* 💙 ปุ่ม warning → ฟ้าอ่อน */
    .btn-warning {
        background: linear-gradient(90deg, #4facfe, #00f2fe);
        border: none;
        color: #fff;
        font-weight: 500;
        box-shadow: 0 4px 10px rgba(0, 200, 255, 0.4);
        transition: all 0.3s ease;
    }

    .btn-warning:hover {
        background: linear-gradient(90deg, #3a8df5, #00d4ff);
        box-shadow: 0 6px 18px rgba(0, 150, 255, 0.6);
        transform: translateY(-2px);
    }

    /* 🔵 ปุ่ม danger → น้ำเงินเข้ม */
    .btn-danger {
        background: linear-gradient(90deg, #0072ff, #00c6ff);
        border: none;
        color: #fff;
        font-weight: 500;
        box-shadow: 0 4px 10px rgba(0, 123, 255, 0.4);
        transition: all 0.3s ease;
    }

    .btn-danger:hover {
        background: linear-gradient(90deg, #005ce6, #00aaff);
        box-shadow: 0 6px 18px rgba(0, 100, 255, 0.6);
        transform: translateY(-2px);
    }
</style>

</head>
<body class="container mt-4">
    <div class="card shadow-lg p-4">
        <h2><i class="fas fa-users-cog me-2"></i>จัดการสมาชิก</h2>
        <a href="index.php" class="btn btn-secondary mb-3"><i class="fas fa-arrow-left me-1"></i>กลับหน้าผู้ดูแล</a>
        <?php if (count($users) === 0): ?>
        <div class="alert alert-warning"><i class="fas fa-exclamation-triangle me-2"></i>ยังไม่มีสมาชิกในระบบ</div>
        <?php else: ?>
        <div class="table-responsive">
            <table id="productTable" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>ชื่อผู้ใช้</th>
                        <th>ชื่อ-นามสกุล</th>
                        <th>อีเมล</th>
                        <th>วันที่สมัคร</th>
                        <th>จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['username']) ?></td>
                        <td><?= htmlspecialchars($user['full_name']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td><?= $user['created_at'] ?></td>
                        <td>
                            <a href="edit_user.php?id=<?= $user['user_id'] ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i> แก้ไข</a>
                            <!-- <a href="#" class="btn btn-sm btn-danger delete-btn" data-bs-toggle="modal" data-bs-target="#deleteModal" data-user-id="<?= $user['user_id'] ?>">
                                <i class="fas fa-trash-alt"></i> ลบ
                            </a> -->
                            <form action="del_sweet.php" method="POST" style="display:inline;">
                                <input type="hidden" name="u_id" value="<?php echo $user['user_id']; ?>">
                                <button type="button" class="delete-button btn btn-danger btn-sm " data-user-id="<?php echo
                                $user['user_id']; ?>">ลบ</button>
                            </form>

                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
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
                    คุณต้องการลบสมาชิกคนนี้หรือไม่?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <a id="deleteUserLink" href="#" class="btn btn-danger">ยืนยันการลบ</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const deleteModal = document.getElementById('deleteModal');
            const deleteUserLink = document.getElementById('deleteUserLink');

            if (deleteModal) {
                deleteModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    const userId = button.getAttribute('data-user-id');
                    deleteUserLink.href = 'users.php?delete=' + userId;
                });
            }
            
            let table = new DataTable('#productTable');
        });
    </script>

    <script>
        // ฟังกช์ นั ส ำหรับแสดงกลอ่ งยนื ยัน SweetAlert2
        function showDeleteConfirmation(userId) {
            Swal.fire({
            title: 'คุณแน่ใจหรือไม่?',
            text: 'คุณจะไม่สามารถเรียกข้อมูลกลับได้!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'ลบ',
            cancelButtonText: 'ยกเลิก',
            }).then((result) => {
            if (result.isConfirmed) {
            // หำกผใู้ชย้นื ยัน ใหส้ ง่ คำ่ ฟอรม์ ไปยัง delete.php เพื่อลบข ้อมูล
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'delUser_Sweet.php';
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'u_id';
            input.value = userId;
            form.appendChild(input);
            document.body.appendChild(form);
            form.submit();
            }
            });
            }
            // แนบตัวตรวจจับเหตุกำรณ์คลิกกับองค์ปุ ่่มลบทั ่ ้งหมดที่มีคลำส delete-button
            const deleteButtons = document.querySelectorAll('.delete-button');
            deleteButtons.forEach((button) => {
            button.addEventListener('click', () => {
            const userId = button.getAttribute('data-user-id');
            showDeleteConfirmation(userId);
            });
        });
    </script>

</body>
</html>
