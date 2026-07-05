<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Nhúng file kết nối CSDL
require_once 'db_connect.php';

/** @var PDO $conn */
$search_keyword = isset($_GET['search']) ? trim($_GET['search']) : '';

// Khởi tạo 2 mảng để phân loại phim
$phim_dang_chieu = [];
$phim_sap_chieu = [];

// Lấy ngày hiện tại định dạng chuẩn của Database (YYYY-MM-DD)
$today = date('Y-m-d');

// === 1. CẤU HÌNH PHÂN TRANG ===
$limit = 8; // Số phim muốn hiển thị trên 1 trang (bạn có thể đổi thành 4, 12... tùy ý)
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page <= 0) $page = 1;
$offset = ($page - 1) * $limit;
// ===============================

try {
    if ($search_keyword != '') {
        // ĐẾM TỔNG SỐ PHIM KHI CÓ TÌM KIẾM
        $sql_count = "SELECT COUNT(*) FROM movies WHERE title LIKE :keyword";
        $stmt_count = $conn->prepare($sql_count);
        $stmt_count->execute(['keyword' => "%$search_keyword%"]);
        $total_movies = $stmt_count->fetchColumn();

        // Lấy phim có phân trang (LIMIT & OFFSET)
        $sql = "SELECT id, title, image, duration, price, release_date FROM movies WHERE title LIKE :keyword ORDER BY release_date DESC LIMIT :limit OFFSET :offset";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':keyword', "%$search_keyword%", PDO::PARAM_STR);
    } else {
        // ĐẾM TỔNG SỐ PHIM KHI KHÔNG TÌM KIẾM
        $sql_count = "SELECT COUNT(*) FROM movies";
        $stmt_count = $conn->prepare($sql_count);
        $stmt_count->execute();
        $total_movies = $stmt_count->fetchColumn();

        // Lấy phim có phân trang (LIMIT & OFFSET)
        $sql = "SELECT id, title, image, duration, price, release_date FROM movies ORDER BY release_date DESC LIMIT :limit OFFSET :offset";
        $stmt = $conn->prepare($sql);
    }

    // === 2. TÍNH TỔNG SỐ TRANG ===
    $total_pages = ceil($total_movies / $limit);

    // Bind giá trị phân trang (Bắt buộc dùng bindValue cho LIMIT/OFFSET)
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

    $stmt->execute();

    // Lấy toàn bộ dữ liệu ra
    $movies = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Bắt đầu vòng lặp để phân loại
    foreach ($movies as $movie) {
        if ($movie['release_date'] <= $today) {
            // Nếu ngày chiếu nhỏ hơn hoặc bằng hôm nay -> Đang chiếu
            $phim_dang_chieu[] = $movie;
        } else {
            // Nếu ngày chiếu lớn hơn hôm nay -> Sắp chiếu
            $phim_sap_chieu[] = $movie;
        }
    }
} catch (PDOException $e) {
    echo "<div class='alert alert-danger text-center'>Lỗi truy vấn dữ liệu: " . $e->getMessage() . "</div>";
    $movies = [];
    $total_pages = 1; // Gán mặc định 1 trang để không bị lỗi giao diện phía dưới
}
?>
<!DOCTYPE html>
<html lang="vi" data-bs-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang chủ - Nova Cinema</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body class="d-flex flex-column min-vh-100">

    <nav class="navbar navbar-expand-lg navbar-dark bg-black shadow-sm py-0 sticky-top z-3">
        <div class="container align-items-center"> <a class="navbar-brand py-1" href="index.php">
                <img src="assets/images/logo_vip.png" alt="Nova Cinema Logo" style="height: 60px;" class="d-inline-block">
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarMenu">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4 d-flex align-items-center">
                    <li class="nav-item"><a class="nav-link active" href="index.php">Trang chủ</a></li>
                    <li class="nav-item"><a class="nav-link" href="#danh-sach-phim">Phim</a></li>
                    <li class="nav-item"><a class="nav-link" href="lich-chieu.php">Lịch chiếu</a></li>
                    <li class="nav-item"><a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#quickBookModal">Đặt vé</a></li>

                    <form class="d-flex me-lg-3 my-2 my-lg-0" action="index.php" method="GET">
                        <input class="form-control me-2 rounded-pill bg-dark text-white border-secondary" type="search" name="search" placeholder="Tìm phim..." value="<?= htmlspecialchars($search_keyword) ?>">
                        <button class="btn btn-outline-warning rounded-pill px-3" type="submit">Tìm</button>
                    </form>

                    <?php if (isset($_SESSION['user_id'])): ?>
                        <div class="dropdown">
                            <button class="btn btn-outline-warning dropdown-toggle fw-bold rounded-pill px-4" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person-circle me-1"></i> <?= htmlspecialchars($_SESSION['fullname']) ?>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark shadow border-secondary mt-2 rounded-3">

                                <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] == 1): ?>
                                    <li>
                                        <a class="dropdown-item fw-bold text-warning py-2" href="admin/dashboard.php">
                                            <i class="bi bi-speedometer2 me-2"></i>Trang quản trị
                                        </a>
                                    </li>
                                    <li>
                                        <hr class="dropdown-divider border-secondary">
                                    </li>
                                <?php endif; ?>

                                <li>
                                    <a class="dropdown-item text-danger py-2" href="logout.php">
                                        <i class="bi bi-box-arrow-left me-2"></i>Đăng xuất
                                    </a>
                                </li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <div class="d-flex align-items-center">
                            <a href="login.php" class="btn btn-warning rounded-pill px-4 fw-bold me-2">Đăng nhập</a>
                            <a href="register.php" class="btn btn-outline-warning rounded-pill px-4 fw-bold">Đăng ký</a>
                        </div>
                    <?php endif; ?>
                    <button id="themeToggle" class="btn btn-outline-secondary rounded-circle ms-lg-3" style="width: 40px; height: 40px;" title="Chuyển giao diện">🌙</button>
            </div>
        </div>
    </nav>

    <section class="hero-slider bg-black">
        <div id="novaBanner" class="carousel slide carousel-fade" data-bs-ride="carousel">

            <div class="carousel-inner">
                <div class="carousel-item active">
                    <a href="movie-detail.php?id=8">
                        <img src="assets/images/movie1.jpg" class="d-block w-100 banner-img" alt="Minions">
                        <div class="banner-overlay"></div>
                    </a>
                </div>

                <div class="carousel-item">
                    <a href="movie-detail.php?id=9">
                        <img src="assets/images/movie2.jpg" class="d-block w-100 banner-img" alt="Interstellar">
                        <div class="banner-overlay"></div>
                    </a>
                </div>

                <div class="carousel-item">
                    <a href="movie-detail.php?id=1">
                        <img src="assets/images/movie3.jpg" class="d-block w-100 banner-img" alt="Oppenheimer">
                        <div class="banner-overlay"></div>
                    </a>
                </div>
            </div>

            <button class="carousel-control-prev" type="button" data-bs-target="#novaBanner" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#novaBanner" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
            </button>
        </div>
    </section>
    <!-- Danh sách phim -->

    <section class="py-4 py-md-5" id="danh-sach-phim">
        <div class="container">

            <div class="galaxy-tab-container mb-4">
                <h2 class="mb-0 fs-4 fs-md-3 galaxy-title">PHIM</h2>
                <ul class="nav nav-tabs galaxy-nav-tabs" id="phimTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="dang-chieu-tab" data-bs-toggle="tab" data-bs-target="#dang-chieu-pane" type="button" role="tab" aria-controls="dang-chieu-pane" aria-selected="true">
                            Đang chiếu
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="sap-chieu-tab" data-bs-toggle="tab" data-bs-target="#sap-chieu-pane" type="button" role="tab" aria-controls="sap-chieu-pane" aria-selected="false">
                            Sắp chiếu
                        </button>
                    </li>
                </ul>
            </div>

            <div class="tab-content" id="phimTabContent">

                <div class="tab-pane fade show active" id="dang-chieu-pane" role="tabpanel" aria-labelledby="dang-chieu-tab" tabindex="0">
                    <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-4 g-3 g-md-4">
                        <?php if (empty($phim_dang_chieu)): ?>
                            <div class="col-12 text-center py-5">
                                <h5 class="text-secondary">Hiện tại không có phim nào đang chiếu.</h5>
                            </div>
                        <?php else: ?>
                            <?php foreach ($phim_dang_chieu as $movie): ?>
                                <div class="col">
                                    <div class="card h-100 border-0 shadow-sm rounded-3 overflow-hidden movie-card-hover" onclick="window.location.href='movie-detail.php?id=<?= $movie['id'] ?>';">
                                        <img src="assets/images/<?= htmlspecialchars($movie['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($movie['title']) ?>" style="aspect-ratio: 2/3; width: 100%; object-fit: cover;">
                                        <div class="card-body p-2 p-md-3 d-flex flex-column">
                                            <h6 class="card-title text-truncate fw-bold mb-1" style="font-size: 0.9rem; max-width: 100%;"><?= htmlspecialchars($movie['title']) ?></h6>
                                            <p class="card-text text-body-secondary mb-3" style="font-size: 0.75rem;">
                                                ⏱️ <?= htmlspecialchars($movie['duration']) ?> phút <br>
                                                📅 Khởi chiếu: <?= date('d/m/Y', strtotime($movie['release_date'])) ?>
                                            </p>
                                            <div class="mt-auto d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center gap-2">
                                                <span class="text-danger fw-bold" style="font-size: 0.85rem; min-width: max-content;"><?= number_format($movie['price'], 0, ',', '.') ?>đ</span>
                                                <a href="movie-detail.php?id=<?= $movie['id'] ?>" class="btn btn-warning btn-sm fw-bold rounded px-2 py-1 w-100 w-sm-auto" style="font-size: 0.75rem;">Xem chi tiết</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <?php if ($total_pages > 1): ?>
                                <nav aria-label="Điều hướng trang phim" class="mt-5 mb-4">
                                    <ul class="pagination justify-content-center">

                                        <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                                            <a class="page-link shadow-sm" href="?page=<?= $page - 1 ?><?= isset($_GET['search']) ? '&search=' . htmlspecialchars($_GET['search']) : '' ?>" tabindex="-1">
                                                &laquo; Trước
                                            </a>
                                        </li>

                                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                            <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                                                <a class="page-link shadow-sm <?= ($page == $i) ? 'bg-warning border-warning text-dark fw-bold' : 'text-dark' ?>"
                                                    href="?page=<?= $i ?><?= isset($_GET['search']) ? '&search=' . htmlspecialchars($_GET['search']) : '' ?>">
                                                    <?= $i ?>
                                                </a>
                                            </li>
                                        <?php endfor; ?>

                                        <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                                            <a class="page-link shadow-sm" href="?page=<?= $page + 1 ?><?= isset($_GET['search']) ? '&search=' . htmlspecialchars($_GET['search']) : '' ?>">
                                                Sau &raquo;
                                            </a>
                                        </li>

                                    </ul>
                                </nav>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="tab-pane fade" id="sap-chieu-pane" role="tabpanel" aria-labelledby="sap-chieu-tab" tabindex="0">
                    <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-4 g-3 g-md-4">
                        <?php if (empty($phim_sap_chieu)): ?>
                            <div class="col-12 text-center py-5">
                                <h5 class="text-secondary">Hiện tại chưa có phim nào sắp ra mắt.</h5>
                            </div>
                        <?php else: ?>
                            <?php foreach ($phim_sap_chieu as $movie): ?>
                                <div class="col">
                                    <div class="card h-100 border-0 shadow-sm rounded-3 overflow-hidden movie-card-hover" onclick="window.location.href='movie-detail.php?id=<?= $movie['id'] ?>';">
                                        <img src="assets/images/<?= htmlspecialchars($movie['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($movie['title']) ?>" style="aspect-ratio: 2/3; width: 100%; object-fit: cover;">
                                        <div class="card-body p-2 p-md-3 d-flex flex-column">
                                            <h6 class="card-title text-truncate fw-bold mb-1" style="font-size: 0.9rem; max-width: 100%;"><?= htmlspecialchars($movie['title']) ?></h6>
                                            <p class="card-text text-body-secondary mb-3" style="font-size: 0.75rem;">
                                                ⏱️ <?= htmlspecialchars($movie['duration']) ?> phút <br>
                                                📅 Dự kiến: <span class="text-primary fw-bold"><?= date('d/m/Y', strtotime($movie['release_date'])) ?></span>
                                            </p>
                                            <div class="mt-auto d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center gap-2">
                                                <span class="text-danger fw-bold" style="font-size: 0.85rem; min-width: max-content;"><?= number_format($movie['price'], 0, ',', '.') ?>đ</span>
                                                <a href="movie-detail.php?id=<?= $movie['id'] ?>" class="btn btn-warning btn-sm fw-bold rounded px-2 py-1 w-100 w-sm-auto" style="font-size: 0.75rem;">Xem chi tiết</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <footer class="bg-body-tertiary pt-5 pb-3 mt-auto border-top">
        <div class="container">
            <div class="row gy-4 text-center text-md-start">
                <div class="col-md-4">
                    <h3 class="text-warning fw-bold mb-3">Nova Cinema</h3>
                    <p class="text-body-secondary">Nova Cinema mang đến trải nghiệm xem phim hiện đại, không gian sang trọng và dịch vụ đặt vé tiện lợi nhất.</p>
                </div>
                <div class="col-md-4">
                    <h5 class="text-warning mb-3">Liên hệ</h5>
                    <p class="mb-1 text-body-secondary">📍 BDU.Cà Mau</p>
                    <p class="mb-1 text-body-secondary">📞 0123 456 789</p>
                    <p class="text-body-secondary">✉️ novacinema@gmail.com</p>
                </div>

            </div>
            <hr class="border-secondary my-4">
            <p class="text-center text-body-secondary mb-0 small">© 2026 Nova Cinema. All Rights Reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Script Xử lý Dark/Light Mode -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const themeToggleBtn = document.getElementById('themeToggle');
            const htmlElement = document.documentElement;

            // 1. Kiểm tra dữ liệu cũ
            const savedTheme = localStorage.getItem('nova_theme');

            // 2. Thiết lập mặc định
            if (savedTheme) {
                htmlElement.setAttribute('data-bs-theme', savedTheme);
                themeToggleBtn.innerHTML = savedTheme === 'dark' ? '☀️' : '🌙';
            } else {
                // Nếu khách chưa từng vào web, ÉP mặc định là nền Sáng
                htmlElement.setAttribute('data-bs-theme', 'light');
                themeToggleBtn.innerHTML = '🌙';
                localStorage.setItem('nova_theme', 'light');
            }

            // 3. Xử lý nút bấm
            themeToggleBtn.addEventListener('click', () => {
                const currentTheme = htmlElement.getAttribute('data-bs-theme');
                const newTheme = currentTheme === 'dark' ? 'light' : 'dark';

                htmlElement.setAttribute('data-bs-theme', newTheme);
                localStorage.setItem('nova_theme', newTheme);
                themeToggleBtn.innerHTML = newTheme === 'dark' ? '☀️' : '🌙';
            });
        });
    </script>
    HTML
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const urlParams = new URLSearchParams(window.location.search);

            // NÂNG CẤP: Bổ sung thêm điều kiện urlParams.has('page')
            if (urlParams.has('search') || urlParams.has('keyword') || urlParams.has('page')) {
                const phimSection = document.getElementById('danh-sach-phim');
                if (phimSection) {
                    // Trượt êm ái xuống khu vực phim
                    setTimeout(() => {
                        phimSection.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }, 300);
                }
            }
        });
    </script>
    <?php include_once 'quick_book_modal.php'; ?>
</body>

</html>