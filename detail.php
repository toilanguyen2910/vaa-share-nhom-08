<?php
// ====================================================================
// HỌC VIỆN HÀNG KHÔNG VIỆT NAM - KHOA CÔNG NGHỆ THÔNG TIN
// MÔN: LẬP TRÌNH WEB - ĐỀ TÀI: VAA SHARE
// TÁC GIẢ PHỤ TRÁCH: DIỆP VĨNH LONG (MSSV: 2531540054)
// TẬP TIN: detail.php - CHI TIẾT SẢN PHẨM & LIÊN HỆ GIAO DỊCH
// ====================================================================

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

$itemId = (int) ($_GET['id'] ?? 0);
$item = get_item_by_id($itemId);

// Nếu không tìm thấy món đồ
if (!$item) {
    die("<div style='font-family:sans-serif; text-align:center; padding:50px;'>"
        . "<h2>Món đồ này không tồn tại hoặc đã bị gỡ bỏ!</h2>"
        . "<p><a href='" . BASE_URL . "/listing.php'>Quay lại danh mục</a></p>"
        . "</div>");
}

// Tự động tăng lượt xem
increase_item_views($itemId);

$currentUser = current_user();

// XỬ LÝ CÁC FORM POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_login();
    $action = $_POST['action'] ?? '';

    // 1. GỬI YÊU CẦU NHẬN / MUA ĐỒ
    if ($action === 'send_request') {
        $phone   = trim($_POST['sdt_lien_he'] ?? '');
        $message = trim($_POST['loi_nhan'] ?? '');

        if (empty($phone)) {
            $_SESSION['flash_error'] = 'Vui lòng nhập số điện thoại hoặc Zalo để người đăng tiện liên hệ lại!';
        } else {
            $ok = create_transaction_request($itemId, $currentUser['id'], $item['nguoi_dung_id'], $message, $phone);
            if ($ok) {
                $_SESSION['flash_success'] = 'Đã gửi yêu cầu thành công! Bạn hãy chờ chủ món đồ xác nhận nhé.';
            } else {
                $_SESSION['flash_error'] = 'Gửi yêu cầu thất bại, vui lòng thử lại.';
            }
        }
        header("Location: " . BASE_URL . "/detail.php?id=" . $itemId);
        exit;
    }

    // 2. THÊM BÌNH LUẬN HỎI ĐÁP
    if ($action === 'add_comment') {
        $content = trim($_POST['noi_dung'] ?? '');
        if (!empty($content)) {
            add_comment($itemId, $currentUser['id'], $content);
            $_SESSION['flash_success'] = 'Bình luận của bạn đã được đăng tải!';
        }
        header("Location: " . BASE_URL . "/detail.php?id=" . $itemId);
        exit;
    }

    // 3. GỬI BÁO CÁO VI PHẠM
    if ($action === 'report_item') {
        $reason  = trim($_POST['ly_do'] ?? '');
        $details = trim($_POST['chi_tiet'] ?? '');
        if (!empty($reason)) {
            create_report($itemId, $currentUser['id'], $reason, $details);
            $_SESSION['flash_success'] = 'Cảm ơn bạn đã phản ánh! Quản trị viên sẽ kiểm tra nội dung này sớm nhất.';
        }
        header("Location: " . BASE_URL . "/detail.php?id=" . $itemId);
        exit;
    }
}

// Lấy danh sách bình luận
$comments = get_comments_by_item($itemId);

$pageTitle = $item['tieu_de'];
require_once __DIR__ . '/includes/header.php';
?>

<div class="container" style="padding: 30px 20px;">

  <!-- ĐƯỜNG DẪN ĐIỀU HƯỚNG (BREADCRUMB) -->
  <div style="font-size:0.85rem; color:var(--vaa-muted); margin-bottom:16px;">
    <a href="<?php echo BASE_URL; ?>/index.php">Trang chủ</a> &raquo;
    <a href="<?php echo BASE_URL; ?>/listing.php?danh_muc_id=<?php echo $item['danh_muc_id']; ?>"><?php echo e($item['ten_danh_muc']); ?></a> &raquo;
    <span><?php echo e($item['tieu_de']); ?></span>
  </div>

  <div class="detail-layout">

    <!-- CỘT TRÁI: ẢNH VÀ NỘI DUNG MÔ TẢ CHI TIẾT -->
    <main class="detail-main">
      <div class="detail-img-wrap">
        <img src="<?php echo BASE_URL; ?>/uploads/<?php echo e($item['hinh_anh']); ?>" alt="<?php echo e($item['tieu_de']); ?>" class="detail-img">
      </div>

      <div style="display:flex; gap:10px; align-items:center; margin-bottom:12px;">
        <?php echo format_price($item['gia']); ?>
        <span class="badge badge-campus">📍 <?php echo e($item['ten_dia_diem']); ?></span>
        <?php if ($item['trang_thai'] === 'Dang giao dich'): ?>
          <span class="badge" style="background:#fed7aa; color:#9a3412;">⏳ Đang có người hẹn lấy</span>
        <?php elseif ($item['trang_thai'] === 'Hoan thanh'): ?>
          <span class="badge" style="background:#cbd5e1; color:#475569;">✓ Đã giao dịch xong</span>
        <?php endif; ?>
      </div>

      <h1 style="font-size:1.6rem; font-weight:800; color:var(--vaa-dark); margin-bottom:16px; line-height:1.4;">
        <?php echo e($item['tieu_de']); ?>
      </h1>

      <div style="background:#f8fafc; border:1px solid var(--vaa-border); border-radius:8px; padding:16px; margin-bottom:24px;">
        <h3 style="font-size:0.95rem; text-transform:uppercase; color:var(--vaa-muted); margin-bottom:10px;">Thông số món đồ</h3>
        <ul style="list-style:none; font-size:0.92rem; line-height:2;">
          <li>📦 <strong>Tình trạng:</strong> <?php echo e($item['tinh_trang']); ?></li>
          <li>📂 <strong>Danh mục:</strong> <?php echo e($item['ten_danh_muc']); ?></li>
          <li>📍 <strong>Điểm nhận đồ:</strong> <?php echo e($item['dia_chi']); ?></li>
          <li>🕒 <strong>Ngày đăng:</strong> <?php echo date('d/m/Y H:i', strtotime($item['ngay_dang'])); ?></li>
          <li>👁️ <strong>Lượt xem:</strong> <?php echo (int) $item['luot_xem']; ?> lượt</li>
        </ul>
      </div>

      <div style="margin-bottom:30px;">
        <h3 style="font-size:1.15rem; font-weight:700; margin-bottom:10px;">Mô tả chi tiết từ người chia sẻ</h3>
        <div style="font-size:1rem; line-height:1.8; color:#334155; white-space:pre-line;">
          <?php echo e($item['mo_ta']); ?>
        </div>
      </div>

      <!-- KHU VỰC HỎI ĐÁP & BÌNH LUẬN -->
      <section style="border-top:1px solid var(--vaa-border); padding-top:24px; margin-top:30px;">
        <h3 style="font-size:1.2rem; font-weight:700; margin-bottom:16px;">💬 Hỏi đáp & Bình luận công khai (<?php echo count($comments); ?>)</h3>

        <!-- DANH SÁCH BÌNH LUẬN -->
        <div style="display:flex; flex-direction:column; gap:12px; margin-bottom:24px;">
          <?php if (empty($comments)): ?>
            <p style="color:var(--vaa-muted); font-size:0.9rem;">Chưa có bình luận nào. Hãy là người đầu tiên đặt câu hỏi cho người đăng!</p>
          <?php else: ?>
            <?php foreach ($comments as $c): ?>
              <div style="background:#f8fafc; border:1px solid var(--vaa-border); border-radius:8px; padding:12px 16px;">
                <div style="display:flex; justify-content:space-between; margin-bottom:4px;">
                  <strong style="color:var(--vaa-blue); font-size:0.9rem;">👤 <?php echo e($c['ho_ten']); ?> (<?php echo e($c['mssv']); ?>)</strong>
                  <span style="font-size:0.75rem; color:#94a3b8;"><?php echo date('d/m/Y H:i', strtotime($c['ngay_dang'])); ?></span>
                </div>
                <p style="font-size:0.92rem; color:var(--vaa-text); margin:0;"><?php echo e($c['noi_dung']); ?></p>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>

        <!-- FORM GỬI BÌNH LUẬN -->
        <?php if ($currentUser): ?>
          <form action="<?php echo BASE_URL; ?>/detail.php?id=<?php echo $itemId; ?>" method="POST">
            <input type="hidden" name="action" value="add_comment">
            <div class="form-group">
              <textarea name="noi_dung" class="form-control" rows="3" placeholder="Nhập câu hỏi hoặc lời nhắn của bạn cho người đăng..." required></textarea>
            </div>
            <button type="submit" class="btn btn-outline" style="font-size:0.88rem;">Gửi bình luận</button>
          </form>
        <?php else: ?>
          <div style="background:#fffbeb; border:1px solid #fef3c7; padding:12px; border-radius:8px; font-size:0.9rem;">
            ℹ️ Vui lòng <a href="<?php echo BASE_URL; ?>/login.php" style="font-weight:700;">đăng nhập</a> để tham gia hỏi đáp dưới bài đăng này.
          </div>
        <?php endif; ?>
      </section>
    </main>

    <!-- CỘT PHẢI: THÔNG TIN TÁC GIẢ & HÀNH ĐỘNG GIAO DỊCH -->
    <aside class="detail-sidebar">

      <!-- HỘP THÔNG TIN NGƯỜI ĐĂNG -->
      <div class="sidebar-box">
        <div style="display:flex; gap:12px; align-items:center;">
          <div class="seller-avatar">
            <?php echo mb_substr($item['ho_ten'], 0, 1, 'UTF-8'); ?>
          </div>
          <div>
            <h4 style="font-size:1.05rem; font-weight:700; color:var(--vaa-dark);"><?php echo e($item['ho_ten']); ?></h4>
            <span style="font-size:0.8rem; color:var(--vaa-muted);">MSSV: <?php echo e($item['mssv']); ?></span><br>
            <span style="font-size:0.8rem; color:var(--vaa-blue); font-weight:600;"><?php echo e($item['khoa_lop']); ?></span>
          </div>
        </div>

        <hr style="border:0; border-top:1px solid var(--vaa-border); margin:16px 0;">

        <!-- 1. NÚT LIÊN HỆ ZALO TRỰC TIẾP -->
        <a href="https://zalo.me/<?php echo urlencode($item['zalo'] ?: $item['so_dien_thoai']); ?>" target="_blank" class="btn btn-primary btn-block" style="background:#0068ff; margin-bottom:8px;">
          💬 Chat Zalo người đăng
        </a>

        <!-- 2. NÚT GỌI ĐIỆN -->
        <a href="tel:<?php echo e($item['so_dien_thoai']); ?>" class="btn btn-outline btn-block" style="margin-bottom:16px;">
          📞 Gọi: <?php echo e($item['so_dien_thoai']); ?>
        </a>

        <!-- 3. HỘP GỬI YÊU CẦU NHẬN/MUA ĐỒ NỘI BỘ -->
        <div style="background:#f1f5f9; padding:16px; border-radius:8px;">
          <h4 style="font-size:0.95rem; font-weight:700; margin-bottom:8px;">🤝 Đặt giữ đồ / Gửi yêu cầu</h4>
          <p style="font-size:0.82rem; color:var(--vaa-muted); margin-bottom:12px;">
            Gửi yêu cầu để người đăng duyệt giữ đồ cho bạn trước khi hẹn gặp tại trường.
          </p>

          <?php if ($currentUser): ?>
            <?php if ($currentUser['id'] === (int)$item['nguoi_dung_id']): ?>
              <p style="font-size:0.85rem; color:#0369a1; font-style:italic;">(Đây là tin đăng của chính bạn)</p>
            <?php else: ?>
              <form action="<?php echo BASE_URL; ?>/detail.php?id=<?php echo $itemId; ?>" method="POST">
                <input type="hidden" name="action" value="send_request">
                <div class="form-group" style="margin-bottom:10px;">
                  <input type="text" name="sdt_lien_he" value="<?php echo e($currentUser['so_dien_thoai']); ?>" class="form-control" style="font-size:0.85rem;" placeholder="SĐT / Zalo của bạn" required>
                </div>
                <div class="form-group" style="margin-bottom:10px;">
                  <textarea name="loi_nhan" class="form-control" style="font-size:0.85rem; min-height:60px;" placeholder="Lời nhắn (Ví dụ: Trưa mai mình ghé CS1 lấy được không?)..."></textarea>
                </div>
                <button type="submit" class="btn btn-success btn-block" style="font-size:0.9rem;">Gửi yêu cầu nhận đồ</button>
              </form>
            <?php endif; ?>
          <?php else: ?>
            <a href="<?php echo BASE_URL; ?>/login.php" class="btn btn-outline btn-block" style="font-size:0.85rem;">Đăng nhập để đặt nhận đồ</a>
          <?php endif; ?>
        </div>
      </div>

      <!-- BÁO CÁO VI PHẠM -->
      <div style="text-align:center;">
        <button onclick="document.getElementById('reportModal').style.display='block'" class="btn btn-outline" style="border:none; color:#ef4444; font-size:0.85rem;">
          🚩 Báo cáo tin đăng có dấu hiệu vi phạm
        </button>
      </div>

    </aside>

  </div>

</div>

<!-- MODAL BÁO CÁO VI PHẠM -->
<div id="reportModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999;">
  <div style="background:#fff; max-width:450px; margin:80px auto; padding:24px; border-radius:12px;">
    <h3 style="color:#ef4444; margin-bottom:12px;">🚩 Báo cáo vi phạm bài đăng</h3>
    <form action="<?php echo BASE_URL; ?>/detail.php?id=<?php echo $itemId; ?>" method="POST">
      <input type="hidden" name="action" value="report_item">
      <div class="form-group">
        <label>Lý do báo cáo:</label>
        <select name="ly_do" class="form-control" required>
          <option value="Đồ giả mạo / Lừa đảo">Đồ giả mạo / Lừa đảo</option>
          <option value="Thông tin sai sự thật">Thông tin sai sự thật</option>
          <option value="Giá bán quá cao so với đồ cũ">Giá bán quá cao so với đồ cũ</option>
          <option value="Nội dung không phù hợp chuẩn mực VAA">Nội dung không phù hợp chuẩn mực VAA</option>
        </select>
      </div>
      <div class="form-group">
        <label>Chi tiết thêm:</label>
        <textarea name="chi_tiet" class="form-control" placeholder="Mô tả cụ thể để ban quản trị xử lý..."></textarea>
      </div>
      <div style="display:flex; justify-content:flex-end; gap:10px;">
        <button type="button" onclick="document.getElementById('reportModal').style.display='none'" class="btn btn-outline">Hủy</button>
        <button type="submit" class="btn btn-danger">Gửi báo cáo</button>
      </div>
    </form>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
