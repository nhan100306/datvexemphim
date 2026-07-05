<?php
session_start();
// Xóa toàn bộ dữ liệu Session hiện tại
session_unset();
session_destroy();

// Đá văng về trang chủ rạp phim
header("Location: index.php");
exit;
?>