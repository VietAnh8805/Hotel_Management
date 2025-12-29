<?php
class AdminController extends controller {
    
    // 1. Trang chủ Admin (Banner chào mừng)
    public function index() {
        ob_start();
        $this->view("Pages/Admin"); // Đây là file chứa cái sidebar và banner bạn gửi ở trên
        $content = ob_get_clean();
        $this->view("Master", ["content" => $content]);
    }

    // 2. Trang Quản lý bộ phận (Giao diện có Form và Bảng)
    public function department() {
        $model = $this->model("DepartmentModel");
        
        // Xử lý tìm kiếm nếu có
        if (isset($_POST['search'])) {
            $departments = $model->search($_POST['keyword']);
        } else {
            $departments = $model->getAll();
        }

        ob_start();
        // Gọi file view quản lý bộ phận
        $this->view("Pages/Department", ["departments" => $departments]); 
        $content = ob_get_clean();

        $this->view("Master", ["content" => $content]);
    }

    // 3. Trang Quản lý nhân viên
    public function employee() {
    $model = $this->model("EmployeeModel");
    
    // Kiểm tra nếu người dùng nhấn nút Search (name="search" trong view)
    $keyword = "";
    if (isset($_POST['search'])) {
        $keyword = $_POST['keyword'];
    }

    // Lấy danh sách nhân viên theo từ khóa (nếu rỗng thì lấy hết)
    $employees = $model->getList($keyword);
    $departments = $model->getDepartments();

    ob_start();
    // Truyền cả employees, departments và keyword (để hiển thị lại trên ô nhập) vào view
    $this->view("Admin/Employee", [
        "employees" => $employees,
        "departments" => $departments,
        "keyword" => $keyword
    ]);
    
    $content = ob_get_clean();
    $this->view("Master", ["content" => $content]);
}
   public function saveEmployee() {
    $model = $this->model("EmployeeModel");
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Lấy dữ liệu từ form gửi lên qua biến $_POST
        // Biến $isEdit sẽ xác định là thêm mới (0) hay sửa (1)
        $isEdit = $_POST['isEdit'];
        
        $result = $model->save($_POST, $isEdit);
        
        if ($result) {
            echo "<script>alert('Lưu dữ liệu thành công!'); window.location.href='?controller=AdminController&action=employee';</script>";
        } else {
            echo "<script>alert('Lỗi khi lưu dữ liệu!'); window.history.back();</script>";
        }
    }
}
}