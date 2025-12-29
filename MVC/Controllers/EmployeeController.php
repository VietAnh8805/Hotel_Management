<?php
class EmployeeController extends controller {
    protected $empModel;

    public function __construct() {
        // Khởi tạo model Nhân viên
        $this->empModel = $this->model("EmployeeModel");
    }

    // Hiển thị trang danh sách
    public function index() {
        $employees = $this->empModel->getList();
        $departments = $this->empModel->getDepartments();

        $this->view("Admin/Employee", [
            "employees" => $employees,
            "departments" => $departments
        ]);
    }

    // Xử lý lưu (Thêm/Sửa)
    public function save() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $result = $this->empModel->save($_POST, $_POST['isEdit']);
            if ($result) {
                header("Location: ?controller=EmployeeController&action=index&status=success");
            } else {
                header("Location: ?controller=EmployeeController&action=index&status=error");
            }
        }
    }

    // Xử lý xóa
    public function delete() {
        if (isset($_GET['id'])) {
            $this->empModel->delete($_GET['id']);
        }
        header("Location: ?controller=EmployeeController&action=index");
    }
}
?>