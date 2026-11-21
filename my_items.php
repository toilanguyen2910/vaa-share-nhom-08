<?php
// ====================================================================
// HỌC VIỆN HÀNG KHÔNG VIỆT NAM - KHOA CÔNG NGHỆ THÔNG TIN
// MÔN: LẬP TRÌNH WEB - ĐỀ TÀI: VAA SHARE
// TÁC GIẢ PHỤ TRÁCH: DIỆP VĨNH LONG (MSSV: 2531540054)
// TẬP TIN: my_items.php - QUẢN LÝ ĐỒ DÙNG CỦA TÔI & DUYỆT YÊU CẦU
// ====================================================================

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

require_login();
$currentUser = current_user();
$db = get_db();

// XỬ LÝ KHI CHỦ ĐỒ DUYỆT HOẶC TỪ CHỐI YÊU CẦU
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // Cập nhật yêu cầu giao dịch
    if ($action === 'update_req') {
        $reqId = (int) ($_POST['req_id'] ?? 0);
        $status = $_POST['status'] ?? '';
        if (in_array($status, ['Dong y', 'Tu choi', 'Hoan tat'])) {
            update_request_status($reqId, $currentUser['id'], $status);
            $_SESSION['flash_success'] = 'Đã cập nhật trạng thái giao dịch!';
        }
        header('Location: ' . BASE_URL . '/my_items.php?tab=requests');
        exit;
    }

    // Đổi trạng thái tin đăng (Đánh dấu đã xong hoặc ẩn tin)
    if ($action === 'update_item_status') {
        $itemId = (int) ($_POST['item_id'] ?? 0);
        $newStatus = $_POST['new_status'] ?? '';
        if (in_array($newStatus, ['Hoan thanh', 'Da an'])) {
            $stmt = $db->prepare("UPDATE do_dung SET trang_thai = ? WHERE id = ? AND nguoi_dung_id = ?");
            $stmt->bind_param("sii", $newStatus, $itemId, $currentUser['id']);
            $stmt->execute();
            $stmt->close();
            $_SESSION['flash_success'] = 'Đã cập nhật trạng thái món đồ!';
        }
        header('Location: ' . BASE_URL . '/my_items.php');
        exit;
    }
}

// LẤY DANH SÁCH ĐỒ TÔI ĐÃ ĐĂNG
$stmtItems = $db->prepare("SELECT dd.*, dm.ten_danh_muc, dl.ten_dia_diem
                           FROM do_dung dd
                           JOIN danh_muc dm ON dd.danh_muc_id = dm.id
                           JOIN dia_diem dl ON dd.dia_diem_id = dl.id
                           WHERE dd.nguoi_dung_id = ?
                           ORDER BY dd.id DESC");
$stmtItems->bind_param("i", $currentUser['id']);
$stmtItems->execute();
$myItems = $stmtItems->get_result()->fetch_all(MYSQLI_ASSOC);
$stmtItems->close();

// LẤY YÊU CẦU NGƯỜI KHÁC GỬI ĐẾN
$incomingRequests = get_incoming_requests_for_owner($currentUser['id']);

// LẤY YÊU CẦU TÔI ĐÃ GỬI ĐI
$stmtSent = $db->prepare("SELECT yk.*, dd.tieu_de, dd.gia, dd.hinh_thuc, nd.ho_ten AS ten_chu_do, nd.so_dien_thoai AS sdt_chu_do, nd.zalo AS zalo_chu_do
                          FROM yeu_cau_giao_dich yk
                          JOIN do_dung dd ON yk.do_dung_id = dd.id
                          JOIN nguoi_dung nd ON yk.nguoi_nhan_id = nd.id
                          WHERE yk.nguoi_gui_id = ?
                          ORDER BY yk.id DESC");
$stmtSent->bind_param("i", $currentUser['id']);
$stmtSent->execute();
$sentRequests = $stmtSent->get_result()->fetch_all(MYSQLI_ASSOC);
$stmtSent->close();

$activeTab = $_GET['tab'] ?? 'items';

$pageTitle = 'Quản lý đồ của tôi & Giao dịch';
$activePage = 'my_items';
require_once __DIR__ . '/includes/header.php';
?>

<div class="container" style="padding: 30px 20px;">
  
  <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px;">
    <div>
      <h1 style="font-size:1.8rem; font-weight:800; color:var(--vaa-dark);">Quản lý đồ dùng & Yêu cầu</h1>
      <p style="color:var(--vaa-muted);">Theo dõi các món đồ bạn chia sẻ và tiếp nhận yêu cầu từ các bạn sinh viên</p>
    </div>
    <a href="<?php echo BASE_URL; ?>/post.php" class="btn btn-primary">➕ Đăng món đồ mới</a>
  </div>

  <!-- THANH CHUYỂN TAB -->
  <div style="display:flex; gap:10px; border-bottom:2px solid var(--vaa-border); margin-bottom:24px;">
    <a href="<?php echo BASE_URL; ?>/my_items.php?tab=items" class="btn <?php echo $activeTab === 'items' ? 'btn-primary' : 'btn-outline'; ?>" style="border-radius:8px 8px 0 0;">
      📦 Đồ tôi đã đăng (<?php echo count($myItems); ?>)
    </a>
    <a href="<?php echo BASE_URL; ?>/my_items.php?tab=requests" class="btn <?php echo $activeTab === 'requests' ? 'btn-primary' : 'btn-outline'; ?>" style="border-radius:8px 8px 0 0;">
      📥 Yêu cầu gửi đến tôi (<?php echo count($incomingRequests); ?>)
    </a>
    <a href="<?php echo BASE_URL; ?>/my_items.php?tab=sent" class="btn <?php echo $activeTab === 'sent' ? 'btn-primary' : 'btn-outline'; ?>" style="border-radius:8px 8px 0 0;">
      📤 Yêu cầu tôi đã gửi (<?php echo count($sentRequests); ?>)
    </a>
  </div>

  <!-- NỘI DUNG TAB 1: ĐỒ TÔI ĐÃ ĐĂNG -->
  <?php if ($activeTab === 'items'): ?>
    <?php if (empty($myItems)): ?>
      <div style="background:#fff; border:1px solid var(--vaa-border); border-radius:12px; padding:50px; text-align:center;">
        <p style="color:var(--vaa-muted); margin-bottom:16px;">Bạn chưa đăng món đồ nào lên hệ thống.</p>
        <a href="<?php echo BASE_URL; ?>/post.php" class="btn btn-primary">Đăng món đồ đầu tiên</a>
      </div>
    <?php else: ?>
      <div class="table-responsive">
        <table class="data-table">
          <thead>
            <tr>
              <th>Hình ảnh</th>
              <th>Tên món đồ</th>
              <th>Danh mục & Cơ sở</th>
              <th>Hình thức</th>
              <th>Trạng thái</th>
              <th>Thao tác</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($myItems as $item): ?>
              <tr>
                <td style="width:70px;">
                  <img src="<?php echo BASE_URL; ?>/uploads/<?php echo e($item['hinh_anh']); ?>" style="width:60px; height:45px; object-fit:cover; border-radius:6px;">
                </td>
                <td>
                  <strong><a href="<?php echo BASE_URL; ?>/detail.php?id=<?php echo $item['id']; ?>"><?php echo e($item['tieu_de']); ?></a></strong><br>
                  <small style="color:var(--vaa-muted);">🕒 <?php echo date('d/m/Y', strtotime($item['ngay_dang'])); ?></small>
                </td>
                <td>
                  <?php echo e($item['ten_danh_muc']); ?><br>
                  <small style="color:var(--vaa-muted);">📍 <?php echo e($item['ten_dia_diem']); ?></small>
                </td>
                <td><?php echo format_price($item['gia']); ?></td>
                <td>
                  <?php if ($item['trang_thai'] === 'Cho duyet'): ?>
                    <span class="badge" style="background:#fef3c7; color:#b45309;">Chờ Admin duyệt</span>
                  <?php elseif ($item['trang_thai'] === 'Da duyet'): ?>
                    <span class="badge" style="background:#dcfce7; color:#15803d;">Đang hiển thị</span>
                  <?php elseif ($item['trang_thai'] === 'Dang giao dich'): ?>
                    <span class="badge" style="background:#ffedd5; color:#c2410c;">Đang có người hẹn</span>
                  <?php elseif ($item['trang_thai'] === 'Hoan thanh'): ?>
                    <span class="badge" style="background:#f1f5f9; color:#475569;">Đã trao/đã bán</span>
                  <?php else: ?>
                    <span class="badge" style="background:#fee2e2; color:#b91c1c;">Đã ẩn</span>
                  <?php endif; ?>
                </td>
                <td>
                  <?php if ($item['trang_thai'] === 'Da duyet' || $item['trang_thai'] === 'Dang giao dich'): ?>
                    <form action="<?php echo BASE_URL; ?>/my_items.php" method="POST" style="display:inline;">
                      <input type="hidden" name="action" value="update_item_status">
                      <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                      <input type="hidden" name="new_status" value="Hoan thanh">
                      <button type="submit" class="btn btn-outline" style="padding:4px 8px; font-size:0.75rem;" onclick="return confirm('Xác nhận món này đã trao tặng / đã bán xong?')">Đánh dấu hoàn tất</button>
                    </form>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  <?php endif; ?>

  <!-- NỘI DUNG TAB 2: YÊU CẦU NGƯỜI KHÁC GỬI ĐẾN (APPROVAL FLOW) -->
  <?php if ($activeTab === 'requests'): ?>
    <?php if (empty($incomingRequests)): ?>
      <div style="background:#fff; border:1px solid var(--vaa-border); border-radius:12px; padding:50px; text-align:center;">
        <p style="color:var(--vaa-muted);">Chưa có bạn nào gửi yêu cầu xin/mua đồ của bạn.</p>
      </div>
    <?php else: ?>
      <div class="table-responsive">
        <table class="data-table">
          <thead>
            <tr>
              <th>Món đồ</th>
              <th>Người yêu cầu</th>
              <th>Lời nhắn & Liên hệ</th>
              <th>Trạng thái</th>
              <th>Hành động của bạn</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($incomingRequests as $req): ?>
              <tr>
                <td>
                  <strong><?php echo e($req['tieu_de']); ?></strong><br>
                  <?php echo format_price($req['gia']); ?>
                </td>
                <td>
                  <strong><?php echo e($req['ten_nguoi_gui']); ?></strong><br>
                  <small style="color:var(--vaa-muted);">MSSV: <?php echo e($req['mssv_nguoi_gui']); ?></small>
                </td>
                <td>
                  <p style="margin:0 0 4px 0; font-size:0.9rem;">"<?php echo e($req['loi_nhan'] ?: 'Em muốn nhận món đồ này ạ.'); ?>"</p>
                  <span style="font-size:0.85rem; color:var(--vaa-blue);">📞 SĐT/Zalo: <?php echo e($req['sdt_lien_he']); ?></span>
                </td>
                <td>
                  <?php if ($req['trang_thai'] === 'Cho xac nhan'): ?>
                    <span class="badge" style="background:#fef3c7; color:#b45309;">Chờ bạn xác nhận</span>
                  <?php elseif ($req['trang_thai'] === 'Dong y'): ?>
                    <span class="badge" style="background:#dcfce7; color:#15803d;">Đã đồng ý</span>
                  <?php elseif ($req['trang_thai'] === 'Tu choi'): ?>
                    <span class="badge" style="background:#fee2e2; color:#b91c1c;">Đã từ chối</span>
                  <?php else: ?>
                    <span class="badge" style="background:#f1f5f9; color:#475569;">Giao dịch hoàn tất</span>
                  <?php endif; ?>
                </td>
                <td>
                  <?php if ($req['trang_thai'] === 'Cho xac nhan'): ?>
                    <form action="<?php echo BASE_URL; ?>/my_items.php" method="POST" style="display:inline;">
                      <input type="hidden" name="action" value="update_req">
                      <input type="hidden" name="req_id" value="<?php echo $req['id']; ?>">
                      <input type="hidden" name="status" value="Dong y">
                      <button type="submit" class="btn btn-success" style="padding:4px 10px; font-size:0.8rem;">✓ Đồng ý</button>
                    </form>
                    <form action="<?php echo BASE_URL; ?>/my_items.php" method="POST" style="display:inline;">
                      <input type="hidden" name="action" value="update_req">
                      <input type="hidden" name="req_id" value="<?php echo $req['id']; ?>">
                      <input type="hidden" name="status" value="Tu choi">
                      <button type="submit" class="btn btn-outline" style="padding:4px 10px; font-size:0.8rem; color:#ef4444;" onclick="return confirm('Từ chối yêu cầu này?')">✕ Từ chối</button>
                    </form>
                  <?php elseif ($req['trang_thai'] === 'Dong y'): ?>
                    <form action="<?php echo BASE_URL; ?>/my_items.php" method="POST" style="display:inline;">
                      <input type="hidden" name="action" value="update_req">
                      <input type="hidden" name="req_id" value="<?php echo $req['id']; ?>">
                      <input type="hidden" name="status" value="Hoan tat">
                      <button type="submit" class="btn btn-primary" style="padding:4px 10px; font-size:0.8rem;">Đã trao xong</button>
                    </form>
                  <?php else: ?>
                    <span style="color:#94a3b8; font-size:0.85rem;">Đã đóng</span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  <?php endif; ?>

  <!-- NỘI DUNG TAB 3: YÊU CẦU TÔI ĐÃ GỬI ĐI -->
  <?php if ($activeTab === 'sent'): ?>
    <?php if (empty($sentRequests)): ?>
      <div style="background:#fff; border:1px solid var(--vaa-border); border-radius:12px; padding:50px; text-align:center;">
        <p style="color:var(--vaa-muted);">Bạn chưa gửi yêu cầu xin hoặc mua món đồ nào.</p>
        <a href="<?php echo BASE_URL; ?>/listing.php" class="btn btn-outline" style="margin-top:10px;">Khám phá kho đồ dùng</a>
      </div>
    <?php else: ?>
      <div class="table-responsive">
        <table class="data-table">
          <thead>
            <tr>
              <th>Món đồ bạn muốn nhận</th>
              <th>Chủ món đồ</th>
              <th>Liên hệ chủ đồ</th>
              <th>Trạng thái yêu cầu</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($sentRequests as $sReq): ?>
              <tr>
                <td>
                  <strong><a href="<?php echo BASE_URL; ?>/detail.php?id=<?php echo $sReq['do_dung_id']; ?>"><?php echo e($sReq['tieu_de']); ?></a></strong><br>
                  <?php echo format_price($sReq['gia']); ?>
                </td>
                <td>
                  <strong><?php echo e($sReq['ten_chu_do']); ?></strong>
                </td>
                <td>
                  <?php if ($sReq['trang_thai'] === 'Dong y'): ?>
                    <a href="https://zalo.me/<?php echo urlencode($sReq['zalo_chu_do'] ?: $sReq['sdt_chu_do']); ?>" target="_blank" class="btn btn-primary" style="padding:4px 10px; font-size:0.8rem; background:#0068ff;">
                      💬 Chat Zalo hẹn gặp
                    </a>
                  <?php else: ?>
                    <span style="font-size:0.85rem; color:#94a3b8;">Chờ chủ đồ đồng ý sẽ hiện liên hệ</span>
                  <?php endif; ?>
                </td>
                <td>
                  <?php if ($sReq['trang_thai'] === 'Cho xac nhan'): ?>
                    <span class="badge" style="background:#fef3c7; color:#b45309;">Chờ chủ đồ phản hồi</span>
                  <?php elseif ($sReq['trang_thai'] === 'Dong y'): ?>
                    <span class="badge" style="background:#dcfce7; color:#15803d;">🎉 Đã được đồng ý!</span>
                  <?php elseif ($sReq['trang_thai'] === 'Tu choi'): ?>
                    <span class="badge" style="background:#fee2e2; color:#b91c1c;">Chủ đồ đã từ chối</span>
                  <?php else: ?>
                    <span class="badge" style="background:#f1f5f9; color:#475569;">Giao dịch hoàn tất</span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  <?php endif; ?>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
