<?php
session_start();
require_once 'db_connect.php'; // Kiểm tra lại đường dẫn file này cho đúng nhé
$msg = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // 1. Kiểm tra nhập đủ dữ liệu
    if (empty($username) || empty($fullname) || empty($email) || empty($password)) {
        $msg = "<div class='alert alert-danger text-center'>Vui lòng nhập đầy đủ thông tin!</div>";
    }
    // 2. Kiểm tra mật khẩu khớp nhau
    elseif ($password !== $confirm_password) {
        $msg = "<div class='alert alert-danger text-center'>Mật khẩu nhập lại không khớp!</div>";
    } else {
        // 3. Kiểm tra xem Username hoặc Email đã có ai dùng chưa
        $stmt_check = $conn->prepare("SELECT id FROM users WHERE username = :username OR email = :email");
        $stmt_check->execute(['username' => $username, 'email' => $email]);

        if ($stmt_check->rowCount() > 0) {
            $msg = "<div class='alert alert-danger text-center'>Tên đăng nhập hoặc Email đã tồn tại!</div>";
        } else {
            // 4. Mã hóa mật khẩu và lưu vào Database
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $role = 0; // Mặc định role = 0 (Thành viên thường)

            try {
                $sql = "INSERT INTO users (username, password, fullname, email, phone, role) 
            VALUES (:username, :password, :fullname, :email, :phone, :role)";

                $stmt = $conn->prepare($sql);
                $stmt->execute([
                    'username' => $username,
                    'password' => $hashed_password,
                    'fullname' => $fullname,
                    'email'    => $email,
                    'phone'    => $phone, 
                    'role'     => 0
                ]);

                echo "<script>alert('Đăng ký thành công!'); window.location.href = 'login.php';</script>";
                exit;
            } catch (PDOException $e) {
                $msg = "<div class='alert alert-danger text-center'>Lỗi SQL: " . $e->getMessage() . "</div>";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Đăng ký tài khoản - Nova Cinema</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Tùy chỉnh CSS giao diện giống hệt form login của bạn */
        body {
            background-color: #f8f1f1;
            /* Màu nền đen hoặc thay bằng ảnh nền giống login.php */
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        .register-box {
            background-color: #242428;
            border-radius: 10px;
            padding: 40px;
            width: 100%;
            max-width: 500px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        }

        .form-control {
            background-color: #ececf4;
            border: 1px solid #555;
            color: #fff;
        }

        .form-control:focus {
            background-color: #444;
            color: #fff;
            border-color: #ffc107;
            box-shadow: none;
        }
    </style>
</head>

<body>

    <div class="register-box">
        <div class="text-center mb-4">
            <img src="assets/images/logo_vip.png" alt="Logo" style="height: 50px;">
            <h4 class="mt-3 fw-bold">ĐĂNG KÝ HỆ THỐNG</h4>
        </div>

        <?= $msg ?>

        <form action="register.php" method="POST">
            <div class="mb-3">
                <label class="form-label text-light" style="font-size: 14px;">Họ và tên</label>
                <input type="text" name="fullname" class="form-control py-2" placeholder="Nhập họ và tên..." required>
            </div>
            <div class="mb-3">
                <label class="form-label text-light" style="font-size: 14px;">Email</label>
                <input type="email" name="email" class="form-control py-2" placeholder="Nhập email..." required>
            </div>
            <div class="mb-3">
                <label class="form-label text-light" style="font-size: 14px;">Số điện thoại</label>
                <input type="text" name="phone" class="form-control py-2" placeholder="Nhập số điện thoại..." required>
            </div>
            <div class="mb-3">
                <label class="form-label text-light" style="font-size: 14px;">Tên đăng nhập</label>
                <input type="text" name="username" class="form-control py-2" placeholder="Nhập tên tài khoản..." required>
            </div>
            <div class="mb-3">
                <label class="form-label text-light" style="font-size: 14px;">Mật khẩu</label>
                <input type="password" name="password" class="form-control py-2" placeholder="Nhập mật khẩu..." required>
            </div>
            <div class="mb-4">
                <label class="form-label text-light" style="font-size: 14px;">Xác nhận mật khẩu</label>
                <input type="password" name="confirm_password" class="form-control py-2" placeholder="Nhập lại mật khẩu..." required>
            </div>

            <button type="submit" class="btn btn-warning w-100 fw-bold py-2 fs-5 text-dark rounded-3">ĐĂNG KÝ</button>

            <div class="mt-4 text-center">
                <span class="text-light" style="opacity: 0.7;">Đã có tài khoản?</span>
                <a href="login.php" class="text-warning text-decoration-none fw-bold ms-1">Đăng nhập</a>
            </div>
            <div class="mt-3 text-center">
                <a href="index.php" class="text-secondary text-decoration-none" style="font-size: 13px;">&larr; Quay lại trang chủ</a>
            </div>
        </form>
    </div>

</body>

</html>