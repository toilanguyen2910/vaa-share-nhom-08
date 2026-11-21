<?php
// ====================================================================
// HỌC VIỆN HÀNG KHÔNG VIỆT NAM - KHOA CÔNG NGHỆ THÔNG TIN
// MÔN: LẬP TRÌNH WEB - ĐỀ TÀI: VAA SHARE
// TÁC GIẢ PHỤ TRÁCH: VŨ NGUYỄN ĐỨC TÍNH (MSSV: 2531540131)
//                   & DIỆP VĨNH LONG (MSSV: 2531540054)
// TẬP TIN: includes/functions.php - CÁC HÀM TRUY VẤN VÀ NGHIỆP VỤ HỆ THỐNG
// ====================================================================

require_once __DIR__ . '/../config/database.php';

/**
 * Hàm chống tấn công XSS (Cross-Site Scripting)
 * Luôn bọc qua hàm này trước khi in bất kỳ chuỗi văn bản nào ra HTML
 */
function e($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Định dạng hiển thị giá tiền VNĐ
 * Nếu giá = 0, hiển thị chữ 'Tặng miễn phí 0đ' nổi bật
 */
function format_price($price) {
    $price = (int) $price;
    if ($price <= 0) {
        return '<span class="badge badge-free">Tặng 0đ</span>';
    }
    return '<span class="badge badge-price">' . number_format($price, 0, ',', '.') . ' đ</span>';
}

/**
 * Lấy toàn bộ danh mục kèm số lượng tin đăng đã duyệt của từng danh mục
 */
function get_categories() {
    $db = get_db();
    $sql = "SELECT dm.*, COUNT(dd.id) AS so_luong_tin
            FROM danh_muc dm
            LEFT JOIN do_dung dd ON dm.id = dd.danh_muc_id AND dd.trang_thai = 'Da duyet'
            GROUP BY dm.id
            ORDER BY dm.thu_tu ASC";
    $result = $db->query($sql);
    $categories = [];
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
    return $categories;
}

/**
 * Lấy danh sách các cơ sở / địa điểm bàn giao của VAA
 */
function get_campuses() {
    $db = get_db();
    $sql = "SELECT * FROM dia_diem ORDER BY id ASC";
    $result = $db->query($sql);
    $campuses = [];
    while ($row = $result->fetch_assoc()) {
        $campuses[] = $row;
    }
    return $campuses;
}

/**
 * HÀM LỌC ĐA CHIỀU (Phụ trách: Vũ Nguyễn Đức Tính)
 * Lọc linh hoạt theo: Danh mục, Hình thức (Tặng/Thanh lý), Cơ sở (CS1/CS2), Từ khóa tìm kiếm
 * Sử dụng Prepared Statement và nối WHERE động, bảo vệ 100% chống SQL Injection.
 */
function get_items($filters = [], $limit = 0, $offset = 0) {
    $db = get_db();

    $sql = "SELECT dd.*, dm.ten_danh_muc, dd_loc.ten_dia_diem, nd.ho_ten, nd.mssv, nd.so_dien_thoai, nd.zalo
            FROM do_dung dd
            JOIN danh_muc dm ON dd.danh_muc_id = dm.id
            JOIN dia_diem dd_loc ON dd.dia_diem_id = dd_loc.id
            JOIN nguoi_dung nd ON dd.nguoi_dung_id = nd.id
            WHERE 1=1";

    $params = [];
    $types  = '';

    // Mặc định chỉ lấy tin 'Da duyet' hoặc 'Dang giao dich' ở trang công khai
    if (isset($filters['trang_thai'])) {
        $sql .= " AND dd.trang_thai = ?";
        $params[] = $filters['trang_thai'];
        $types   .= 's';
    } else {
        $sql .= " AND dd.trang_thai IN ('Da duyet', 'Dang giao dich')";
    }

    // 1. Lọc theo danh mục
    if (!empty($filters['danh_muc_id'])) {
        $sql .= " AND dd.danh_muc_id = ?";
        $params[] = (int) $filters['danh_muc_id'];
        $types   .= 'i';
    }

    // 2. Lọc theo hình thức: Tặng 0đ hoặc Thanh lý
    if (!empty($filters['hinh_thuc'])) {
        $sql .= " AND dd.hinh_thuc = ?";
        $params[] = $filters['hinh_thuc'];
        $types   .= 's';
    }

    // 3. Lọc theo cơ sở: CS1 Phú Nhuận hoặc CS2 Tân Bình
    if (!empty($filters['dia_diem_id'])) {
        $sql .= " AND dd.dia_diem_id = ?";
        $params[] = (int) $filters['dia_diem_id'];
        $types   .= 'i';
    }

    // 4. Tìm kiếm từ khóa theo tiêu đề hoặc mô tả món đồ
    if (!empty($filters['tu_khoa'])) {
        $sql .= " AND (dd.tieu_de LIKE ? OR dd.mo_ta LIKE ?)";
        $searchTerm = '%' . trim($filters['tu_khoa']) . '%';
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $types   .= 'ss';
    }

    // Sắp xếp tin mới nhất lên đầu
    $sql .= " ORDER BY dd.id DESC";

    // Giới hạn số lượng (Limit)
    if ($limit > 0) {
        $sql .= " LIMIT " . (int)$limit;
        if ($offset > 0) {
            $sql .= " OFFSET " . (int)$offset;
        }
    }

    $stmt = $db->prepare($sql);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $items = [];
    while ($row = $result->fetch_assoc()) {
        $items[] = $row;
    }
    $stmt->close();
    return $items;
}

/**
 * Lấy chi tiết một món đồ theo ID (Phụ trách: Diệp Vĩnh Long)
 */
function get_item_by_id($id) {
    $db = get_db();
    $stmt = $db->prepare("SELECT dd.*, dm.ten_danh_muc, dd_loc.ten_dia_diem, dd_loc.dia_chi,
                                 nd.ho_ten, nd.mssv, nd.so_dien_thoai, nd.zalo, nd.khoa_lop, nd.email
                          FROM do_dung dd
                          JOIN danh_muc dm ON dd.danh_muc_id = dm.id
                          JOIN dia_diem dd_loc ON dd.dia_diem_id = dd_loc.id
                          JOIN nguoi_dung nd ON dd.nguoi_dung_id = nd.id
                          WHERE dd.id = ? LIMIT 1");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $item = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $item;
}

/**
 * Tự động tăng số lượt xem của món đồ
 */
function increase_item_views($id) {
    $db = get_db();
    $stmt = $db->prepare("UPDATE do_dung SET luot_xem = luot_xem + 1 WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

/**
 * Thêm tin đăng mới (Phụ trách: Diệp Vĩnh Long)
 */
function create_item($data) {
    $db = get_db();
    $stmt = $db->prepare("INSERT INTO do_dung (nguoi_dung_id, danh_muc_id, dia_diem_id, tieu_de, mo_ta, hinh_thuc, gia, tinh_trang, hinh_anh, trang_thai)
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'Cho duyet')");
    $stmt->bind_param("iiisssiss", 
        $data['nguoi_dung_id'],
        $data['danh_muc_id'],
        $data['dia_diem_id'],
        $data['tieu_de'],
        $data['mo_ta'],
        $data['hinh_thuc'],
        $data['gia'],
        $data['tinh_trang'],
        $data['hinh_anh']
    );
    $success = $stmt->execute();
    $new_id = $stmt->insert_id;
    $stmt->close();
    return $success ? $new_id : false;
}

/**
 * Lấy danh sách bình luận dưới mỗi món đồ
 */
function get_comments_by_item($item_id) {
    $db = get_db();
    $stmt = $db->prepare("SELECT bl.*, nd.ho_ten, nd.mssv
                          FROM binh_luan bl
                          JOIN nguoi_dung nd ON bl.nguoi_dung_id = nd.id
                          WHERE bl.do_dung_id = ?
                          ORDER BY bl.id ASC");
    $stmt->bind_param("i", $item_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $comments = [];
    while ($row = $result->fetch_assoc()) {
        $comments[] = $row;
    }
    $stmt->close();
    return $comments;
}

/**
 * Thêm bình luận hỏi đáp mới
 */
function add_comment($item_id, $user_id, $noi_dung) {
    $db = get_db();
    $stmt = $db->prepare("INSERT INTO binh_luan (do_dung_id, nguoi_dung_id, noi_dung) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $item_id, $user_id, $noi_dung);
    $success = $stmt->execute();
    $stmt->close();
    return $success;
}

/**
 * Gửi yêu cầu nhận / mua đồ (Cơ chế P2P Request)
 */
function create_transaction_request($item_id, $sender_id, $owner_id, $message, $phone) {
    $db = get_db();
    $stmt = $db->prepare("INSERT INTO yeu_cau_giao_dich (do_dung_id, nguoi_gui_id, nguoi_nhan_id, loi_nhan, sdt_lien_he, trang_thai)
                          VALUES (?, ?, ?, ?, ?, 'Cho xac nhan')");
    $stmt->bind_param("iiiss", $item_id, $sender_id, $owner_id, $message, $phone);
    $success = $stmt->execute();
    $stmt->close();
    return $success;
}

/**
 * Lấy danh sách yêu cầu gửi đến cho chủ đồ duyệt
 */
function get_incoming_requests_for_owner($owner_id) {
    $db = get_db();
    $stmt = $db->prepare("SELECT yk.*, dd.tieu_de, dd.hinh_anh, dd.hinh_thuc, dd.gia,
                                 nd.ho_ten AS ten_nguoi_gui, nd.mssv AS mssv_nguoi_gui, nd.email AS email_nguoi_gui
                          FROM yeu_cau_giao_dich yk
                          JOIN do_dung dd ON yk.do_dung_id = dd.id
                          JOIN nguoi_dung nd ON yk.nguoi_gui_id = nd.id
                          WHERE yk.nguoi_nhan_id = ?
                          ORDER BY yk.id DESC");
    $stmt->bind_param("i", $owner_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $requests = [];
    while ($row = $result->fetch_assoc()) {
        $requests[] = $row;
    }
    $stmt->close();
    return $requests;
}

/**
 * Chủ đồ cập nhật trạng thái yêu cầu (Đồng ý / Từ chối)
 * Nếu Đồng ý -> Chuyển trạng thái món đồ sang 'Dang giao dich'
 */
function update_request_status($request_id, $owner_id, $new_status) {
    $db = get_db();

    // Lấy thông tin yêu cầu để biết do_dung_id
    $stmt = $db->prepare("SELECT do_dung_id FROM yeu_cau_giao_dich WHERE id = ? AND nguoi_nhan_id = ? LIMIT 1");
    $stmt->bind_param("ii", $request_id, $owner_id);
    $stmt->execute();
    $req = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$req) return false;

    // Cập nhật trạng thái yêu cầu
    $stmt2 = $db->prepare("UPDATE yeu_cau_giao_dich SET trang_thai = ? WHERE id = ?");
    $stmt2->bind_param("si", $new_status, $request_id);
    $stmt2->execute();
    $stmt2->close();

    // Nếu đồng ý -> Chuyển món đồ sang 'Dang giao dich'
    if ($new_status === 'Dong y') {
        $stmt3 = $db->prepare("UPDATE do_dung SET trang_thai = 'Dang giao dich' WHERE id = ?");
        $stmt3->bind_param("i", $req['do_dung_id']);
        $stmt3->execute();
        $stmt3->close();
    } elseif ($new_status === 'Hoan tat') {
        $stmt4 = $db->prepare("UPDATE do_dung SET trang_thai = 'Hoan thanh' WHERE id = ?");
        $stmt4->bind_param("i", $req['do_dung_id']);
        $stmt4->execute();
        $stmt4->close();
    }

    return true;
}

/**
 * Báo cáo tin đăng vi phạm
 */
function create_report($item_id, $user_id, $reason, $details) {
    $db = get_db();
    $stmt = $db->prepare("INSERT INTO bao_cao_vi_pham (do_dung_id, nguoi_bao_cao_id, ly_do, chi_tiet) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiss", $item_id, $user_id, $reason, $details);
    $success = $stmt->execute();
    $stmt->close();
    return $success;
}
