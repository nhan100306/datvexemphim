<?php
// Truy vấn gom toàn bộ dữ liệu lịch chiếu hiện có (Từ hôm nay trở đi)
require_once 'db_connect.php';
$sql_quick = "
    SELECT m.id AS movie_id, m.title, s.show_date, s.show_time 
    FROM movies m 
    JOIN showtimes s ON m.id = s.movie_id 
    WHERE s.show_date >= CURDATE()
    ORDER BY m.title, s.show_date, s.show_time
";
$stmt_quick = $conn->query($sql_quick);

// Sắp xếp dữ liệu thành mảng đa chiều để đưa cho Javascript xử lý
$quick_data = [];
while ($row = $stmt_quick->fetch(PDO::FETCH_ASSOC)) {
    $m_id = $row['movie_id'];
    $date = $row['show_date'];
    $time = date('H:i', strtotime($row['show_time']));
    
    if (!isset($quick_data[$m_id])) {
        $quick_data[$m_id] = ['title' => $row['title'], 'dates' => []];
    }
    if (!isset($quick_data[$m_id]['dates'][$date])) {
        $quick_data[$m_id]['dates'][$date] = [];
    }
    $quick_data[$m_id]['dates'][$date][] = $time;
}
$quick_data_json = json_encode($quick_data);
?>

<div class="modal fade" id="quickBookModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            
            <div class="modal-header bg-warning border-0">
                <h5 class="modal-title fw-bold text-dark">🎟️ ĐẶT VÉ NHANH</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body p-4">
                <form action="booking.php" method="GET" id="quickBookForm">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">1. Chọn Phim</label>
                        <select class="form-select text-truncate" name="id" id="qb-movie" required style="max-width: 100%;">
                            <option value="">-- Vui lòng chọn phim --</option>
                            <?php foreach ($quick_data as $id => $movie): ?>
                                <option value="<?= $id ?>"><?= htmlspecialchars($movie['title']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">2. Chọn Ngày Chiếu</label>
                        <select class="form-select text-truncate" name="date" id="qb-date" disabled required style="max-width: 100%;">
                            <option value="">-- Vui lòng chọn phim trước --</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">3. Chọn Suất Chiếu</label>
                        <select class="form-select text-truncate" name="time" id="qb-time" disabled required style="max-width: 100%;">
                            <option value="">-- Vui lòng chọn ngày trước --</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-danger w-100 fw-bold py-2" id="qb-submit" disabled>
                        TIẾP TỤC CHỌN GHẾ
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Nhận dữ liệu từ PHP
    const movieData = <?= $quick_data_json ?>;
    
    const selectMovie = document.getElementById('qb-movie');
    const selectDate = document.getElementById('qb-date');
    const selectTime = document.getElementById('qb-time');
    const btnSubmit = document.getElementById('qb-submit');

    // Khi người dùng đổi Phim
    selectMovie.addEventListener('change', function() {
        const movieId = this.value;
        selectDate.innerHTML = '<option value="">-- Chọn ngày --</option>';
        selectTime.innerHTML = '<option value="">-- Vui lòng chọn ngày trước --</option>';
        selectTime.disabled = true;
        btnSubmit.disabled = true;

        if (movieId && movieData[movieId]) {
            const dates = Object.keys(movieData[movieId].dates);
            dates.forEach(date => {
                // Đổi format yyyy-mm-dd sang dd/mm/yyyy cho đẹp
                const dateObj = new Date(date);
                const displayDate = ('0' + dateObj.getDate()).slice(-2) + '/' + ('0' + (dateObj.getMonth() + 1)).slice(-2) + '/' + dateObj.getFullYear();
                
                selectDate.innerHTML += `<option value="${date}">${displayDate}</option>`;
            });
            selectDate.disabled = false;
        } else {
            selectDate.disabled = true;
            selectDate.innerHTML = '<option value="">-- Vui lòng chọn phim trước --</option>';
        }
    });

    // Khi người dùng đổi Ngày
    selectDate.addEventListener('change', function() {
        const movieId = selectMovie.value;
        const selectedDate = this.value;
        selectTime.innerHTML = '<option value="">-- Chọn suất chiếu --</option>';
        btnSubmit.disabled = true;

        if (movieId && selectedDate && movieData[movieId].dates[selectedDate]) {
            const times = movieData[movieId].dates[selectedDate];
            times.forEach(time => {
                selectTime.innerHTML += `<option value="${time}">${time}</option>`;
            });
            selectTime.disabled = false;
        } else {
            selectTime.disabled = true;
        }
    });

    // Khi người dùng đổi Giờ (Bật nút Submit)
    selectTime.addEventListener('change', function() {
        if (this.value !== "") {
            btnSubmit.disabled = false;
        } else {
            btnSubmit.disabled = true;
        }
    });
</script>