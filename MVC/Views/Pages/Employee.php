<div class="admin-wrapper">
    <!-- SIDEBAR -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <i class="fas fa-hotel"></i><span>Hotel Admin</span>
        </div>
        
        <nav class="sidebar-menu">
            <ul>
                <li><a href="?controller=AdminController&action=department"><i class="fas fa-th-large"></i> Quản lý Bộ phận</a></li>
                <li class="active"><a href="?controller=EmployeeController&action=index"><i class="fas fa-users"></i> Quản lý Nhân viên</a></li>
                <li><a href="#"><i class="fas fa-user-shield"></i> Quản lý Tài khoản</a></li>
                <li><a href="#"><i class="fas fa-bed"></i> Quản lý Loại phòng</a></li>
                <li><a href="#"><i class="fas fa-door-open"></i> Quản lý Phòng</a></li>
                <li><a href="#"><i class="fas fa-concierge-bell"></i> Quản lý Dịch vụ</a></li>
                <li><a href="#"><i class="fas fa-tags"></i> Quản lý Giảm giá</a></li>
                <li><a href="#"><i class="fas fa-address-book"></i> Quản lý Khách hàng</a></li>
                <li><a href="#"><i class="fas fa-calendar-check"></i> Quản lý Đặt phòng</a></li>
                <li><a href="#"><i class="fas fa-credit-card"></i> Thanh toán & Trả phòng</a></li>
                <li><a href="#"><i class="fas fa-chart-line"></i> Báo cáo & Thống kê</a></li>
            </ul>
        </nav>

        <div class="sidebar-footer">
            <a href="?controller=AuthController&action=logout" class="btn-logout" onclick="return confirm('Đăng xuất?')">
                <i class="fas fa-sign-out-alt"></i>Đăng xuất
            </a>
        </div>
    </aside>

    <!-- MAIN -->
    <main class="main-content">
        <header class="top-header">
            <div class="user-info">Chào mừng: <strong>Quản trị viên</strong></div>
        </header>

        <section class="content-body">

            <!-- TOOLBAR -->
            <div class="toolbar" style="display:flex;justify-content:space-between;align-items:center;">
                <div style="display:flex;gap:15px;align-items:center;">
                    <button class="btn btn-primary" onclick="toggleForm(true)">
                        <i class="fas fa-plus-circle"></i> Thêm mới
                    </button>

                    <form action="?controller=EmployeeController&action=index" method="POST" style="display:flex;gap:5px;">
                        <input type="text" name="keyword" placeholder="Tìm tên, mã, CCCD..."
                               value="<?= isset($_POST['keyword']) ? $_POST['keyword'] : '' ?>"
                               style="padding:8px;border-radius:8px;border:1px solid #555;background:#555960;color:white;">
                        <button type="submit" name="search" class="btn btn-outline"><i class="fas fa-search"></i></button>
                    </form>
                </div>

                <div style="display:flex;gap:10px;">
                    <?php $kw = $_POST['keyword'] ?? ($_GET['keyword'] ?? '') ?>
                    <a href="?controller=EmployeeController&action=exportExcel&keyword=<?= $kw ?>" class="btn-custom-white">
                        <i class="fas fa-file-excel" style="color:#16a34a;"></i> Xuất Excel
                    </a>
                    <button class="btn-custom-white" onclick="toggleImport(true)">
                        <i class="fas fa-file-import"></i> Upload File
                    </button>
                </div>
            </div>

            <!-- FORM NHÂN VIÊN -->
            <div id="employeeForm" class="info-card" style="display:none;margin-top:20px;">
                <h4 id="formTitle">Thêm nhân viên mới</h4>

                <form id="mainEmpForm" action="?controller=EmployeeController&action=save" method="POST">
                    <div class="form-grid" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:15px;">

                        <div class="form-group"><label>Mã NV</label><input type="text" name="MaNhanVien" id="MaNhanVien" class="form-control" required></div>
                        <div class="form-group"><label>Họ & Tên đệm</label><input type="text" name="HoNhanVien" id="HoNhanVien" class="form-control" required></div>
                        <div class="form-group"><label>Tên</label><input type="text" name="TenNhanVien" id="TenNhanVien" class="form-control" required></div>
                        <div class="form-group"><label>Chức Danh</label><input type="text" name="ChucDanhNV" id="ChucDanhNV" class="form-control"></div>
                        <div class="form-group"><label>Số Điện Thoại</label><input type="text" name="SoDienThoaiNV" id="SoDienThoaiNV" class="form-control"></div>
                        <div class="form-group"><label>Email</label><input type="email" name="EmailNhanVien" id="EmailNhanVien" class="form-control"></div>
                        <div class="form-group"><label>Ngày Vào Làm</label><input type="date" name="NgayVaoLam" id="NgayVaoLam" class="form-control"></div>
                        <div class="form-group"><label>Địa Chỉ</label><input type="text" name="DiaChi" id="DiaChi" class="form-control"></div>

                        <div class="form-group"><label>Bộ Phận</label>
                            <select name="MaBoPhan" id="MaBoPhan" class="form-control">
                                <option value="">-- Chọn bộ phận --</option>
                                <?php foreach($data['departments'] as $bp): ?>
                                <option value="<?= $bp['MaBoPhan'] ?>"><?= $bp['TenBoPhan'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group"><label>Số CCCD</label><input type="text" name="CMND_CCCD" id="CMND_CCCD" class="form-control" required></div>
                    </div>

                    <div style="margin-top:20px;display:flex;gap:10px;">
                        <input type="hidden" name="isEdit" id="isEdit" value="0">
                        <button type="submit" class="btn btn-primary">Lưu</button>
                        <button type="button" class="btn btn-danger" onclick="toggleForm(false)">Hủy</button>
                    </div>
                </form>
            </div>

            <!-- TABLE -->
            <div class="table-container" style="margin-top:20px;overflow-x:auto;">
                <table class="admin-table" style="width:100%;min-width:1200px;">
                    <thead>
                    <tr>
                        <th>Mã NV</th><th>Họ</th><th>Tên</th><th>Chức Danh</th><th>SĐT</th><th>Email</th>
                        <th>Ngày Vào</th><th>Địa Chỉ</th><th>Bộ Phận</th><th>CCCD</th><th>Thao tác</th>
                    </tr>
                    </thead>

                    <tbody>
                    <?php foreach($data['employees'] as $nv): ?>
                    <tr>
                        <td><strong><?= $nv['MaNhanVien'] ?></strong></td>
                        <td><?= $nv['HoNhanVien'] ?></td>
                        <td><?= $nv['TenNhanVien'] ?></td>
                        <td><?= $nv['ChucDanhNV'] ?></td>
                        <td><?= $nv['SoDienThoaiNV'] ?></td>
                        <td style="color:#38bdf8;"><?= $nv['EmailNhanVien'] ?></td>
                        <td><?= $nv['NgayVaoLam'] ? date('d/m/Y',strtotime($nv['NgayVaoLam'])):'' ?></td>
                        <td><?= $nv['DiaChi'] ?></td>
                        <td><span class="badge"><?= $nv['TenBoPhan'] ?? 'Chưa xếp' ?></span></td>
                        <td><?= $nv['CMND_CCCD'] ?></td>

                        <td style="text-align:center;">
                            <button class="btn-icon edit" onclick='editEmp(<?= json_encode($nv) ?>)'>
                            <i class="fas fa-edit"></i>
                            </button>
                            <a href="?controller=EmployeeController&action=delete&id=<?= $nv['MaNhanVien'] ?>" class="btn-icon delete" onclick="return confirm('Xóa nhân viên này?')">
                            <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        </section>
    </main>
</div>

<script>
function toggleForm(show){
    document.getElementById("employeeForm").style.display = show ? "block" : "none";
    document.querySelector(".table-container").style.display = show ? "none":"block";
    document.querySelector(".toolbar").style.display = show ? "none":"flex";
    if(show) document.getElementById("mainEmpForm").reset();
}

function editEmp(data){
    toggleForm(true);
    document.getElementById("formTitle").innerText="Sửa nhân viên: "+data.MaNhanVien;
    document.getElementById("isEdit").value="1";
    for(let i in data) if(document.getElementById(i)) document.getElementById(i).value=data[i];
    document.getElementById("MaNhanVien").readOnly = true;
}
</script>
