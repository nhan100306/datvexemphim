<?php
// BẮT BUỘC PHẢI CÓ DÒNG NÀY ĐỂ LẤY TÊN TỪ SESSION
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'db_connect.php';

$success = false;
$error_msg = '';
$booking_id = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $showtime_id = isset($_POST['showtime_id']) ? (int)$_POST['showtime_id'] : 0;
    $selected_seats = isset($_POST['selected_seats']) ? $_POST['selected_seats'] : '';
    $total_price = isset($_POST['total_price']) ? (int)$_POST['total_price'] : 0;
    
    // Lấy số điện thoại (nếu có form truyền sang, không có thì để N/A)
   $customer_phone = isset($_SESSION['phone']) ? $_SESSION['phone'] : 'Chưa cập nhật';

    // Tự động lấy tên khách hàng từ Session
    $customer_name = isset($_SESSION['fullname']) ? $_SESSION['fullname'] : 'Khách vãng lai';

    if ($showtime_id > 0 && !empty($selected_seats)) {
        try {
            // Bắt đầu quá trình lưu (Transaction)
            $conn->beginTransaction();

            // 1. Lưu Hóa đơn vào bảng bookings
            $sql_booking = "INSERT INTO bookings (showtime_id, customer_name, customer_phone, total_price) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql_booking);
            $stmt->execute([$showtime_id, $customer_name, $customer_phone, $total_price]);

            // Lấy ID của hóa đơn vừa tạo
            $booking_id = $conn->lastInsertId();

            // 2. Lưu từng ghế vào bảng booked_seats
            $seats_array = explode(',', $selected_seats);
            $sql_seat = "INSERT INTO booked_seats (booking_id, seat_name) VALUES (?, ?)";
            $stmt_seat = $conn->prepare($sql_seat);

            foreach ($seats_array as $seat) {
                $stmt_seat->execute([$booking_id, trim($seat)]);
            }

            // Hoàn tất lưu dữ liệu
            $conn->commit();
            $success = true;
        } catch (Exception $e) {
            $conn->rollBack();
            $error_msg = "Có lỗi xảy ra trong quá trình đặt vé: " . $e->getMessage();
        }
    } else {
        $error_msg = "Dữ liệu không hợp lệ. Vui lòng chọn lại ghế.";
    }
} else {
    // Nếu ai đó cố tình gõ URL truy cập thẳng vào trang này thì đuổi về trang chủ
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kết quả đặt vé - Nova Cinema</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .ticket-box {
            background: linear-gradient(135deg, #222, #000);
            border-radius: 15px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.3);
            position: relative;
            overflow: hidden;
            border: 2px dashed #ffc107;
        }
        .ticket-circle-left,
        .ticket-circle-right {
            position: absolute;
            top: 50%;
            width: 40px;
            height: 40px;
            background-color: #f8f9fa;
            border-radius: 50%;
            transform: translateY(-50%);
        }
        .ticket-circle-left {
            left: -20px;
        }
        .ticket-circle-right {
            right: -20px;
        }
        [data-bs-theme="dark"] .ticket-circle-left,
        [data-bs-theme="dark"] .ticket-circle-right {
            background-color: #212529;
        }
    </style>
</head>
<body class="bg-body-tertiary d-flex align-items-center min-vh-100">

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">

                <?php if ($success): ?>
                    <div class="text-center mb-4">
                        <h2 class="text-success fw-bold">🎉 ĐẶT VÉ THÀNH CÔNG!</h2>
                        <p class="text-secondary">Cảm ơn bạn đã lựa chọn Nova Cinema</p>
                    </div>

                    <div class="ticket-box text-white p-4 p-md-5 mb-4">
                        <div class="ticket-circle-left"></div>
                        <div class="ticket-circle-right"></div>

                        <div class="text-center border-bottom border-secondary pb-3 mb-3">
                            <h4 class="text-warning fw-bold mb-0">VÉ XEM PHIM</h4>
                            <small class="text-white-50">Mã hóa đơn: #NV-<?= str_pad($booking_id, 5, '0', STR_PAD_LEFT) ?></small>
                        </div>

                        <div class="row g-3 fs-5">
                            <div class="col-6 text-white-50">Khách hàng:</div>
                            <div class="col-6 fw-bold text-end"><?= htmlspecialchars($customer_name) ?></div>

                            <div class="col-6 text-white-50">Ghế đã chọn:</div>
                            <div class="col-6 fw-bold text-end text-warning"><?= htmlspecialchars($selected_seats) ?></div>

                            <div class="col-6 text-white-50">Tổng tiền:</div>
                            <div class="col-6 fw-bold text-end text-danger fs-4"><?= number_format($total_price, 0, ',', '.') ?>đ</div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="text-center mb-4">
                        <h2 class="text-danger fw-bold">❌ ĐẶT VÉ THẤT BẠI!</h2>
                        <p class="text-secondary"><?= $error_msg ?></p>
                    </div>
                <?php endif; ?>

                <div class="text-center mt-5">
                    <a href="index.php" class="btn btn-outline-secondary px-4 me-2">Về trang chủ</a>
                    <a href="lich-chieu.php" class="btn btn-warning fw-bold px-4">Mua thêm vé</a>
                </div>

            </div>
        </div>
    </div>
</body>
</html>