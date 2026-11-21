<?php
// ====================================================================
// HỌC VIỆN HÀNG KHÔNG VIỆT NAM - KHOA CÔNG NGHỆ THÔNG TIN
// MÔN: LẬP TRÌNH WEB - ĐỀ TÀI: VAA SHARE
// TÁC GIẢ PHỤ TRÁCH: DIỆP VĨNH LONG (MSSV: 2531540054)
// TẬP TIN: post.php - ĐĂNG TIN TRAO TẶNG / BÁN LẠI ĐỒ DÙNG
// ====================================================================

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

// Bắt buộc đăng nhập tài khoản sinh viên mới được đăng tin
require_login();
$currentUser = current_user();

$categories = get_categories();
$campuses   = get_campuses();
$errors = [];

// XỬ LÝ KHI NGƯỜI DÙNG BẤM NỘP FORM (METHOD POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tieu_de     = trim($_POST['tieu_de'] ?? '');
    $danh_muc_id = (int) ($_POST['danh_muc_id'] ?? 0);
    $dia_diem_id = (int) ($_POST['dia_diem_id'] ?? 0);
    $hinh_thuc   = trim($_POST['hinh_thuc'] ?? 'Tang mien phi');
    $gia         = ($hinh_thuc === 'Tang mien phi') ? 0 : (int) ($_POST['gia'] ?? 0);
    $tinh_trang  = trim($_POST['tinh_trang'] ?? 'Còn tốt');
    $mo_ta       = trim($_POST['mo_ta'] ?? '');

    // Kiểm tra dữ liệu đầu vào (Validation)
    if (empty($tieu_de)) {
        $errors[] = 'Vui lòng nhập tên tiêu đề món đồ cần chia sẻ.';
    }
    if ($danh_muc_id <= 0) {
        $errors[] = 'Vui lòng chọn danh mục đồ dùng.';
    }
    if ($dia_diem_id <= 0) {
        $errors[] = 'Vui lòng chọn cơ sở bàn giao hẹn gặp tại trường.';
    }
    if ($hinh_thuc === 'Thanh ly' && $gia <= 0) {
        $errors[] = 'Vui lòng nhập giá thanh lý hợp lệ (lớn hơn 0đ).';
    }
    if (empty($mo_ta)) {
        $errors[] = 'Vui lòng nhập vài dòng mô tả tình trạng đồ dùng.';
    }

    // XỬ LÝ UPLOAD ẢNH THẬT
    $hinh_anh = 'default_item.jpg';
    if (isset($_FILES['hinh_anh']) && $_FILES['hinh_anh']['error'] === UPLOAD_ERR_OK) {
        $fileTmp  = $_FILES['hinh_anh']['tmp_name'];
        $fileName = $_FILES['hinh_anh']['name'];
        $fileSize = $_FILES['hinh_anh']['size'];

        // Kiểm tra dung lượng tối đa 5MB
        if ($fileSize > 5 * 1024 * 1024) {
            $errors[] = 'Kích thước ảnh không được vượt quá 5MB.';
        } else {
            // Kiểm tra đuôi file hợp lệ
            $allowedExts = ['jpg', 'jpeg', 'png', 'webp'];
            $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            if (!in_array($ext, $allowedExts)) {
                $errors[] = 'Chỉ chấp nhận các định dạng ảnh: JPG, PNG, WEBP.';
            } else {
                // Đổi tên file ngẫu nhiên để tránh trùng lặp
                $newFileName = 'item_' . time() . '_' . uniqid() . '.' . $ext;
                $uploadPath = __DIR__ . '/uploads/' . $newFileName;

                if (move_uploaded_file($fileTmp, $uploadPath)) {
                    $hinh_anh = $newFileName;
                } else {
                    $errors[] = 'Có lỗi khi lưu file ảnh lên máy chủ.';
                }
            }
        }
    }

    // NẾU HỢP LỆ -> LƯU VÀO CSDL
    if (empty($errors)) {
        $newItemId = create_item([
            'nguoi_dung_id' => $currentUser['id'],
            'danh_muc_id'   => $danh_muc_id,
            'dia_diem_id'   => $dia_diem_id,
            'tieu_de'       => $tieu_de,
            'mo_ta'         => $mo_ta,
            'hinh_thuc'     => $hinh_thuc,
            'gia'           => $gia,
            'tinh_trang'    => $tinh_trang,
            'hinh_anh'      => $hinh_anh
        ]);

        if ($newItemId) {
            $_SESSION['flash_success'] = 'Đăng tin thành công! Tin của bạn đang ở trạng thái "Chờ duyệt" và sẽ hiển thị ngay sau khi Admin kiểm tra.';
            header('Location: ' . BASE_URL . '/my_items.php');
            exit;
        } else {
            $errors[] = 'Lỗi hệ thống khi lưu bài đăng. Vui lòng thử lại.';
        }
    }
}

$pageTitle = 'Đăng tin mới';
require_once __DIR__ . '/includes/header.php';
?>

<div class="container" style="padding: 30px 20px;">
  <div class="form-card" style="max-width: 700px;">
    <h2 style="font-size:1.5rem; font-weight:800; color:var(--vaa-dark); margin-bottom:8px;">➕ Đăng tin chia sẻ đồ dùng</h2>
    <p style="color:var(--vaa-muted); font-size:0.9rem; margin-bottom:20px;">
      Hãy mô tả trung thực để các bạn sinh viên VAA dễ dàng liên hệ nhận hoặc mua lại đồ dùng.
    </p>

    <?php if (!empty($errors)): ?>
      <div class="alert alert-error">
        <ul style="margin-left: 20px;">
          <?php foreach ($errors as $err): ?>
            <li><?php echo e($err); ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <form action="<?php echo BASE_URL; ?>/post.php" method="POST" enctype="multipart/form-data">
      
      <!-- 1. Tiêu đề món đồ -->
      <div class="form-group">
        <label>Tiêu đề món đồ: <span style="color:red;">*</span></label>
        <input type="text" name="tieu_de" class="form-control" placeholder="Ví dụ: Giáo trình Khí tượng Hàng không, Máy tính Casio 580..." required>
      </div>

      <!-- 2. Danh mục & Cơ sở -->
      <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px;">
        <div class="form-group">
          <label>Danh mục: <span style="color:red;">*</span></label>
          <select name="danh_muc_id" class="form-control" required>
            <option value="">-- Chọn danh mục --</option>
            <?php foreach ($categories as $cat): ?>
              <option value="<?php echo $cat['id']; ?>"><?php echo e($cat['ten_danh_muc']); ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-group">
          <label>Cơ sở bàn giao: <span style="color:red;">*</span></label>
          <select name="dia_diem_id" class="form-control" required>
            <option value="">-- Chọn cơ sở hẹn gặp --</option>
            <?php foreach ($campuses as $camp): ?>
              <option value="<?php echo $camp['id']; ?>"><?php echo e($camp['ten_dia_diem']); ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <!-- 3. Hình thức & Giá -->
      <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px;">
        <div class="form-group">
          <label>Hình thức chia sẻ: <span style="color:red;">*</span></label>
          <select name="hinh_thuc" id="hinhThucSelect" class="form-control" onchange="togglePriceInput()">
            <option value="Tang mien phi">🎁 Tặng miễn phí (0đ)</option>
            <option value="Thanh ly">🏷️ Thanh lý / Bán lại giá rẻ</option>
          </select>
        </div>

        <div class="form-group" id="priceGroup" style="display:none;">
          <label>Giá bán (VNĐ): <span style="color:red;">*</span></label>
          <input type="number" name="gia" id="giaInput" class="form-control" placeholder="Ví dụ: 50000" min="1000" step="1000">
        </div>
      </div>

      <!-- 4. Tình trạng đồ dùng -->
      <div class="form-group">
        <label>Tình trạng đồ dùng:</label>
        <select name="tinh_trang" class="form-control">
          <option value="Còn rất tốt (trên 90%)">Còn rất tốt (trên 90%)</option>
          <option value="Còn dùng tốt (70% - 90%)">Còn dùng tốt (70% - 90%)</option>
          <option value="Cũ nhưng hoạt động bình thường">Cũ nhưng hoạt động bình thường</option>
          <option value="Mới 100% chưa qua sử dụng">Mới 100% chưa qua sử dụng</option>
        </select>
      </div>

      <!-- 5. Tải ảnh thực tế -->
      <div class="form-group">
        <label>Hình ảnh chụp thực tế đồ dùng:</label>
        <input type="file" name="hinh_anh" accept="image/png, image/jpeg, image/webp" class="form-control">
        <small style="color:var(--vaa-muted); display:block; margin-top:4px;">Chấp nhận file JPG, PNG, WEBP (tối đa 5MB). Nếu không chọn sẽ dùng ảnh mặc định.</small>
      </div>

      <!-- 6. Mô tả chi tiết -->
      <div class="form-group">
        <label>Mô tả chi tiết: <span style="color:red;">*</span></label>
        <textarea name="mo_ta" class="form-control" rows="4" placeholder="Nêu rõ thông tin sách, phụ kiện kèm theo, thời gian bạn rảnh để hẹn gặp tại CS1 hoặc CS2..." required></textarea>
      </div>

      <button type="submit" class="btn btn-primary btn-block" style="padding:12px; font-size:1rem;">🚀 Đăng tin ngay</button>
    </form>
  </div>
</div>

<script>
function togglePriceInput() {
  const select = document.getElementById('hinhThucSelect');
  const priceGroup = document.getElementById('priceGroup');
  const giaInput = document.getElementById('giaInput');
  if (select.value === 'Thanh ly') {
    priceGroup.style.display = 'block';
    giaInput.required = true;
  } else {
    priceGroup.style.display = 'none';
    giaInput.required = false;
    giaInput.value = '';
  }
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
