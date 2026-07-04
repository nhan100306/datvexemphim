<?php
require_once 'db_connect.php';

// 1. Lấy tham số từ URL
$movie_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$show_date = isset($_GET['date']) ? $_GET['date'] : '';
$show_time = isset($_GET['time']) ? $_GET['time'] : '';

// 2. Truy vấn lấy thông tin Suất chiếu và Phim
$sql_info = "
    SELECT s.id AS showtime_id, s.format, m.title, m.image, m.price 
    FROM showtimes s
    JOIN movies m ON s.movie_id = m.id
    WHERE s.movie_id = :m_id AND s.show_date = :s_date AND s.show_time = :s_time
";
$stmt = $conn->prepare($sql_info);
$stmt->execute([
    'm_id' => $movie_id,
    's_date' => $show_date,
    's_time' => $show_time
]);
$showtime = $stmt->fetch(PDO::FETCH_ASSOC);

// Nếu không tìm thấy suất chiếu, đẩy về trang chủ
if (!$showtime) {
    header("Location: index.php");
    exit;
}

// 3. Lấy danh sách các ghế ĐÃ BÁN của suất chiếu này (chuẩn bị cho bước sau)
$sql_booked = "
    SELECT bs.seat_name 
    FROM booked_seats bs
    JOIN bookings b ON bs.booking_id = b.id
    WHERE b.showtime_id = :st_id
";
$stmt_booked = $conn->prepare($sql_booked);
$stmt_booked->execute(['st_id' => $showtime['showtime_id']]);
$booked_seats_raw = $stmt_booked->fetchAll(PDO::FETCH_COLUMN);
// Đưa mảng ghế đã bán sang JS để xử lý giao diện
$booked_seats_json = json_encode($booked_seats_raw);
?>

<!DOCTYPE html>
<html lang="vi" data-bs-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chọn ghế - Nova Cinema</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        /* CSS cho Sơ đồ ghế */
        .screen {
            height: 50px;
            background: linear-gradient(to bottom, #ffc107, transparent);
            transform: perspective(200px) rotateX(-5deg);
            box-shadow: 0 15px 30px rgba(255, 193, 7, 0.4);
            margin-bottom: 40px;
            border-radius: 5px 5px 0 0;
        }

        .seat {
            width: 35px;
            height: 35px;
            background-color: #e0e0e0;
            border-radius: 8px 8px 4px 4px;
            margin: 5px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: bold;
            transition: all 0.2s ease;
            color: #333;
        }

        [data-bs-theme="dark"] .seat {
            background-color: #444;
            color: #fff;
        }

        .seat:not(.occupied):hover {
            transform: scale(1.1);
            background-color: #b0c4de;
        }

        .seat.selected {
            background-color: #ffc107 !important;
            color: #000 !important;
            box-shadow: 0 0 10px rgba(255, 193, 7, 0.5);
        }

        .seat.occupied {
            background-color: #dc3545 !important;
            color: #fff !important;
            cursor: not-allowed;
            opacity: 0.6;
        }

        .seat-row-label {
            width: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }
    </style>
</head>

<body class="bg-body-tertiary">

<nav class="navbar navbar-expand-lg navbar-dark bg-black shadow-sm py-0 sticky-top">
        <div class="container align-items-center">
            <a class="navbar-brand py-1" href="index.php">
                <img src="assets/images/logo_vip.png" alt="Nova Cinema Logo" style="height: 60px;">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarMenu">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4 d-flex align-items-center">
                    <li class="nav-item"><a class="nav-link" href="index.php">Trang chủ</a></li>
                    <li class="nav-item"><a class="nav-link" href="index.php#danh-sach-phim">Phim</a></li>
                    <li class="nav-item"><a class="nav-link" href="lich-chieu.php">Lịch chiếu</a></li>
                    <li class="nav-item"><a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#quickBookModal" style="cursor: pointer;">Đặt vé</a></li>
                </ul>
                <button id="themeToggle" class="btn btn-outline-secondary rounded-circle ms-lg-3" style="width: 40px; height: 40px;" title="Chuyển giao diện">🌙</button>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <div class="row g-4">

            <div class="col-lg-8">
                <div class="bg-white p-4 rounded-3 shadow-sm border h-100">
                    <h5 class="fw-bold mb-4 text-center">MÀN HÌNH CHÍNH</h5>
                    <div class="screen w-75 mx-auto"></div>

                    <div class="d-flex flex-column align-items-center mb-4" id="seat-map">
                        <?php
                        $rows = ['A', 'B', 'C', 'D', 'E', 'F'];
                        $cols = 8;
                        foreach ($rows as $r):
                        ?>
                            <div class="d-flex justify-content-center">
                                <div class="seat-row-label"><?= $r ?></div>
                                <?php for ($c = 1; $c <= $cols; $c++):
                                    $seat_id = $r . $c;
                                ?>
                                    <div class="seat" data-seat="<?= $seat_id ?>"><?= $c ?></div>
                                <?php endfor; ?>
                                <div class="seat-row-label"><?= $r ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="d-flex justify-content-center gap-4 mt-5 pt-3 border-top">
                        <div class="d-flex align-items-center gap-2">
                            <div class="seat m-0" style="width:25px; height:25px;"></div> Ghế trống
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <div class="seat selected m-0" style="width:25px; height:25px;"></div> Đang chọn
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <div class="seat occupied m-0" style="width:25px; height:25px;"></div> Đã bán
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="bg-white p-4 rounded-3 shadow-sm border h-100 d-flex flex-column">
                    <div class="d-flex gap-3 mb-4">
                        <img src="assets/images/<?= $showtime['image'] ?>" class="rounded-3" style="width: 80px; object-fit: cover;" alt="Poster">
                        <div>
                            <h5 class="fw-bold mb-1"><?= $showtime['title'] ?></h5>
                            <span class="badge bg-danger"><?= $showtime['format'] ?></span>
                        </div>
                    </div>

                    <ul class="list-group list-group-flush mb-4">
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-secondary">Ngày chiếu:</span>
                            <span class="fw-bold"><?= date('d/m/Y', strtotime($show_date)) ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-secondary">Giờ chiếu:</span>
                            <span class="fw-bold text-primary"><?= date('H:i', strtotime($show_time)) ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-secondary">Ghế chọn:</span>
                            <span class="fw-bold text-warning" id="selected-seats-display">Chưa chọn</span>
                        </li>
                    </ul>

                    <div class="mt-auto border-top pt-3">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <span class="fs-5">Tổng tiền:</span>
                            <span class="fs-3 fw-bold text-danger" id="total-price-display">0đ</span>
                        </div>

                        <form action="process_booking.php" method="POST" id="booking-form">
                            <input type="hidden" name="showtime_id" value="<?= $showtime['showtime_id'] ?>">
                            <input type="hidden" name="selected_seats" id="input-selected-seats" value="">
                            <input type="hidden" name="total_price" id="input-total-price" value="0">

                            <button type="submit" class="btn btn-warning w-100 py-3 fw-bold fs-5 rounded-pill shadow" id="btn-checkout" disabled>
                                THANH TOÁN
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        // Dữ liệu từ PHP truyền sang
        const ticketPrice = <?= $showtime['price'] ?>;
        const bookedSeats = <?= $booked_seats_json ?>;

        const seatElements = document.querySelectorAll('.seat:not(.occupied)');
        const displaySeats = document.getElementById('selected-seats-display');
        const displayPrice = document.getElementById('total-price-display');
        const inputSeats = document.getElementById('input-selected-seats');
        const inputPrice = document.getElementById('input-total-price');
        const btnCheckout = document.getElementById('btn-checkout');

        let selectedSeats = [];

        // 1. Khởi tạo ghế đã bán
        document.querySelectorAll('.seat').forEach(seat => {
            const seatId = seat.getAttribute('data-seat');
            if (bookedSeats.includes(seatId)) {
                seat.classList.add('occupied');
            }
        });

        // 2. Xử lý logic chọn ghế
        seatElements.forEach(seat => {
            seat.addEventListener('click', () => {
                // Không cho click vào ghế đã bán
                if (seat.classList.contains('occupied')) return;

                seat.classList.toggle('selected');
                const seatId = seat.getAttribute('data-seat');

                if (seat.classList.contains('selected')) {
                    selectedSeats.push(seatId);
                } else {
                    selectedSeats = selectedSeats.filter(id => id !== seatId);
                }

                updateCheckoutPanel();
            });
        });

        // 3. Cập nhật bảng tính tiền
        function updateCheckoutPanel() {
            // Sắp xếp lại tên ghế (A1, A2...) cho đẹp
            selectedSeats.sort();

            const count = selectedSeats.length;
            const total = count * ticketPrice;

            if (count > 0) {
                displaySeats.innerText = selectedSeats.join(', ');
                displayPrice.innerText = total.toLocaleString('vi-VN') + 'đ';
                inputSeats.value = selectedSeats.join(',');
                inputPrice.value = total;
                btnCheckout.disabled = false;
            } else {
                displaySeats.innerText = 'Chưa chọn';
                displayPrice.innerText = '0đ';
                inputSeats.value = '';
                inputPrice.value = 0;
                btnCheckout.disabled = true;
            }
        }
    </script>
    <?php include_once 'quick_book_modal.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Script Xử lý Dark/Light Mode cho trang Booking
        document.addEventListener("DOMContentLoaded", function() {
            const themeToggleBtn = document.getElementById('themeToggle');
            const htmlElement = document.documentElement;
            const savedTheme = localStorage.getItem('nova_theme') || 'light';
            htmlElement.setAttribute('data-bs-theme', savedTheme);
            if (themeToggleBtn) {
                themeToggleBtn.innerHTML = savedTheme === 'dark' ? '☀️' : '🌙';
                themeToggleBtn.addEventListener('click', () => {
                    const currentTheme = htmlElement.getAttribute('data-bs-theme');
                    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                    htmlElement.setAttribute('data-bs-theme', newTheme);
                    localStorage.setItem('nova_theme', newTheme);
                    themeToggleBtn.innerHTML = newTheme === 'dark' ? '☀️' : '🌙';
                });
            }
        });
    </script>
</body>

</html>