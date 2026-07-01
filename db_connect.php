<?php
$host = 'localhost';
$dbname = 'novacinema_db'; 
$username = 'root';
$password = ''; 

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    // Thiết lập chế độ báo lỗi để dễ debug
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Kết nối thành công!"; 
} catch(PDOException $e) {
    echo "Lỗi kết nối: " . $e->getMessage();
}
?>