<?php
session_start();

// Hàng rào bảo vệ Admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 1) {
    header("Location: ../login.php");
    exit;
}

require_once '../db_connect.php';

// Kiểm tra xem có nhận được ID thành viên từ URL không
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int)$_GET['id'];

    // CƠ CHẾ BẢO VỆ: Tuyệt đối không cho phép Admin tự xóa chính tài khoản đang đăng nhập
    if ($id === $_SESSION['user_id']) {
        header("Location: members.php?msg=self_delete_error");
        exit;
    }

    try {
        // Thực hiện lệnh xóa tài khoản trong Database
        $sql = "DELETE FROM users WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['id' => $id]);
        
        // Nếu xóa thành công
        header("Location: members.php?msg=delete_success");
        exit;
    } catch (PDOException $e) {
        // Lỗi này thường xảy ra nếu thành viên này đã có dữ liệu đặt vé ở bảng `bookings` (Ràng buộc khóa ngoại)
        header("Location: members.php?msg=foreign_key_error");
        exit;
    }
}

// Nếu không có ID hợp lệ, quay về trang danh sách
header("Location: members.php");
exit;
?>