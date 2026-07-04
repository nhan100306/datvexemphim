<?php
require_once 'db_connect.php';

// 1. Lấy ngày từ URL, nếu không có thì mặc định lấy ngày hôm nay (2026-07-04)
$selected_date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
$timestamp = strtotime($selected_date);

$days_vn = ['Chủ Nhật', 'Thứ Hai', 'Thứ Ba', 'Thứ Tư', 'Thứ Năm', 'Thứ Sáu', 'Thứ Bảy'];
$today_name = $days_vn[date('w', $timestamp)];
$today_date = date('d/m/Y', $timestamp);
$db_date = date('Y-m-d', $timestamp);

// 2. Truy vấn Database lấy lịch chiếu theo ngày được chọn
$sql_schedule = "
    SELECT m.id AS movie_id, m.title, m.duration, s.show_time, s.format, s.is_hot 
    FROM movies m
    JOIN showtimes s ON m.id = s.movie_id
    WHERE s.show_date = :today
    ORDER BY m.id ASC, s.show_time ASC
";
$stmt = $conn->prepare($sql_schedule);
$stmt->execute(['today' => $db_date]);
$all_showtimes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 3. Gom nhóm lịch chiếu theo phim
$movies_schedule = [];
foreach ($all_showtimes as $row) {
    $m_id = $row['movie_id'];
    if (!isset($movies_schedule[$m_id])) {
        $movies_schedule[$m_id] = [
            'id' => $m_id,
            'title' => $row['title'],
            'duration' => $row['duration'],
            'shows' => []
        ];
    }
    $movies_schedule[$m_id]['shows'][] = [
        'time' => date('H:i', strtotime($row['show_time'])),
        'format' => $row['format'],
        'is_hot' => $row['is_hot']
    ];
}
?>
<!DOCTYPE html>
<html lang="vi" data-bs-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lịch Chiếu Phim - Nova Cinema</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .date-btn {
            min-width: 90px;
            transition: all 0.2s ease;
        }
    </style>
</head>

<body class="d-flex flex-column min-vh-100 bg-body-tertiary">

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
                    <li class="nav-item"><a class="nav-link active" href="lich-chieu.php">Lịch chiếu</a></li>
                    <li class="nav-item"><a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#quickBookModal">Đặt vé</a></li>
                </ul>
                <button id="themeToggle" class="btn btn-outline-secondary rounded-circle ms-lg-3" style="width: 40px; height: 40px;">🌙</button>
            </div>
        </div>
    </nav>

    <main class="flex-grow-1">
        <div class="container py-5 mt-2">

            <h3 class="fw-bold mb-4 border-start border-4 border-warning ps-3">LỊCH CHIẾU PHIM</h3>

            <div class="d-flex flex-wrap gap-2 mb-4 bg-white p-3 rounded-3 shadow-sm border">
                <?php
                $start = new DateTime('2026-07-04');
                $end = new DateTime('2026-07-11'); // Tới ngày 11 để lấy hết ngày 10
                $interval = new DateInterval('P1D');
                $period = new DatePeriod($start, $interval, $end);

                foreach ($period as $dt) {
                    $date_string = $dt->format('Y-m-d');
                    $date_display = $dt->format('d/m');
                    $w = $dt->format('w');

                    // Đổi tên hiển thị ngày hôm nay cho thân thiện
                    $day_label = ($date_string == date('Y-m-d')) ? "Hôm nay" : $days_vn[$w];

                    // Nếu ngày đang lặp trùng với ngày người dùng đang xem -> Tô màu vàng active
                    $is_active = ($date_string == $db_date);
                    $btn_class = $is_active ? "btn btn-warning fw-bold text-dark shadow-sm" : "btn btn-outline-dark";
                ?>
                    <a href="lich-chieu.php?date=<?= $date_string ?>" class="btn <?= $btn_class ?> date-btn text-center py-2">
                        <small class="d-block opacity-75" style="font-size: 0.75rem;"><?= $day_label ?></small>
                        <span class="fs-5"><?= $date_display ?></span>
                    </a>
                <?php
                }
                ?>
            </div>

            <div class="d-inline-block bg-dark text-warning fw-bold px-4 py-2 mb-0 rounded-top border-bottom border-warning border-3" style="font-size: 1.1rem;">
                Lịch chiếu: <?= $today_name ?> - <?= $today_date ?>
            </div>

            <div class="bg-white rounded-3 shadow-sm overflow-hidden border border-secondary border-opacity-25">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="min-width: 800px;">
                        <tbody>
                            <?php if (empty($movies_schedule)): ?>
                                <tr>
                                    <td class="text-center py-5 text-muted">
                                        <p class="fs-5 mb-1">Hiện tại rạp chưa xếp lịch chiếu cho ngày này.</p>
                                        <small>Vui lòng chọn các ngày khác trên thanh điều hướng phía trên.</small>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php $stt = 1;
                                foreach ($movies_schedule as $movie): ?>
                                    <tr class="border-bottom">
                                        <td class="bg-light text-center fw-bold text-secondary" style="width: 5%; font-size: 1.2rem;">
                                            <?= $stt++ ?>
                                        </td>
                                        <td class="ps-4" style="width: 35%;">
                                            <div class="d-flex align-items-center">
                                                <span class="badge bg-danger rounded-1 me-3 fs-6 px-2 py-1 shadow-sm">2D</span>
                                                <div>
                                                    <a href="movie-detail.php?id=<?= $movie['id'] ?>" class="text-decoration-none text-body fw-bold" style="font-size: 1.1rem;">
                                                        <?= htmlspecialchars($movie['title']) ?>
                                                    </a>
                                                    <div class="text-muted mt-1" style="font-size: 0.85rem;">
                                                        <span class="fw-bold text-secondary border border-secondary px-1 rounded me-2">C13</span>
                                                        ⏱️ <?= $movie['duration'] ?> phút
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="pe-3" style="width: 60%;">
                                            <div class="d-flex flex-wrap gap-2 py-3">
                                                <?php foreach ($movie['shows'] as $suat):
                                                    $btn_class = ($suat['is_hot'] == 1) ? "btn btn-outline-danger btn-sm fw-bold" : "btn btn-outline-secondary btn-sm fw-bold text-dark";
                                                ?>
                                                    <a href="booking.php?id=<?= $movie['id'] ?>&date=<?= $db_date ?>&time=<?= $suat['time'] ?>"
                                                        class="<?= $btn_class ?> px-3 py-1" style="background-color: #fcfcfc;">
                                                        <?= $suat['time'] ?>
                                                    </a>
                                                <?php endforeach; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
    <?php include_once 'quick_book_modal.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script>
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