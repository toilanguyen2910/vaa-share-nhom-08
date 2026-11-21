<?php
// ====================================================================
// HỌC VIỆN HÀNG KHÔNG VIỆT NAM - KHOA CÔNG NGHỆ THÔNG TIN
// MÔN: LẬP TRÌNH WEB - ĐỀ TÀI: VAA SHARE
// TÁC GIẢ PHỤ TRÁCH: DIỆP VĨNH LONG (MSSV: 2531540054)
// TẬP TIN: about.php - TRANG GIỚI THIỆU SỨ MỆNH & CAM KẾT VAA SHARE
// ====================================================================

$pageTitle = 'Giới thiệu về VAA Share';
$activePage = 'about';
require_once __DIR__ . '/includes/header.php';
?>

<div class="container" style="padding: 40px 20px; max-width: 900px;">

  <div style="text-align:center; margin-bottom: 40px;">
    <span class="badge" style="background:#e0f2fe; color:var(--vaa-blue); font-size:0.9rem; padding:6px 14px; margin-bottom:12px;">HỌC VIỆN HÀNG KHÔNG VIỆT NAM</span>
    <h1 style="font-size:2.2rem; font-weight:800; color:var(--vaa-dark); margin-top:8px;">Về Dự Án VAA Share</h1>
    <p style="color:var(--vaa-muted); font-size:1.1rem; max-width:650px; margin:10px auto 0 auto;">
      Nền tảng kết nối trao đổi, trao tặng giáo trình và đồ dùng học tập nội bộ phi thương mại dành riêng cho sinh viên VAA.
    </p>
  </div>

  <div style="background:#fff; border:1px solid var(--vaa-border); border-radius:var(--vaa-radius); padding:30px; margin-bottom:30px; box-shadow:var(--vaa-shadow);">
    <h2 style="font-size:1.4rem; font-weight:700; color:var(--vaa-dark); margin-bottom:12px;">1. Ý tưởng khởi nguồn</h2>
    <p style="line-height:1.8; color:#334155; margin-bottom:16px;">
      Sau mỗi học kỳ hoặc đợt tốt nghiệp ra trường, sinh viên Học viện Hàng không thường tích lũy một lượng lớn giáo trình chuyên ngành (Khí tượng, Dẫn đường bay, Pháp luật hàng không...), dụng cụ vẽ kỹ thuật, máy tính Casio, đồng phục và đồ sinh hoạt phòng trọ. Đa số các vật dụng này còn dùng rất tốt nhưng thường bị bỏ quên hoặc bán phế liệu.
    </p>
    <p style="line-height:1.8; color:#334155;">
      Trong khi đó, các tân sinh viên khóa dưới bước vào năm học mới lại gặp khó khăn trong việc tìm mua đúng tài liệu chuyên ngành của trường và phải chi trả chi phí không nhỏ. VAA Share ra đời như một chiếc cầu nối tin cậy, giúp sinh viên khóa trên tặng lại hoặc thanh lý rẻ cho khóa dưới một cách văn minh, tiết kiệm và bảo vệ môi trường.
    </p>
  </div>

  <div style="background:#fff; border:1px solid var(--vaa-border); border-radius:var(--vaa-radius); padding:30px; margin-bottom:30px; box-shadow:var(--vaa-shadow);">
    <h2 style="font-size:1.4rem; font-weight:700; color:var(--vaa-dark); margin-bottom:20px;">2. Bốn cam kết an toàn & minh bạch</h2>
    
    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:20px;">
      <div style="background:#f8fafc; padding:18px; border-radius:8px; border-left:4px solid var(--vaa-blue);">
        <h4 style="margin-bottom:6px; color:var(--vaa-navy);">🛡️ 100% Nội bộ VAA</h4>
        <p style="font-size:0.88rem; color:#475569; margin:0;">
          Hệ thống chỉ chấp nhận tài khoản có email trường <strong>@vaa.edu.vn</strong>. Người ngoài trường không thể tự do đăng tin bừa bãi.
        </p>
      </div>

      <div style="background:#f8fafc; padding:18px; border-radius:8px; border-left:4px solid #10b981;">
        <h4 style="margin-bottom:6px; color:#065f46;">🎁 Ưu tiên trao tặng 0đ</h4>
        <p style="font-size:0.88rem; color:#475569; margin:0;">
          Khuyến khích tinh thần tương thân tương ái, hỗ trợ miễn phí các tài liệu và giáo trình đại cương cho sinh viên có hoàn cảnh khó khăn.
        </p>
      </div>

      <div style="background:#f8fafc; padding:18px; border-radius:8px; border-left:4px solid #f59e0b;">
        <h4 style="margin-bottom:6px; color:#b45309;">📍 Hẹn gặp an toàn tại trường</h4>
        <p style="font-size:0.88rem; color:#475569; margin:0;">
          Ưu tiên bàn giao trực tiếp tại <strong>Thư viện CS1 (104 Nguyễn Văn Trỗi)</strong> hoặc <strong>Sảnh D2 CS2 (18A/1 Cộng Hòa)</strong>, tránh rủi ro lừa đảo qua mạng.
        </p>
      </div>

      <div style="background:#f8fafc; padding:18px; border-radius:8px; border-left:4px solid #ef4444;">
        <h4 style="margin-bottom:6px; color:#991b1b;">⚙️ Kiểm duyệt & Xử lý báo cáo</h4>
        <p style="font-size:0.88rem; color:#475569; margin:0;">
          Mọi tin đăng mới đều được Admin xem duyệt trước. Sinh viên có nút báo cáo sai phạm để ban quản trị khóa tài khoản lừa đảo.
        </p>
      </div>
    </div>
  </div>

  <div style="background:#f8fafc; border:1px solid var(--vaa-border); border-radius:var(--vaa-radius); padding:24px; text-align:center;">
    <h3 style="margin-bottom:8px;">Nhóm thực hiện đề tài (Nhóm 8)</h3>
    <p style="color:var(--vaa-muted); font-size:0.9rem; margin-bottom:16px;">
      Môn học: Lập trình Web — Lớp 25ĐHSA02 & 25ĐHSA03 — Học viện Hàng không Việt Nam<br>
      Giảng viên hướng dẫn: <strong>ThS. Huỳnh Thanh Sơn</strong>
    </p>
    <div style="display:flex; justify-content:center; gap:20px; flex-wrap:wrap;">
      <span class="badge" style="background:#fff; border:1px solid #cbd5e1; padding:8px 14px;">👑 <strong>Vũ Hoàng Khôi Nguyên</strong> (2531540127 - 25ĐHSA02)</span>
      <span class="badge" style="background:#fff; border:1px solid #cbd5e1; padding:8px 14px;">⚙️ <strong>Vũ Nguyễn Đức Tính</strong> (2531540131 - 25ĐHSA02)</span>
      <span class="badge" style="background:#fff; border:1px solid #cbd5e1; padding:8px 14px;">✨ <strong>Diệp Vĩnh Long</strong> (2531540054 - 25ĐHSA03)</span>
    </div>
  </div>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
