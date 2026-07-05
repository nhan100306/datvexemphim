<?php
session_start();
require_once 'db_connect.php';

// Nếu người dùng đã đăng nhập rồi thì không cho vào trang này nữa
if (isset($_SESSION['user_role'])) {
    if ($_SESSION['user_role'] == 1) {
        header("Location: admin/dashboard.php");
    } else {
        header("Location: index.php");
    }
    exit;
}

$error_msg = '';

// Xử lý khi người dùng bấm nút Đăng nhập
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (!empty($username) && !empty($password)) {
        try {
            // Tìm tài khoản trong database
            $sql = "SELECT * FROM users WHERE username = :username LIMIT 1";
            $stmt = $conn->prepare($sql);
            $stmt->execute(['username' => $username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                $is_password_correct = false;

                // TH1: Kiểm tra bằng password_verify đối với mật khẩu đã mã hóa của tài khoản mới
                if (password_verify($password, $user['password'])) {
                    $is_password_correct = true;
                } 
                // TH2: Châm chước kiểm tra thô đối với tài khoản admin cũ ("admin123")
                elseif ($password === $user['password']) {
                    $is_password_correct = true;
                }

                // Nếu rơi vào 1 trong 2 trường hợp hợp lệ trên
                if ($is_password_correct) {
                    // Đăng nhập thành công -> Lưu thông tin vào Session
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['fullname'] = $user['fullname'];
                    $_SESSION['user_role'] = $user['role'];

                    // PHÂN LUỒNG TỰ ĐỘNG BẰNG ROLE
                    if ($user['role'] == 1) {
                        // Trùm cuối (Admin) -> Bay vào bảng điều khiển
                        header("Location: admin/dashboard.php");
                    } else {
                        // Thành viên thường -> Bay ra trang chủ xem phim
                        header("Location: index.php");
                    }
                    exit;
                } else {
                    $error_msg = "Tài khoản hoặc mật khẩu không chính xác!";
                }
            } else {
                $error_msg = "Tài khoản hoặc mật khẩu không chính xác!";
            }
        } catch (PDOException $e) {
            $error_msg = "Lỗi hệ thống: " . $e->getMessage();
        }
    } else {
        $error_msg = "Vui lòng nhập đầy đủ tài khoản và mật khẩu!";
    }
}
?>

<!DOCTYPE html>
<html lang="vi" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - Nova Cinema</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            /* Nền tối mang phong cách rạp chiếu phim */
            background: linear-gradient(rgba(0, 0, 0, 0.8), rgba(0, 0, 0, 0.8)), url('assets/images/movie2.jpg') no-repeat center center/cover;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: rgba(33, 37, 41, 0.95);
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.5);
            border: 1px solid #444;
            backdrop-filter: blur(10px);
        }
        .form-control {
            background-color: rgba(255, 255, 255, 0.1);
            border: 1px solid #555;
            color: #fff;
        }
        .form-control:focus {
            background-color: rgba(255, 255, 255, 0.15);
            border-color: #ffc107;
            box-shadow: 0 0 0 0.25rem rgba(255, 193, 7, 0.25);
            color: #fff;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="login-card p-4 p-md-5">
                    
                    <div class="text-center mb-4">
                        <a href="index.php">
                            <img src="assets/images/logo_vip.png" alt="Nova Cinema Logo" style="height: 70px;">
                        </a>
                        <h4 class="mt-3 text-white fw-bold">ĐĂNG NHẬP HỆ THỐNG</h4>
                    </div>

                    <?php if ($error_msg != ''): ?>
                        <div class="alert alert-danger text-center shadow-sm">
                            <?= $error_msg ?>
                        </div>
                    <?php endif; ?>

                    <form action="login.php" method="POST">
                        <div class="mb-3">
                            <label class="form-label text-white-50">Tên đăng nhập</label>
                            <input type="text" name="username" class="form-control form-control-lg" placeholder="Nhập tài khoản..." required autofocus>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label text-white-50">Mật khẩu</label>
                            <input type="password" name="password" class="form-control form-control-lg" placeholder="Nhập mật khẩu..." required>
                        </div>

                        <button type="submit" class="btn btn-warning w-100 py-3 fw-bold fs-5 mb-3 rounded-pill shadow">
                            ĐĂNG NHẬP
                        </button>
                    </form>
                    
                    <div class="text-center">
                        <a href="index.php" class="text-secondary text-decoration-none">&larr; Quay lại trang chủ</a>
                    </div>

                </div>
            </div>
        </div>
    </div>

</body>
</html>