-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 04, 2026 at 10:03 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `novacinema_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `movie_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `total_price` int(11) NOT NULL,
  `booking_date` datetime DEFAULT current_timestamp(),
  `status` tinyint(4) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `movies`
--

CREATE TABLE `movies` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `release_date` date NOT NULL,
  `duration` int(11) NOT NULL,
  `price` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `movies`
--

INSERT INTO `movies` (`id`, `title`, `description`, `image`, `release_date`, `duration`, `price`) VALUES
(1, 'Thám tử lừng danh Conan: Thiên Thần Sa Ngã Trên Xa Lộ', 'Conan cùng Ran, Sonoko, đội thám tử nhí và Sera Masumi đến Yokohama tham dự Lễ hội Moto Kanagawa. Một tay lái bí ẩn mang biệt danh \"Quái Xế Đen\" xuất hiện, kéo Conan và nữ cảnh sát Chihaya Hagiwara vào cuộc điều tra đầy kịch tính liên quan đến mẫu mô tô công nghệ cao \"Angel\" và một vụ án nhiều năm trước.', 'conan.jpg', '2026-07-24', 109, 90000),
(2, 'ĐỀN LA SÁT', 'Nhóm sinh viên quốc tế thực hiện khảo sát tại một ngôi đền bỏ hoang ở Kobe, Nhật Bản. Khi một thành viên đột nhiên phát điên và mất tích bí ẩn, người quản lý dự án Yu-mi phải nhờ đến pháp sư Myung-jin để đối đầu với thế lực tà ác đang ẩn náu trong ngôi đền và khám phá bí mật kinh hoàng phía sau lời nguyền La Sát.', 'denlasat.jpg', '2026-07-03', 96, 95000),
(3, 'QUỶ BẮT HỒN', 'Một gia đình tưởng chừng có cuộc sống bình yên bỗng bị cuốn vào chuỗi hiện tượng siêu nhiên đáng sợ. Những tiếng gọi bí ẩn giữa đêm, những bóng người xuất hiện rồi biến mất và lời nguyền \"Đúng tháng đúng ngày, hồn về theo quỷ\" khiến họ phải đối mặt với thế lực tà ác để bảo vệ những người mình yêu thương.', 'quybathon.jpg', '2026-07-10', 103, 90000),
(4, 'NGƯỜI NHỆN: KHỞI ĐẦU MỚI', 'Sau khi cả thế giới quên mất danh tính của Peter Parker, anh quyết định sống như một Người Nhện thực thụ, đơn độc bảo vệ thành phố New York. Khi một mối đe dọa bí ẩn mới xuất hiện và sức mạnh của bản thân có dấu hiệu thay đổi, Peter phải đối mặt với thử thách lớn nhất từ trước đến nay để bảo vệ những người mình yêu quý.', 'spider.jpg', '2026-07-31', 145, 95000),
(5, 'ĐỒNG DAO MA QUÁI\r\n', 'Một nhóm học sinh cá biệt được đưa đến ngôi chùa bỏ hoang để rèn luyện kỷ luật. Tại đây, họ vô tình tìm thấy một bàn cờ cổ bị phong ấn. Mỗi lần gieo xúc xắc, một thế lực ma quỷ trong truyền thuyết Thái Lan được giải phóng, kéo cả nhóm vào trò chơi sinh tử nơi chỉ có cách phá bỏ lời nguyền mới có thể sống sót.', 'dongdao.jpg', '2026-07-03', 123, 95000),
(6, 'THỎ ƠI!!', 'Lấy cảm hứng từ những câu chuyện có thật, Thỏ Ơi!! kể về những góc khuất của tình yêu và hôn nhân, nơi những bí mật dần được hé lộ và những \"chiếc mặt nạ\" của con người lần lượt bị gỡ bỏ. Bộ phim pha trộn giữa tâm lý, hài hước và cảm xúc, mang đến nhiều bất ngờ cho khán giả.', 'thooi.jpg', '2026-02-17', 127, 95000),
(7, 'BÓNG QUỶ', 'Trong một cộng đồng Cơ Đốc giáo bảo thủ tại một thị trấn hẻo lánh ở Úc, hai thiếu niên Naim và Ryan bị ép tham gia liệu pháp \"chuyển đổi\". Một thế lực tà ác được đánh thức, có khả năng hóa thân thành người mà nạn nhân yêu thương hoặc tin tưởng nhất, đẩy họ vào cuộc chiến sinh tồn giữa đức tin, nỗi sợ và sự thật.', 'bongquy.jpg', '2026-07-03', 88, 85000),
(8, 'MINIONS & QUÁI VẬT', 'Các Minions mang tham vọng chinh phục Hollywood bằng cách tự thực hiện một bộ phim quái vật. Tuy nhiên, kế hoạch nhanh chóng trở thành thảm họa khi chúng vô tình giải phóng những quái vật kỳ dị ra khắp thế giới. Với sự hài hước quen thuộc, nhóm Minions phải hợp sức để cứu lấy hành tinh khỏi chính mớ hỗn loạn mà mình gây ra.', 'minions.jpg', '2026-07-01', 90, 95000),
(9, 'NGÀY CON SỐNG LẠI', 'Sau một tai nạn bí ẩn, một người mẹ bất ngờ nhận được cơ hội gặp lại đứa con đã qua đời thông qua một hiện tượng khoa học chưa từng có. Khi ranh giới giữa ký ức, thực tại và tương lai dần bị xóa nhòa, cô phải đưa ra lựa chọn đau đớn giữa việc níu giữ quá khứ hay chấp nhận sự thật để tiếp tục sống.', 'ngaycon.jpg', '2026-07-03', 126, 95000),
(10, 'MẸ ƠI VỀ NHÀ', 'Bộ phim kể về hành trình đoàn tụ của một gia đình sau nhiều năm xa cách, mang đến những khoảnh khắc cảm động về tình mẫu tử và giá trị của mái ấm.', 'meoi.jpg', '2026-07-10', 115, 100000),
(11, 'MƯA ĐỎ', 'Bộ phim lịch sử - chiến tranh lấy cảm hứng từ sự kiện 81 ngày đêm chiến đấu bảo vệ Thành cổ Quảng Trị năm 1972. Phim khắc họa lòng dũng cảm, sự hy sinh của những người lính trẻ và khát vọng hòa bình của dân tộc.', 'muado.jpg', '2025-08-22', 124, 99000),
(12, 'NHÀ MÌNH ĐI THÔI', 'Bộ phim gia đình - hài kể về Phương, một nữ startup trẻ buộc phải tổ chức chuyến du lịch giả tạo cho gia đình bất ổn của mình để giành khoản đầu tư. Trên hành trình ấy, những mâu thuẫn dần được hóa giải và các thành viên tìm lại sự gắn kết.', 'nhaminh.jpg', '2026-02-26', 115, 80000);

-- --------------------------------------------------------

--
-- Table structure for table `showtimes`
--

CREATE TABLE `showtimes` (
  `id` int(11) NOT NULL,
  `movie_id` int(11) NOT NULL,
  `show_date` date NOT NULL,
  `show_time` time NOT NULL,
  `format` varchar(50) NOT NULL DEFAULT '2D Sub',
  `is_hot` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `showtimes`
--

INSERT INTO `showtimes` (`id`, `movie_id`, `show_date`, `show_time`, `format`, `is_hot`) VALUES
(1, 2, '2026-07-04', '10:00:00', '2D Sub', 0),
(2, 2, '2026-07-04', '14:15:00', '2D Sub', 0),
(3, 2, '2026-07-04', '20:30:00', '2D Sub', 1),
(4, 5, '2026-07-04', '09:30:00', '2D Sub', 0),
(5, 5, '2026-07-04', '13:45:00', '2D Sub', 0),
(6, 5, '2026-07-04', '19:00:00', '2D Sub', 0),
(7, 6, '2026-07-04', '08:30:00', '2D Sub', 0),
(8, 6, '2026-07-04', '15:00:00', '2D Sub', 0),
(9, 6, '2026-07-04', '21:00:00', '2D Sub', 1),
(10, 7, '2026-07-04', '11:00:00', '2D Sub', 0),
(11, 7, '2026-07-04', '16:30:00', '2D Sub', 0),
(12, 7, '2026-07-04', '22:15:00', '2D Sub', 0),
(13, 8, '2026-07-04', '09:00:00', '2D Lồng Tiếng', 0),
(14, 8, '2026-07-04', '14:00:00', '2D Lồng Tiếng', 0),
(15, 8, '2026-07-04', '18:30:00', '3D Lồng Tiếng', 1),
(16, 9, '2026-07-04', '10:30:00', '2D Sub', 0),
(17, 9, '2026-07-04', '17:15:00', '2D Sub', 0),
(18, 9, '2026-07-04', '19:45:00', '2D Sub', 1),
(19, 1, '2026-07-24', '18:00:00', '2D Lồng Tiếng', 0),
(20, 1, '2026-07-24', '20:00:00', '2D Sub', 1),
(21, 3, '2026-07-10', '19:00:00', '2D Sub', 0),
(22, 3, '2026-07-10', '21:30:00', '2D Sub', 1),
(23, 4, '2026-07-31', '18:30:00', '3D IMAX', 0),
(24, 4, '2026-07-31', '20:45:00', '3D IMAX', 1),
(25, 10, '2026-07-10', '17:30:00', '2D Sub', 0),
(26, 10, '2026-07-10', '19:45:00', '2D Sub', 1),
(29, 11, '2026-07-04', '18:40:00', '2D Sub', 0),
(30, 11, '2026-07-04', '17:00:00', '2D Sub', 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role` tinyint(4) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `movie_id` (`movie_id`);

--
-- Indexes for table `movies`
--
ALTER TABLE `movies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `showtimes`
--
ALTER TABLE `showtimes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `movie_id` (`movie_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `movies`
--
ALTER TABLE `movies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `showtimes`
--
ALTER TABLE `showtimes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`movie_id`) REFERENCES `movies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `showtimes`
--
ALTER TABLE `showtimes`
  ADD CONSTRAINT `fk_movie_showtime` FOREIGN KEY (`movie_id`) REFERENCES `movies` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
