<?php
// Nhúng file kết nối CSDL
require_once 'db_connect.php';

/** @var PDO $conn */
$search_keyword = ''; // Khai báo biến rỗng để Navbar không báo lỗi

// 1. Lấy ID phim từ URL an toàn
$movie_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$movie = null;

if ($movie_id > 0) {
    try {
        $stmt = $conn->prepare("SELECT * FROM movies WHERE id = :id");
        $stmt->execute(['id' => $movie_id]);
        $movie = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("DB Error: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="vi" data-bs-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $movie ? htmlspecialchars($movie['title']) : 'Chi tiết phim' ?> - Nova Cinema</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .movie-detail-section {
            overflow: hidden;
            min-height: 80vh;
            display: flex;
            align-items: center;
        }

        .movie-blur-bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-size: cover;
            background-position: center 20%;
            filter: blur(25px);
            transform: scale(1.15);
            z-index: 0;
        }

        .movie-blur-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            /* Lớp phủ đen mờ 85% */
            background: rgba(15, 15, 15, 0.85);
            z-index: 1;
        }
    </style>
</head>

<body class="d-flex flex-column min-vh-100">

    <!-- Thanh điều hướng Navbar (Giống hệt trang chủ) -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-black shadow-sm py-0 sticky-top" style="z-index: 9999;">
        <div class="container align-items-center">
            <a class="navbar-brand py-1" href="index.php">
                <img src="assets/images/logo_vip.png" alt="Nova Cinema Logo" style="height: 60px;" class="d-inline-block">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarMenu">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4 d-flex align-items-center">
                    <li class="nav-item"><a class="nav-link" href="index.php">Trang chủ</a></li>
                    <li class="nav-item"><a class="nav-link active" href="index.php#danh-sach-phim">Phim</a></li>
                    <li class="nav-item"><a class="nav-link" href="lich-chieu.php">Lịch chiếu</a></li>
                    <li class="nav-item"><a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#quickBookModal">Đặt vé</a></li>
                </ul>
                <form class="d-flex me-lg-3 my-2 my-lg-0" action="index.php" method="GET">
                    <input class="form-control me-2 rounded-pill bg-dark text-white border-secondary" type="search" name="search" placeholder="Tìm phim...">
                    <button class="btn btn-outline-warning rounded-pill px-3" type="submit">Tìm</button>
                </form>
                <a href="admin/login.php" class="btn btn-warning fw-bold rounded-pill px-4">Đăng nhập</a>
                <button id="themeToggle" class="btn btn-outline-secondary rounded-circle ms-lg-3" style="width: 40px; height: 40px;" title="Chuyển giao diện">🌙</button>
            </div>
        </div>
    </nav>

    <!-- Nội dung trang chi tiết -->
    <?php if (!$movie): ?>
        <!-- Trường hợp không tìm thấy ID phim -->
        <div class="container text-center py-5 my-5 flex-grow-1">
            <h2 class="text-danger fw-bold">Không tìm thấy bộ phim này!</h2>
            <p class="text-body-secondary mb-4">Phim bạn đang tìm có thể đã bị xóa hoặc đường dẫn không chính xác.</p>
            <a href="index.php" class="btn btn-warning rounded-pill px-4 fw-bold">⬅ Quay lại trang chủ</a>
        </div>
    <?php else: ?>
        <!-- Trường hợp có phim: Render giao diện chuẩn Điện ảnh -->
        <main class="flex-grow-1">
            <!-- Phần Banner làm mờ phía sau -->
            <section class="movie-detail-section position-relative py-5">

                <!-- Lớp 1: Nền ảnh mờ phía sau cùng (CSS sẽ lo phần blur) -->
                <div class="movie-blur-bg" style="background-image: url('assets/images/<?= htmlspecialchars($movie['image']) ?>');"></div>

                <!-- Lớp 2: Phủ màu đen trong suốt lên trên ảnh -->
                <div class="movie-blur-overlay"></div>

                <!-- Lớp 3: Nội dung chính (Đổi z-1 thành z-3 để nổi lên trên cùng) -->
                <div class="container py-4 position-relative z-3 text-white">
                    <div class="row gy-4 align-items-center">

                        <!-- Cột bên trái: Poster phim (Tỷ lệ chuẩn 2:3) -->
                        <div class="col-md-4 col-lg-3 text-center">
                            <img src="assets/images/<?= htmlspecialchars($movie['image']) ?>"
                                class="img-fluid rounded-4 shadow-lg border border-secondary border-opacity-25"
                                alt="<?= htmlspecialchars($movie['title']) ?>"
                                style="aspect-ratio: 2/3; width: 100%; max-width: 300px; object-fit: cover;">
                        </div>

                        <!-- Cột bên phải: Thông tin chi tiết -->
                        <div class="col-md-8 col-lg-9">
                            <?php
                            $today_check = date('Y-m-d');
                            if ($movie['release_date'] > $today_check):
                            ?>
                                <span class="badge bg-primary text-white mb-2 px-3 py-2 fw-bold fs-6">🍿 Phim Sắp Ra Mắt</span>
                            <?php else: ?>
                                <span class="badge bg-warning text-dark mb-2 px-3 py-2 fw-bold fs-6">🎬 Phim Đang Chiếu</span>
                            <?php endif; ?>
                            <h1 class="display-4 fw-bold text-warning mb-3"><?= htmlspecialchars($movie['title']) ?></h1>

                            <!-- Các thẻ thông tin nhanh -->
                            <div class="d-flex flex-wrap gap-4 mb-4 text-light opacity-75">
                                <div>⏱️ <strong>Thời lượng:</strong> <?= htmlspecialchars($movie['duration']) ?> phút</div>
                                <div>📅 <strong>Khởi chiếu:</strong> <?= date('d/m/Y', strtotime($movie['release_date'])) ?></div>
                                <?php if (!empty($movie['genre'])): ?>
                                    <div>🏷️ <strong>Thể loại:</strong> <?= htmlspecialchars($movie['genre']) ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Nội dung phim -->
                            <h5 class="text-warning fw-bold mb-2">Nội dung phim:</h5>
                            <p class="fs-5 text-light opacity-75 mb-4" style="line-height: 1.8; text-align: justify;">
                                <?= !empty($movie['description']) ? nl2br(htmlspecialchars($movie['description'])) : 'Nội dung bộ phim đang được chúng tôi cập nhật...' ?>
                            </p>

                            <!-- Khung giá vé và Nút đặt -->
                            <div class="d-flex flex-wrap align-items-center gap-4 pt-3 border-top border-secondary border-opacity-50">
                                <div>
                                    <small class="text-light opacity-50 d-block">Giá vé chỉ từ</small>
                                    <span class="text-danger fw-bold fs-2"><?= number_format($movie['price'], 0, ',', '.') ?> VNĐ</span>
                                </div>
                                <button class="btn btn-warning fw-bold px-4 py-2 rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#quickBookModal" onclick="preselectMovie(<?= $movie['id'] ?>)">
                                    🎟️ĐẶT VÉ NGAY
                                </button>
                            </div>
                        </div>

                    </div>
                </div>
            </section>

            <!-- Phần Lịch Chiếu Phim bên dưới -->
            <section class="py-5 bg-body-tertiary">
                <div class="container"> <!-- Thêm container để canh giữa nội dung không bị tràn viền -->
                    <div class="mt-4">
                        <?php
                        // Đảm bảo $movie và $conn đã được khai báo ở phần trên của file movie-detail.php
                        $today = date('Y-m-d');
                        $release_date = $movie['release_date'];
                        $current_movie_id = $movie['id'];

                        // 1. Xác định ngày lấy lịch chiếu
                        $target_date = ($release_date > $today) ? $release_date : $today;

                        // 2. Truy vấn tự động vào bảng showtimes
                        $sql_showtimes = "SELECT show_time, format, is_hot FROM showtimes 
                              WHERE movie_id = :movie_id AND show_date = :target_date 
                              ORDER BY show_time ASC";
                        $stmt_st = $conn->prepare($sql_showtimes);
                        $stmt_st->execute([
                            'movie_id' => $current_movie_id,
                            'target_date' => $target_date
                        ]);
                        $lich_chieu = $stmt_st->fetchAll(PDO::FETCH_ASSOC);

                        // 3. Xử lý hiển thị Tiêu đề
                        $is_coming_soon = ($release_date > $today);
                        $title_text = $is_coming_soon ? "Lịch Đặt Trước" : "Lịch Chiếu Hôm Nay";
                        $border_color = $is_coming_soon ? "border-primary" : "border-warning";
                        $text_color = $is_coming_soon ? "text-primary" : "";
                        ?>

                        <h4 class="fw-bold border-start border-4 <?= $border_color ?> ps-2 mb-4 <?= $text_color ?>">
                            <?= $title_text ?>
                        </h4>

                        <div class="bg-white p-4 rounded-3 shadow-sm border <?= $is_coming_soon ? 'border-primary' : '' ?>">
                            <?php if ($is_coming_soon): ?>
                                <p class="text-secondary mb-3">
                                    Phim khởi chiếu vào <strong><?= date('d/m/Y', strtotime($release_date)) ?></strong>. Chọn suất chiếu sớm nhất để đặt chỗ:
                                </p>
                            <?php else: ?>
                                <p class="text-secondary mb-3">Chọn suất chiếu phù hợp với bạn tại <strong>Nova Cinema BDU</strong>:</p>
                            <?php endif; ?>

                            <div class="d-flex flex-wrap gap-2">
                                <?php if (empty($lich_chieu)): ?>
                                    <p class="text-danger mb-0">Hiện tại chưa có suất chiếu nào được cập nhật.</p>
                                <?php else: ?>
                                    <?php foreach ($lich_chieu as $suat):
                                        $time_formatted = date('H:i', strtotime($suat['show_time']));

                                        // Setup style cho nút
                                        if ($suat['is_hot'] == 1) {
                                            $btn_class = $is_coming_soon ? "btn btn-primary fw-bold shadow-sm" : "btn btn-warning fw-bold border-0";
                                            $hot_text = " (Hot)";
                                        } else {
                                            $btn_class = $is_coming_soon ? "btn btn-outline-primary" : "btn btn-outline-dark";
                                            $hot_text = "";
                                        }
                                    ?>
                                        <a href="booking.php?id=<?= $current_movie_id ?>&date=<?= $target_date ?>&time=<?= $time_formatted ?>&format=<?= urlencode($suat['format']) ?>"
                                            class="<?= $btn_class ?> px-4 py-2">
                                            <?= $time_formatted ?> - <?= htmlspecialchars($suat['format']) ?><?= $hot_text ?>
                                        </a>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        <?php endif; ?>

        </main>
        <?php include_once 'quick_book_modal.php'; ?>
        <script>
            function preselectMovie(movieId) {
                // Đợi 200ms để Popup kịp mở ra rồi mới thao tác
                setTimeout(() => {
                    const selectMovie = document.getElementById('qb-movie');
                    if (selectMovie) {
                        selectMovie.value = movieId;
                        // Kích hoạt sự kiện 'change' để hệ thống tự động tải Ngày chiếu tương ứng
                        selectMovie.dispatchEvent(new Event('change'));
                    }
                }, 200);
            }
        </script>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
        <!-- Script Xử lý Dark/Light Mode -->
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const themeToggleBtn = document.getElementById('themeToggle');
                const htmlElement = document.documentElement;

                const savedTheme = localStorage.getItem('nova_theme');
                if (savedTheme) {
                    htmlElement.setAttribute('data-bs-theme', savedTheme);
                    themeToggleBtn.innerHTML = savedTheme === 'dark' ? '☀️' : '🌙';
                } else {
                    htmlElement.setAttribute('data-bs-theme', 'light');
                    themeToggleBtn.innerHTML = '🌙';
                    localStorage.setItem('nova_theme', 'light');
                }

                themeToggleBtn.addEventListener('click', () => {
                    const currentTheme = htmlElement.getAttribute('data-bs-theme');
                    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';

                    htmlElement.setAttribute('data-bs-theme', newTheme);
                    localStorage.setItem('nova_theme', newTheme);
                    themeToggleBtn.innerHTML = newTheme === 'dark' ? '☀️' : '🌙';
                });
            });
        </script>
</body>

</html>