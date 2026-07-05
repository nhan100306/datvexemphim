<?php
session_start();

// Hàng rào bảo vệ
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 1) {
    header("Location: ../login.php");
    exit;
}

require_once '../db_connect.php';

// Kiểm tra xem có nhận được ID của phim cần xóa từ URL không
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int)$_GET['id'];

    try {
        // BƯỚC 1: Lấy tên file ảnh để xóa khỏi thư mục (Tránh làm đầy bộ nhớ Server)
        $sql_get_img = "SELECT image FROM movies WHERE id = :id";
        $stmt_img = $conn->prepare($sql_get_img);
        $stmt_img->execute(['id' => $id]);
        $movie = $stmt_img->fetch(PDO::FETCH_ASSOC);

        // Nếu phim có tồn tại và ảnh không phải ảnh mặc định thì tiến hành xóa file ảnh
        if ($movie && !empty($movie['image']) && $movie['image'] != 'default_movie.jpg') {
            $image_path = "../assets/images/" . $movie['image'];
            if (file_exists($image_path)) {
                unlink($image_path); // Lệnh unlink của PHP dùng để xóa file vật lý
            }
        }

        // BƯỚC 2: Xóa dữ liệu phim trong Database
        $sql_delete = "DELETE FROM movies WHERE id = :id";
        $stmt_delete = $conn->prepare($sql_delete);
        $stmt_delete->execute(['id' => $id]);

    } catch (PDOException $e) {
        // Ghi log lỗi nếu cần thiết (Hiện tại để trống để tự động quay về trang danh sách)
    }
}

// Xóa xong thì lập tức đá người dùng quay lại trang danh sách phim
header("Location: movies.php");
exit;
?>