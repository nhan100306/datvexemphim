<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nova Cinema</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
    <div class="container">

        <!-- Brand -->
        <a class="navbar-brand fw-bold" href="index.php">
            Nova Cinema
        </a>

        <!-- Nút menu trên điện thoại -->
        <button class="navbar-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarMenu"
                aria-controls="navbarMenu"
                aria-expanded="false"
                aria-label="Toggle navigation">

            <span class="navbar-toggler-icon"></span>

        </button>

        <!-- Menu -->
        <div class="collapse navbar-collapse" id="navbarMenu">

            <ul class="navbar-nav ms-auto">

                <li class="nav-item">
                    <a class="nav-link active" href="index.php">Trang chủ</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="#">Phim</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="#">Lịch chiếu</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="#">Khuyến mãi</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="#">Liên hệ</a>
                </li>

            </ul>

            <a href="#" class="btn btn-warning ms-lg-3">
                Đăng nhập
            </a>

        </div>

    </div>
</nav>
<section class="movie-detail">

    <div class="detail-container">

        <div class="movie-poster">

            <img src="assets/images/movie1.jpg" alt="Interstellar">

        </div>

        <div class="movie-info">

            <h1>Interstellar</h1>

            <p><strong>Thể loại:</strong> Khoa học viễn tưởng</p>

            <p><strong>Ngày khởi chiếu:</strong> 07/11/2014</p>

            <p><strong>Thời lượng:</strong> 169 phút</p>

            <p><strong>Giá vé:</strong> 90.000 VNĐ</p>

            <h2>Nội dung phim</h2>

            <p>
                Trong tương lai, Trái Đất đang dần không còn thích hợp để sinh sống.
                Một nhóm phi hành gia được giao nhiệm vụ khám phá những hành tinh mới
                nhằm tìm nơi ở cho nhân loại.
            </p>

            <a href="#" class="btn-detail">
                Đặt vé ngay
            </a>

        </div>

    </div>

</section>
<footer>

    <div class="footer-container">

        <div class="footer-about">

            <h2>Nova Cinema</h2>

            <p>
                Nova Cinema mang đến trải nghiệm xem phim hiện đại,
                đặt vé nhanh chóng và tiện lợi.
            </p>

        </div>

        <div class="footer-contact">

            <h3>Liên hệ</h3>

            <p>📍 Đầm Dơi, Cà Mau</p>

            <p>📞 0123 456 789</p>

            <p>✉️ novacinema@gmail.com</p>

        </div>

        <div class="footer-social">

            <h3>Theo dõi chúng tôi</h3>

            <a href="#">Facebook</a>
            <a href="#">Instagram</a>
            <a href="#">YouTube</a>

        </div>

    </div>

    <hr>

    <p class="copyright">
        © 2026 Nova Cinema. All Rights Reserved.
    </p>

</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>