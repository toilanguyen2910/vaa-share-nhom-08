<?php
// ====================================================================
// HỌC VIỆN HÀNG KHÔNG VIỆT NAM - KHOA CÔNG NGHỆ THÔNG TIN
// MÔN: LẬP TRÌNH WEB - ĐỀ TÀI: VAA SHARE
// TÁC GIẢ PHỤ TRÁCH: VŨ NGUYỄN ĐỨC TÍNH (MSSV: 2531540131)
// TẬP TIN: index.php - TRANG CHỦ HỆ THỐNG
// ====================================================================

$pageTitle = 'Trang chủ';
$activePage = 'home';
require_once __DIR__ . '/includes/header.php';

// Lấy danh sách 5 danh mục chính
$categories = get_categories();

// Lấy danh sách 8 món đồ mới nhất đã duyệt
$recentItems = get_items([], 8);

// Lấy danh sách 4 món đồ tặng miễn phí 0đ
$freeItems = get_items(['hinh_thuc' => 'Tang mien phi'], 4);
?>

<!-- BANNER CHÍNH (HERO SECTION) -->
<section class="hero">
  <div class="container">
    <h1>CÙNG CHIA SẺ — TIẾT KIỆM CHO SINH VIÊN VAA</h1>
    <p>Nền tảng trao đổi giáo trình Hàng không, thanh lý đồng phục, dụng cụ học tập và đồ phòng trọ nội bộ tại CS1 Phú Nhuận & CS2 Tân Bình.</p>

    <!-- FORM TÌM KIẾM NHANH TRÊN HERO BANNER -->
    <form action="<?php echo BASE_URL; ?>/listing.php" method="GET" class="hero-search-box">
      <input type="text" name="tu_khoa" placeholder="Tìm giáo trình, máy tính Casio, áo đồng phục..." required>
      <button type="submit">🔍 Tìm kiếm</button>
    </form>

    <div class="hero-tags">
      <span style="font-size:0.85rem; color:#94a3b8; align-self:center;">Gợi ý nhanh:</span>
      <a href="<?php echo BASE_URL; ?>/listing.php?hinh_thuc=Tang+mien+phi" class="hero-tag">🎁 Đồ tặng 0đ</a>
      <a href="<?php echo BASE_URL; ?>/listing.php?dia_diem_id=1" class="hero-tag">📍 Cơ sở 1 (Phú Nhuận)</a>
      <a href="<?php echo BASE_URL; ?>/listing.php?dia_diem_id=2" class="hero-tag">📍 Cơ sở 2 (Cộng Hòa)</a>
      <a href="<?php echo BASE_URL; ?>/listing.php?danh_muc_id=1" class="hero-tag">📚 Giáo trình Hàng không</a>
      <a href="<?php echo BASE_URL; ?>/listing.php?danh_muc_id=2" class="hero-tag">👔 Đồng phục VAA</a>
    </div>
  </div>
</section>

<div class="container">

  <!-- 1. HÀNG 5 DANH MỤC NỔI BẬT -->
  <section class="section" style="padding-top: 30px;">
    <div class="section-header">
      <div>
        <h2 class="section-title">Danh mục đồ dùng sinh viên</h2>
        <p class="section-subtitle">Phân loại theo nhu cầu học tập và sinh hoạt đặc thù tại Học viện</p>
      </div>
      <a href="<?php echo BASE_URL; ?>/listing.php" style="font-weight:600;">Xem tất cả →</a>
    </div>

    <div class="category-grid">
      <?php foreach ($categories as $cat): ?>
        <a href="<?php echo BASE_URL; ?>/listing.php?danh_muc_id=<?php echo $cat['id']; ?>" class="category-card">
          <h3><?php echo e($cat['ten_danh_muc']); ?></h3>
          <span><?php echo (int) $cat['so_luong_tin']; ?> món đồ đang có</span>
        </a>
      <?php endforeach; ?>
    </div>
  </section>

  <!-- 2. ĐỒ DÙNG MỚI ĐĂNG GẦN ĐÂY -->
  <section class="section">
    <div class="section-header">
      <div>
        <h2 class="section-title">Mới đăng gần đây</h2>
        <p class="section-subtitle">Các món đồ vừa được sinh viên chia sẻ và kiểm duyệt an toàn</p>
      </div>
      <a href="<?php echo BASE_URL; ?>/listing.php" class="btn btn-outline" style="padding: 6px 14px; font-size:0.85rem;">Xem kho đồ dùng</a>
    </div>

    <div class="item-grid">
      <?php if (empty($recentItems)): ?>
        <p style="grid-column: 1/-1; text-align:center; color:#64748b; padding:40px;">Hiện chưa có món đồ nào được đăng.</p>
      <?php else: ?>
        <?php foreach ($recentItems as $item): ?>
          <div class="item-card">
            <div class="item-card-img-wrap">
              <a href="<?php echo BASE_URL; ?>/detail.php?id=<?php echo $item['id']; ?>">
                <img src="<?php echo BASE_URL; ?>/uploads/<?php echo e($item['hinh_anh']); ?>" alt="<?php echo e($item['tieu_de']); ?>" class="item-card-img">
              </a>
              <div class="item-badge-type">
                <?php echo format_price($item['gia']); ?>
              </div>
            </div>

            <div class="item-card-body">
              <div class="item-card-category"><?php echo e($item['ten_danh_muc']); ?></div>
              <a href="<?php echo BASE_URL; ?>/detail.php?id=<?php echo $item['id']; ?>" class="item-card-title">
                <?php echo e($item['tieu_de']); ?>
              </a>

              <div class="item-card-meta">
                <span class="badge badge-campus">📍 <?php echo e($item['ten_dia_diem']); ?></span>
                <span>👁️ <?php echo (int) $item['luot_xem']; ?></span>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </section>

  <!-- 3. GÓC ĐỒ TẶNG 0Đ DÀNH RIÊNG CHO TÂN SINH VIÊN -->
  <?php if (!empty($freeItems)): ?>
  <section class="section" style="background: #f0fdf4; padding: 30px; border-radius: var(--vaa-radius); border:1px solid #bbf7d0; margin-bottom:40px;">
    <div class="section-header">
      <div>
        <h2 class="section-title" style="color:#166534;">🎁 Góc trao tặng 0đ (Hỗ trợ sinh viên)</h2>
        <p class="section-subtitle" style="color:#15803d;">Giáo trình, tài liệu và đồ dùng học tập được các bạn khóa trên trao tặng miễn phí 100%</p>
      </div>
      <a href="<?php echo BASE_URL; ?>/listing.php?hinh_thuc=Tang+mien+phi" class="btn btn-success" style="padding:6px 14px; font-size:0.85rem;">Xem tất cả đồ 0đ</a>
    </div>

    <div class="item-grid">
      <?php foreach ($freeItems as $item): ?>
        <div class="item-card" style="border-color:#86efac;">
          <div class="item-card-img-wrap">
            <a href="<?php echo BASE_URL; ?>/detail.php?id=<?php echo $item['id']; ?>">
              <img src="<?php echo BASE_URL; ?>/uploads/<?php echo e($item['hinh_anh']); ?>" alt="<?php echo e($item['tieu_de']); ?>" class="item-card-img">
            </a>
            <div class="item-badge-type">
              <span class="badge badge-free">Tặng 0đ</span>
            </div>
          </div>

          <div class="item-card-body">
            <div class="item-card-category"><?php echo e($item['ten_danh_muc']); ?></div>
            <a href="<?php echo BASE_URL; ?>/detail.php?id=<?php echo $item['id']; ?>" class="item-card-title">
              <?php echo e($item['tieu_de']); ?>
            </a>

            <div class="item-card-meta">
              <span class="badge badge-campus">📍 <?php echo e($item['ten_dia_diem']); ?></span>
              <span style="color:#16a34a; font-weight:600;">Miễn phí</span>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </section>
  <?php endif; ?>

  <!-- 4. BA BƯỚC HOẠT ĐỘNG ĐƠN GIẢN -->
  <section class="section" style="border-top:1px solid var(--vaa-border); padding-top:40px;">
    <h2 class="section-title" style="text-align:center; margin-bottom:30px;">Cách thức hoạt động tại VAA Share</h2>
    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap:24px; text-align:center;">
      <div style="background:#fff; padding:24px; border-radius:12px; border:1px solid var(--vaa-border);">
        <div style="font-size:2.5rem; margin-bottom:10px;">📸</div>
        <h3 style="margin-bottom:8px;">1. Đăng tin đồ dùng</h3>
        <p style="color:#64748b; font-size:0.9rem;">Chụp ảnh sách hoặc đồ dùng, chọn hình thức tặng 0đ hoặc thanh lý giá rẻ sinh viên.</p>
      </div>
      <div style="background:#fff; padding:24px; border-radius:12px; border:1px solid var(--vaa-border);">
        <div style="font-size:2.5rem; margin-bottom:10px;">🤝</div>
        <h3 style="margin-bottom:8px;">2. Gửi yêu cầu & Xác nhận</h3>
        <p style="color:#64748b; font-size:0.9rem;">Sinh viên có nhu cầu bấm gửi yêu cầu, chủ món đồ vào trang cá nhân bấm Đồng ý giao dịch.</p>
      </div>
      <div style="background:#fff; padding:24px; border-radius:12px; border:1px solid var(--vaa-border);">
        <div style="font-size:2.5rem; margin-bottom:10px;">📍</div>
        <h3 style="margin-bottom:8px;">3. Hẹn gặp tại CS1 / CS2</h3>
        <p style="color:#64748b; font-size:0.9rem;">Liên hệ Zalo / SĐT để hẹn trao đổi trực tiếp tại Thư viện CS1 hoặc Sảnh D2 CS2 an toàn.</p>
      </div>
    </div>
  </section>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
