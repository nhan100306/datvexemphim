<?php
session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 1) {
    header("Location: ../login.php");
    exit;
}

require_once '../db_connect.php';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int)$_GET['id'];
    try {
        $sql = "DELETE FROM showtimes WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['id' => $id]);
        
        // Quay về trang danh sách (Kèm thông báo nếu muốn làm sau)
        header("Location: showtimes.php?msg=delete_success");
        exit;
    } catch (PDOException $e) {
        // Lỗi khóa ngoại: Suất chiếu này đã có người đặt vé, không được xóa
        echo "<script>alert('Không thể xóa! Đã có khách hàng mua vé cho suất chiếu này.'); window.location.href='showtimes.php';</script>";
        exit;
    }
}
header("Location: showtimes.php");
exit;
?>