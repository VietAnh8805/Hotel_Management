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
                <li class="active">
                    <a href="?controller=AdminController&action=employee">
                        <i class="fas fa-users"></i>Quản lý Nhân viên
                    </a>
                </li>
                <li><a href="#"><i class="fas fa-user-shield"></i>Quản lý Tài khoản</a></li>
                <li><a href="#"><i class="fas fa-bed"></i>Quản lý Loại phòng</a></li>
                <li><a href="#"><i class="fas fa-door-open"></i>Quản lý Phòng</a></li>
                <li><a href="#"><i class="fas fa-concierge-bell"></i>Quản lý Dịch vụ</a></li>
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
                    
                    <form action="?controller=AdminController&action=employee" method="POST" style="display: flex; gap: 5px;">
                        <input type="text" name="keyword" value="<?= isset($_POST['keyword']) ? $_POST['keyword'] : '' ?>" placeholder="Tìm tên, mã, CCCD..." 
                               style="padding: 8px 15px; border-radius: 8px; border: 1px solid var(--border-color); background: #555960ff; color: white;">
                        <button type="submit" name="search" class="btn btn-outline" style="padding: 8px 15px;">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>

                <div class="right-tools" style="display: flex; gap: 10px;">
                    <?php $currentKeyword = isset($_POST['keyword']) ? $_POST['keyword'] : (isset($_GET['keyword']) ? $_GET['keyword'] : ''); ?>
                    <a href="?controller=AdminController&action=exportEmployeeExcel&keyword=<?= $currentKeyword ?>" class="btn-custom-white">
                        <i class="fas fa-file-excel" style="color: #16a34a;"></i> Xuất Excel
                    </a>
                    <button class="btn-custom-white" onclick="toggleImport(true)">
                        <i class="fas fa-file-import"></i> Upload File
                    </button>
                </div>
            </div>

            <div id="employeeForm" class="info-card" style="display: none; margin-top: 20px;">
                <h4 id="formTitle" style="color: var(--ocean-blue); margin-bottom: 15px;">Thêm nhân viên mới</h4>
                <form id="mainEmpForm" action="?controller=AdminController&action=saveEmployee" method="POST">
                    <div class="form-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                        <div class="form-group">
                            <label>Mã Nhân Viên</label>
                            <input type="text" name="MaNhanVien" id="MaNhanVien" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Họ & Tên đệm</label>
                            <input type="text" name="HoNhanVien" id="HoNhanVien" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Tên</label>
                            <input type="text" name="TenNhanVien" id="TenNhanVien" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Bộ Phận</label>
                            <select name="MaBoPhan" id="MaBoPhan" class="form-control">
                                <option value="">-- Chọn bộ phận --</option>
                                <?php if(!empty($data['departments'])): ?>
                                    <?php foreach($data['departments'] as $bp): ?>
                                        <option value="<?= $bp['MaBoPhan'] ?>"><?= $bp['TenBoPhan'] ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Số CCCD</label>
                            <input type="text" name="CMND_CCCD" id="CMND_CCCD" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Chức Danh</label>
                            <input type="text" name="ChucDanhNV" id="ChucDanhNV" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Số Điện Thoại</label>
                            <input type="text" name="SoDienThoaiNV" id="SoDienThoaiNV" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="EmailNhanVien" id="EmailNhanVien" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Ngày Vào Làm</label>
                            <input type="date" name="NgayVaoLam" id="NgayVaoLam" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Địa Chỉ</label>
                            <input type="text" name="DiaChi" id="DiaChi" class="form-control">
                        </div>
                    </div>
                    <div style="margin-top: 20px; display: flex; gap: 10px;">
                        <input type="hidden" name="isEdit" id="isEdit" value="0">
                        <button type="submit" class="btn btn-primary">Xác nhận Lưu</button>
                        <button type="button" class="btn btn-danger" onclick="toggleForm(false)">Hủy bỏ</button>
                    </div>
                </form>
            </div>

            <div class="table-container" style="margin-top: 20px; overflow-x: auto;">
    <table class="admin-table" style="width: 100%; min-width: 1200px;">
        <thead>
            <tr>
                <th>Mã NV</th>
                <th>Họ & Tên</th>
                <th>Bộ Phận</th>
                <th>CCCD</th>
                <th>SĐT</th>
                <th>Email</th>
                <th>Chức Danh</th>
                <th>Ngày Vào</th>
                <th>Địa Chỉ</th>
                <th style="text-align: center;">Thao Tác</th>
            </tr>
        </thead>
        <tbody>
            <?php if(!empty($data['employees'])): ?>
                <?php foreach($data['employees'] as $nv): ?>
                <tr>
                    <td><strong><?= $nv['MaNhanVien'] ?></strong></td>
                    <td><?= $nv['HoNhanVien'] . ' ' . $nv['TenNhanVien'] ?></td>
                    <td><span class="badge"><?= $nv['TenBoPhan'] ?? 'Chưa xếp' ?></span></td>
                    <td><?= $nv['CMND_CCCD'] ?></td>
                    <td><?= $nv['SoDienThoaiNV'] ?></td>
                    <td style="color: #38bdf8; font-size: 0.85rem;"><?= $nv['EmailNhanVien'] ?></td>
                    <td><?= $nv['ChucDanhNV'] ?></td>
                    <td><?= !empty($nv['NgayVaoLam']) ? date('d/m/Y', strtotime($nv['NgayVaoLam'])) : '' ?></td>
                    <td title="<?= $nv['DiaChi'] ?>">
                        <div style="max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                            <?= $nv['DiaChi'] ?>
                        </div>
                    </td>
                    <td class="action-buttons">
                        <button class="btn-icon edit" onclick='editEmp(<?= json_encode($nv) ?>)'>
                            <i class="fas fa-edit"></i>
                        </button>
                        <a href="?controller=AdminController&action=deleteEmployee&id=<?= $nv['MaNhanVien'] ?>" 
                           class="btn-icon delete" onclick="return confirm('Xác nhận xóa?')">
                            <i class="fas fa-trash"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="10" style="text-align:center;">Không tìm thấy nhân viên nào.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
            </div>
        </section>
    </main>
</div>

<script>
// Logic hiển thị Form
function manageDisplay(activeForm) {
    const table = document.querySelector('.table-container');
    const toolbar = document.querySelector('.toolbar');
    const addForm = document.getElementById('employeeForm');
    
    if(table) table.style.display = activeForm ? 'none' : 'block';
    if(toolbar) toolbar.style.display = activeForm ? 'none' : 'flex';
    if(addForm) addForm.style.display = (activeForm === 'add') ? 'block' : 'none';
}

function toggleForm(show) { 
    if(show) {
        document.getElementById('mainEmpForm').reset();
        document.getElementById('isEdit').value = "0";
        document.getElementById('MaNhanVien').readOnly = false;
        document.getElementById('formTitle').innerText = "Thêm Nhân Viên Mới";
        manageDisplay('add');
    } else {
        manageDisplay(null);
    }
}

// Đổ dữ liệu vào Form khi sửa
function editEmp(data) {
    manageDisplay('add'); 
    document.getElementById('formTitle').innerText = "Sửa nhân viên: " + data.MaNhanVien;
    document.getElementById('isEdit').value = "1";
    document.getElementById('MaNhanVien').value = data.MaNhanVien;
    document.getElementById('MaNhanVien').readOnly = true;
    
    document.getElementById('HoNhanVien').value = data.HoNhanVien;
    document.getElementById('TenNhanVien').value = data.TenNhanVien;
    document.getElementById('MaBoPhan').value = data.MaBoPhan;
    document.getElementById('CMND_CCCD').value = data.CMND_CCCD;
    document.getElementById('ChucDanhNV').value = data.ChucDanhNV;
    document.getElementById('SoDienThoaiNV').value = data.SoDienThoaiNV;
    document.getElementById('EmailNhanVien').value = data.EmailNhanVien || '';
    document.getElementById('NgayVaoLam').value = data.NgayVaoLam;
    document.getElementById('DiaChi').value = data.DiaChi || '';
}
</script>