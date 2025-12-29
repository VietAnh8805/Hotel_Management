<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Dịch vụ - Hotel Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="./Public/Css/service_style.css">
</head>
<body>

<div class="admin-wrapper">
    <aside class="sidebar">
        <div class="sidebar-header">
            <i class="fas fa-hotel"></i>
            <span>Hotel Admin</span>
        </div>
        
        <nav class="sidebar-menu">
            <ul>
                <li>
                    <a href="?controller=AdminController&action=department">
                        <i class="fas fa-th-large"></i>Quản lý Bộ phận
                    </a>
                </li>
                <li><a href="#"><i class="fas fa-users"></i>Quản lý Nhân viên</a></li>
                <li><a href="#"><i class="fas fa-user-shield"></i>Quản lý Tài khoản</a></li>
                <li><a href="#"><i class="fas fa-bed"></i>Quản lý Loại phòng</a></li>
                <li><a href="#"><i class="fas fa-door-open"></i>Quản lý Phòng</a></li>
                <li class="active">
                    <a href="?controller=ServiceController&action=index">
                        <i class="fas fa-concierge-bell"></i>Quản lý Dịch vụ
                    </a>
                </li>
                <li><a href="#"><i class="fas fa-tags"></i>Quản lý Giảm giá</a></li>
                <li><a href="#"><i class="fas fa-address-book"></i>Quản lý Khách hàng</a></li>
                <li><a href="#"><i class="fas fa-calendar-check"></i>Quản lý Đặt phòng</a></li>
                <li><a href="#"><i class="fas fa-credit-card"></i>Thanh toán & Trả phòng</a></li>
                <li><a href="#"><i class="fas fa-chart-line"></i>Báo cáo & Thống kê</a></li>
            </ul>
        </nav>

        <div class="sidebar-footer">
            <a href="?controller=AuthController&action=logout" class="btn-logout" onclick="return confirmLogout(event)">
                <i class="fas fa-sign-out-alt"></i> Đăng xuất
            </a>
        </div>
    </aside>

    <main class="main-content">
        <header class="top-header">
            <div class="user-info">
                Chào mừng: <strong>Quản trị viên</strong>
            </div>
        </header>

        <section class="content-body">
            <div class="toolbar" style="display: flex; justify-content: space-between; align-items: center;">
                <div class="left-tools" style="display: flex; gap: 15px; align-items: center;">
                    <button class="btn btn-primary" onclick="toggleForm(true)">
                        <i class="fas fa-plus-circle"></i> Thêm mới
                    </button>
                    
                    <form action="?controller=ServiceController&action=index" method="POST" style="display: flex; gap: 5px;">
                        <input type="text" name="keyword" value="<?= isset($_POST['keyword']) ? $_POST['keyword'] : '' ?>" placeholder="Tìm tên hoặc mã dịch vụ..." 
                               style="padding: 8px 15px; border-radius: 8px; border: 1px solid var(--border-color); background: #555960ff; color: white;">
                        <button type="submit" name="search" class="btn btn-outline" style="padding: 8px 15px;">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>

                <div class="right-tools" style="display: flex; gap: 10px;">
                    <?php $currentKeyword = isset($_POST['keyword']) ? $_POST['keyword'] : (isset($_GET['keyword']) ? $_GET['keyword'] : ''); ?>
                    <a href="?controller=ServiceController&action=exportExcel&keyword=<?= $currentKeyword ?>" class="btn-custom-white">
                        <i class="fas fa-file-excel" style="color: #16a34a;"></i> Xuất Excel
                    </a>
                    <button class="btn-custom-white" onclick="toggleImport(true)">
                       <i class="fas fa-file-import"></i> Upload File
                    </button>
                </div>
            </div>

            <!-- FORM THÊM/SỬA DỊCH VỤ -->
            <div id="serviceForm" class="info-card" style="display: none; margin-top: 20px;">
                <h4 id="formTitle" style="color: var(--ocean-blue); margin-bottom: 15px;">Thêm dịch vụ mới</h4>
                <form id="mainServiceForm" action="?controller=ServiceController&action=saveService" method="POST">
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Mã Dịch Vụ <span style="color: red;">*</span></label>
                            <input type="text" name="MaDichVu" id="MaDichVu" class="form-control" placeholder="VD: DV001" required>
                        </div>
                        <div class="form-group">
                            <label>Tên Dịch Vụ <span style="color: red;">*</span></label>
                            <input type="text" name="TenDichVu" id="TenDichVu" class="form-control" placeholder="VD: Spa massage" required>
                        </div>
                        <div class="form-group">
                            <label>Chi Phí Dịch Vụ (VNĐ) <span style="color: red;">*</span></label>
                            <input type="number" name="ChiPhiDichVu" id="ChiPhiDichVu" class="form-control" placeholder="VD: 500000" required min="0" step="1000">
                        </div>
                        <div class="form-group" style="grid-column: span 2;">
                            <label>Mô Tả Dịch Vụ</label>
                            <textarea name="MoTaDichVu" id="MoTaDichVu" class="form-control" rows="3" placeholder="Mô tả chi tiết về dịch vụ..."></textarea>
                        </div>
                    </div>
                    <div style="margin-top: 20px; display: flex; gap: 10px;">
                        <input type="hidden" name="isEdit" id="isEdit" value="0">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Xác nhận Lưu
                        </button>
                        <button type="button" class="btn btn-danger" onclick="toggleForm(false)">
                            <i class="fas fa-times"></i> Hủy bỏ
                        </button>
                    </div>
                </form>
            </div>

            <!-- FORM IMPORT EXCEL -->
            <div id="importForm" class="info-card" style="display: none; margin-top: 20px; border: 2px dashed #38bdf8; background: #1e293b;">
                <h4 style="color: #38bdf8; margin-bottom: 15px;">
                    <i class="fas fa-file-import"></i> Nhập dữ liệu từ Excel
                </h4>
                <form action="?controller=ServiceController&action=importExcel" method="POST" enctype="multipart/form-data">
                    <div style="text-align: center; padding: 30px; border-radius: 8px; background: #2d333b;">
                        <i class="fas fa-cloud-upload-alt" style="font-size: 40px; color: #38bdf8; margin-bottom: 10px;"></i>
                        <p style="color: #ccc; margin-bottom: 15px;">Chọn file Excel (.xlsx) có cấu trúc: Mã DV | Tên DV | Mô Tả | Chi Phí</p>
                        
                        <input type="file" name="excel_file" id="excel_file_input" accept=".xlsx,.xls" required style="margin-bottom: 20px; color: white;">
                        
                        <div style="display: flex; gap: 10px; justify-content: center;">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-upload"></i> Bắt đầu tải lên
                            </button>
                            <button type="button" class="btn btn-danger" onclick="toggleImport(false)">
                                <i class="fas fa-times"></i> Hủy bỏ
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- BẢNG DANH SÁCH DỊCH VỤ -->
            <div class="table-container" style="margin-top: 20px;">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Mã Dịch Vụ</th>
                            <th>Tên Dịch Vụ</th>
                            <th>Chi Phí Dịch Vụ</th>
                            <th>Mô Tả Dịch Vụ</th>
                            <th style="text-align: center;">Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($data['services'])): ?>
                            <?php foreach($data['services'] as $row): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($row['MaDichVu']) ?></strong></td>
                                <td><?= htmlspecialchars($row['TenDichVu']) ?></td>
                                <td class="salary-text"><?= number_format($row['ChiPhiDichVu']) ?> đ</td>
                                <td style="max-width: 300px; overflow: hidden; text-overflow: ellipsis;">
                                    <?= htmlspecialchars($row['MoTaDichVu']) ?>
                                </td>
                                <td class="action-buttons">
                                    <button class="btn-icon edit" 
                                            onclick="editService('<?= htmlspecialchars($row['MaDichVu']) ?>',
                                                               '<?= htmlspecialchars($row['TenDichVu']) ?>',
                                                               <?= $row['ChiPhiDichVu'] ?>,
                                                               '<?= htmlspecialchars($row['MoTaDichVu']) ?>')">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <a href="?controller=ServiceController&action=deleteService&id=<?= htmlspecialchars($row['MaDichVu']) ?>" 
                                       class="btn-icon delete" 
                                       onclick="return confirm('Bạn có chắc chắn muốn xóa dịch vụ này?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" style="text-align:center; padding: 30px;">
                                    <i class="fas fa-inbox" style="font-size: 40px; color: #ccc; margin-bottom: 10px;"></i>
                                    <p style="color: #999;">Không tìm thấy dữ liệu dịch vụ phù hợp.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</div>

<script>
/**
 * Hàm quản lý trạng thái hiển thị của UI
 * @param activeForm: 'add' | 'import' | null
 */
function manageDisplay(activeForm) {
    const tables = document.querySelectorAll('.table-container');
    const toolbar = document.querySelector('.toolbar');
    const addForm = document.getElementById('serviceForm');
    const importForm = document.getElementById('importForm');

    // 1. Ẩn tất cả trước khi hiển thị cái mới
    tables.forEach(t => t.style.display = 'none');
    if(toolbar) toolbar.style.display = 'none';
    if(addForm) addForm.style.display = 'none';
    if(importForm) importForm.style.display = 'none';

    // 2. Kiểm tra trạng thái để hiển thị lại
    if (activeForm === 'add') {
        addForm.style.display = 'block';
    } else if (activeForm === 'import') {
        importForm.style.display = 'block';
    } else {
        // Mặc định hiện lại bảng và thanh công cụ
        tables.forEach(t => t.style.display = 'block');
        if(toolbar) toolbar.style.display = 'flex';
    }
}

// Bật/Tắt Form Import
function toggleImport(show) {
    manageDisplay(show ? 'import' : null);
}

// Bật/Tắt Form Thêm mới
function toggleForm(show = true) {
    if (show) {
        manageDisplay('add');
        // Reset trạng thái về chế độ THÊM
        document.getElementById('isEdit').value = "0";
        document.getElementById('MaDichVu').readOnly = false;
        document.getElementById('formTitle').innerText = "Thêm Dịch Vụ Mới";
        
        const formObj = document.getElementById('mainServiceForm');
        if(formObj) formObj.reset();
    } else {
        manageDisplay(null);
    }
}

// Chế độ Sửa (Dùng chung UI với Thêm)
function editService(id, name, price, desc) {
    manageDisplay('add'); 
    
    document.getElementById('formTitle').innerHTML = '<i class="fas fa-edit"></i> Chỉnh sửa dịch vụ: ' + id;
    document.getElementById('isEdit').value = "1";
    document.getElementById('MaDichVu').value = id;
    document.getElementById('MaDichVu').readOnly = true;
    document.getElementById('TenDichVu').value = name;
    document.getElementById('ChiPhiDichVu').value = price;
    document.getElementById('MoTaDichVu').value = desc;
    
    // Cuộn lên đầu trang mượt mà để thấy Form
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// Xác nhận đăng xuất
function confirmLogout(event) {
    const isConfirmed = confirm("Bạn có chắc chắn muốn đăng xuất không?");
    
    if (isConfirmed) {
        return true; 
    } else {
        event.preventDefault();
        return false;
    }
}
</script>

</body>
</html>