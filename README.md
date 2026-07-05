# 🎬 HỆ THỐNG QUẢN LÝ VÀ ĐẶT VÉ RẠP CHIẾU PHIM - NOVA CINEMA

## 1. Tên đề tài
**Xây dựng Website Đặt Vé Xem Phim Trực Tuyến (Nova Cinema)**
*Mô tả ngắn:* Dự án là một hệ thống web cho phép người dùng tra cứu lịch chiếu, xem trailer và đặt vé xem phim trực tuyến. Đồng thời cung cấp trang quản trị cho Admin quản lý suất chiếu và danh mục phim.

---

## 2. Danh sách thành viên
| STT | Họ và Tên | MSSV | Vai trò |
| :---: | :--- | :---: | :--- |
| 1 | Nguyễn Thanh Nhàn | [24210501026] | Trưởng nhóm |
| 2 | Đặng Công Danh | [24210501002] |Thành viên|
| 3 | Trần Vĩnh Thịnh | [24210501042] | Thanh viên |
| 4 | Đào Tuấn Khanh | [24210501019] | Thanh viên |
| 5 | Trang Thanh Bảo | [24210501054] | Thanh viên |
---

## 3. Phân công công việc
* **Nguyễn Thanh Nhàn:** Thiết kế Database,khởi tạo thư mục và file cấu hình(setup-architecture)và tích hợp xem Trailer YouTube. 
* **Đặng Công Danh:**Xử lý toàn bộ logic quản lý phim(feature-crud-movies)và viết tài liệu báo cáo.  
* **Trần Vĩnh Thịnh:** Logic đặt vé (feature-booking-action),xử lý dữ liệu khi người dùng bấm "Đặt vé", kiểm tra và lưu thông tin vào bảng bookings và xử lý tính năng lịch chiếu (ui-lich-chieu).
* **Đào Tuấn Khanh:** : Dùng HTML5, CSS3, Bootstrap 5 thiết kế giao diện Trang chủ, Trang chi tiết phim, Thanh điều hướng Menu, Logovà Footer. Đặc biệt phải đảm bảo giao diện Responsive hiển thị tốt trên điện thoại và máy tính(ui-homepage).
* **Trang Thanh Bảo:** : Dựng bộ khung giao diện cho Trang quản trị . Viết chức năng Đăng nhập / Đăng xuất cho hệ thống. 
---

## 4. Công nghệ sử dụng
* **Ngôn ngữ Lập trình (Backend):** PHP (PDO).
* **Cơ sở Dữ liệu:** MySQL (phpMyAdmin).
* **Giao diện (Frontend):** HTML5, CSS3, JavaScript.
* **Framework/Thư viện:** Bootstrap 5.3.7, Bootstrap Icons.
* **Môi trường phát triển:** XAMPP / VS Code.

---

## 5. Mô tả chức năng
Hệ thống được chia thành 2 phân hệ chính với các chức năng như sau:

### 👤 Phân hệ Người dùng (Khách hàng)
* **Xác thực:** Đăng ký, Đăng nhập (Mã hóa mật khẩu an toàn bằng `password_hash`), Đăng xuất.
* **Tra cứu:** Lọc dữ liệu phim đang chiếu, phim sắp chiếu và thanh tìm kiếm cơ bản về phim.
* **Tương tác:**Phân trang phim , Xem chi tiết nội dung phim, xem Trailer trực tiếp trên web dạng Popup.
* **Đặt vé:** Chọn suất chiếu, chọn ghế ngồi, tự động tính tổng tiền.
* **In hóa đơn:** Hiển thị hóa đơn điện tử in đúng tên và số điện thoại của khách hàng lấy từ Session.

### 🛡️ Phân hệ Quản trị viên (Admin)
* **Quản lý Phim:** Thêm phim mới (cập nhật poster, link YouTube Trailer), sửa/xóa phim.
* **Quản lý Suất chiếu:** Lên lịch chiếu, chọn định dạng (2D Sub, 3D IMAX...), đánh dấu suất chiếu Đặc biệt (Hot).
* **Quản lý Hóa đơn:** Xem lịch sử đặt vé của toàn bộ hệ thống, theo dõi tên và số điện thoại liên hệ của khách.
* **Quản lý thành viên :** Xem được thông tin tài khoản đăng ký và có thể xóa đi tài khoản khách.
---

## 6. Hướng dẫn cài đặt
1. Tải và cài đặt phần mềm **XAMPP** .
2. Clone từ GitHub về máy.
3. Hãy copy hoặc di chuyển toàn bộ thư mục dự án đó vào đường dẫn và đổi tên thành `datvexemphim`.
4. Copy toàn bộ thư mục `datvexemphim` dán vào đường dẫn: `C:\xampp\htdocs\`.
5. Mở **XAMPP Control Panel**, khởi động 2 module là **Apache** và **MySQL**.
6. Mở trình duyệt, truy cập vào `http://localhost/phpmyadmin/`.
7. Tạo một cơ sở dữ liệu mới mang tên `novacinema_db` dán toàn bộ code trong novacinema_db đang có vào và bấm (Go)** để khôi phục cấu trúc các bảng.
8. Về XAMPP mở admin của Apache và chon "datvexemphim".

---

## 7. Hướng dẫn chạy chương trình
1. **Truy cập Trang chủ:** Mở trình duyệt và gõ đường dẫn `http://localhost/datvexemphim/`
2. **Tài khoản Khách (Test):** Có thể tự tạo tài khoản mới qua trang Đăng ký.
3. **Tài khoản Admin (Quản trị):**
   * Tên đăng nhập: `admin`
   * Mật khẩu: `admin123`

---

## 8. Cấu trúc thư mục
```text
DATVEXEMPHIM/
│
├── admin/                  # Thư mục chứa các chức năng của Quản trị viên
│   ├── dashboard.php       # Bảng điều khiển, thống kê
│   ├── movie_add.php       # Xử lý thêm phim mới (có link trailer)
│   ├── showtime_add.php    # Xử lý thêm suất chiếu (Format, is_hot)
│   └── bookings.php 
│   ├── member_delete.php
│   ├── members.php
│   ├── movie_delete.php
│   ├── movie_edit.php
│   ├── movies.php
│   ├── showtime_delete.php
│   ├── showtime.php         
│
├── assets/                
│   ├── css/               
│   ├── images/             # Hình ảnh poster, logo, banner
│                
│
├── db_connect.php          # Cấu hình kết nối CSDL (PDO)
├── index.php               # Trang chủ hiển thị danh sách phim
├── login.php               # Trang xử lý đăng nhập & phân quyền
├── register.php            # Trang đăng ký tài khoản khách hàng
├── movie-detail.php        # Trang chi tiết phim & Modal xem Trailer
├── process_booking.php     # Xử lý luồng đặt vé, lưu vào CSDL & xuất vé
├── logout.php              # Xóa session và đăng xuất
├── novacinema_db.sql       # File export Database gốc để import
├── quick_book_modal.php
├── lich-chieu.php
├── booking.php
└── README.md

