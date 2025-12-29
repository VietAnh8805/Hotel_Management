<?php
class EmployeeController extends controller {
    protected $empModel;

    public function __construct() {
        $this->empModel = $this->model("EmployeeModel");
    }

    // Trang danh sách + Tìm kiếm
    public function index() {
        $keyword = isset($_POST['keyword']) ? $_POST['keyword'] : "";
        $employees = $this->empModel->getList($keyword);
        $departments = $this->empModel->getDepartments();

        ob_start();
        $this->view("Pages/Employee", [
            "employees" => $employees,
            "departments" => $departments,
            "keyword" => $keyword
        ]);
        $content = ob_get_clean();
        $this->view("Master", ["content" => $content]);
    }

    // Xử lý lưu
    public function save() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $result = $this->empModel->save($_POST, $_POST['isEdit']);
            if ($result) {
                echo "<script>alert('Thành công!'); window.location.href='?controller=EmployeeController&action=index';</script>";
            } else {
                echo "<script>alert('Lỗi!'); window.history.back();</script>";
            }
        }
    }

    // Xử lý xóa
    public function delete() {
        if (isset($_GET['id'])) {
            $this->empModel->delete($_GET['id']);
        }
        echo "<script>window.location.href='?controller=EmployeeController&action=index';</script>";
    }

public function exportExcel() {
    $keyword = isset($_GET['keyword']) ? $_GET['keyword'] : "";
    $employees = $this->empModel->getList($keyword);

    // Tên file
    header('Content-Type: application/vnd.ms-excel; charset=utf-8');
    header('Content-Disposition: attachment; filename=NhanVien.xls');
    
    echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
    echo "<table border='1'>
            <tr style='background:#d5d5d5;font-weight:bold'>
                <th>Mã NV</th>
                <th>Họ</th>
                <th>Tên</th>
                <th>Bộ Phận</th>
                <th>CCCD</th>
                <th>SĐT</th>
                <th>Email</th>
                <th>Chức Danh</th>
                <th>Ngày Vào</th>
                <th>Địa Chỉ</th>
            </tr>";

    while ($row = mysqli_fetch_assoc($employees)) {
        echo "<tr>
                <td>{$row['MaNhanVien']}</td>
                <td>{$row['HoNhanVien']}</td>
                <td>{$row['TenNhanVien']}</td>
                <td>{$row['TenBoPhan']}</td>
                <td>{$row['CMND_CCCD']}</td>
                <td>{$row['SoDienThoaiNV']}</td>
                <td>{$row['EmailNhanVien']}</td>
                <td>{$row['ChucDanhNV']}</td>
                <td>{$row['NgayVaoLam']}</td>
                <td>{$row['DiaChi']}</td>
            </tr>";
    }

    echo "</table>";
}
public function importExcel(){
    if($_SERVER['REQUEST_METHOD']=="POST" && isset($_FILES['excel_file'])){

        $file = $_FILES['excel_file']['tmp_name'];

        // Load PHPExcel
        $libPath = dirname(__DIR__,2)."/Public/Classes/PHPExcel.php";
        if(!file_exists($libPath)) die("Thiếu thư viện tại: ".$libPath);
        require_once $libPath;

        $obj = PHPExcel_IOFactory::load($file);
        $sheet = $obj->getSheet(0);

        $model = $this->model("EmployeeModel");
        $success=0;$duplicate=0;

        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn(); // Lấy cột cuối (VD: H, I,...)

        // Tạo mảng tên cột theo header dòng 1
        $headers = [];
        for ($col = 'A'; $col <= $highestColumn; $col++) {
            $headers[] = trim($sheet->getCell($col.'1')->getValue());
        }

        // Import dữ liệu từ dòng 2 đến cuối
        for($row=2;$row<=$highestRow;$row++){

            $rowData=[];

            // Đọc tất cả cột
            $colIndex = 0;
            for($col = 'A'; $col <= $highestColumn; $col++){
                $rowData[$headers[$colIndex]] = trim($sheet->getCell("$col$row")->getValue());
                $colIndex++;
            }

            $id = $rowData['MaNV'] ?? null;
            if($id=="") continue;

            if(!$model->checkDuplicate($id)){
                $model->insertFull($rowData); // ← xử lý insert full trường
                $success++;
            }else $duplicate++;
        }

        echo "<script>
            alert('Import hoàn tất! Thêm: $success | Trùng: $duplicate');
            window.location='?controller=EmployeeController&action=index';
        </script>";
    }
}
}