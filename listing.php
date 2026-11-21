<?php
// ====================================================================
// HỌC VIỆN HÀNG KHÔNG VIỆT NAM - KHOA CÔNG NGHỆ THÔNG TIN
// MÔN: LẬP TRÌNH WEB - ĐỀ TÀI: VAA SHARE
// TÁC GIẢ PHỤ TRÁCH: VŨ NGUYỄN ĐỨC TÍNH (MSSV: 2531540131)
// TẬP TIN: listing.php - DANH MỤC ĐỒ DÙNG & BỘ LỌC ĐA CHIỀU
// ====================================================================

$pageTitle = 'Danh mục đồ dùng';
$activePage = 'listing';
require_once __DIR__ . '/includes/header.php';

// Đọc các tham số lọc từ URL ($_GET)
$danhMucId = (int) ($_GET['danh_muc_id'] ?? 0);
$hinhThuc  = trim($_GET['hinh_thuc'] ?? '');
$diaDiemId = (int) ($_GET['dia_diem_id'] ?? 0);
$tuKhoa    = trim($_GET['tu_khoa'] ?? '');

// Đóng gói mảng điều kiện lọc
$filters = [];
if ($danhMucId > 0) $filters['danh_muc_id'] = $danhMucId;
if (!empty($hinhThuc)) $filters['hinh_thuc'] = $hinhThuc;
if ($diaDiemId > 0) $filters['dia_diem_id'] = $diaDiemId;
if (!empty($tuKhoa)) $filters['tu_khoa'] = $tuKhoa;

// Thực hiện gọi hàm lọc (do bạn Tính phụ trách)
$items = get_items($filters);

// Lấy danh mục và cơ sở để đổ vào dropdown bộ lọc
$categories = get_categories();
$campuses   = get_campuses();
?>

<div class="container" style="padding: 30px 20px;">

  <!-- TIÊU ĐỀ TRANG -->
  <div style="margin-bottom: 24px;">
    <h1 style="font-size:1.8rem; font-weight:800; color:var(--vaa-dark);">Kho đồ dùng sinh viên VAA</h1>
    <p style="color:var(--vaa-muted);">
      <?php if (!empty($tuKhoa)): ?>
        Kết quả tìm kiếm cho từ khóa: <strong>"<?php echo e($tuKhoa); ?>"</strong> (<?php echo count($items); ?> kết quả)
      <?php else: ?>
        Hiện có tổng cộng <strong><?php echo count($items); ?></strong> món đồ đang được chia sẻ
      <?php endif; ?>
    </p>
  </div>

  <div style="display:grid; grid-template-columns: 280px 1fr; gap:30px; align-items:start;">

    <!-- CỘT BÊN TRÁI: BỘ LỌC ĐA CHIỀU -->
    <aside style="background:#fff; border:1px solid var(--vaa-border); border-radius:var(--vaa-radius); padding:20px; box-shadow:var(--vaa-shadow);">
      <h3 style="font-size:1.1rem; margin-bottom:16px; border-bottom:1px solid var(--vaa-border); padding-bottom:10px;">
        ⚙️ Bộ lọc tìm kiếm
      </h3>

      <form action="<?php echo BASE_URL; ?>/listing.php" method="GET">
        <!-- 1. Từ khóa -->
        <div class="form-group">
          <label>Từ khóa tìm kiếm:</label>
          <input type="text" name="tu_khoa" value="<?php echo e($tuKhoa); ?>" class="form-control" placeholder="Tên sách, đồng phục...">
        </div>

        <!-- 2. Danh mục -->
        <div class="form-group">
          <label>Danh mục đồ dùng:</label>
          <select name="danh_muc_id" class="form-control">
            <option value="0">-- Tất cả danh mục --</option>
            <?php foreach ($categories as $cat): ?>
              <option value="<?php echo $cat['id']; ?>" <?php echo $danhMucId === (int)$cat['id'] ? 'selected' : ''; ?>>
                <?php echo e($cat['ten_danh_muc']); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <!-- 3. Hình thức -->
        <div class="form-group">
          <label>Hình thức:</label>
          <select name="hinh_thuc" class="form-control">
            <option value="">-- Tất cả hình thức --</option>
            <option value="Tang mien phi" <?php echo $hinhThuc === 'Tang mien phi' ? 'selected' : ''; ?>>Tặng miễn phí (0đ)</option>
            <option value="Thanh ly" <?php echo $hinhThuc === 'Thanh ly' ? 'selected' : ''; ?>>Thanh lý / Bán lại</option>
          </select>
        </div>

        <!-- 4. Cơ sở bàn giao -->
        <div class="form-group">
          <label>Cơ sở bàn giao:</label>
          <select name="dia_diem_id" class="form-control">
            <option value="0">-- Tất cả cơ sở --</option>
            <?php foreach ($campuses as $camp): ?>
              <option value="<?php echo $camp['id']; ?>" <?php echo $diaDiemId === (int)$camp['id'] ? 'selected' : ''; ?>>
                <?php echo e($camp['ten_dia_diem']); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <button type="submit" class="btn btn-primary btn-block" style="margin-top:10px;">Áp dụng bộ lọc</button>
        <?php if ($danhMucId || $hinhThuc || $diaDiemId || $tuKhoa): ?>
          <a href="<?php echo BASE_URL; ?>/listing.php" class="btn btn-outline btn-block" style="margin-top:8px; font-size:0.85rem;">Xóa bộ lọc</a>
        <?php endif; ?>
      </form>
    </aside>

    <!-- CỘT BÊN PHẢI: LƯỚI SẢN PHẨM -->
    <main>
      <?php if (empty($items)): ?>
        <div style="background:#fff; border:1px solid var(--vaa-border); border-radius:var(--vaa-radius); padding:60px 20px; text-align:center;">
          <div style="font-size:3rem; margin-bottom:12px;">🔍</div>
          <h3>Không tìm thấy món đồ phù hợp</h3>
          <p style="color:var(--vaa-muted); margin-top:6px;">Hãy thử nới lỏng các tiêu chí lọc hoặc tìm từ khóa ngắn hơn bạn nhé!</p>
          <a href="<?php echo BASE_URL; ?>/listing.php" class="btn btn-outline" style="margin-top:16px;">Xem tất cả đồ dùng</a>
        </div>
      <?php else: ?>
        <div class="item-grid">
          <?php foreach ($items as $item): ?>
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
                  <span>Đăng bởi: <?php echo e($item['ho_ten']); ?></span>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </main>

  </div>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
