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

        header('Content-Type: application/vnd.ms-excel; charset=utf-8');
        header('Content-Disposition: attachment; filename=NhanVien.xls');
        
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
        echo "<table border='1'>
                <tr style='background:#d5d5d5;font-weight:bold'>
                    <th>Mã NV</th>
                    <th>Tên</th>
                    <th>Họ</th>
                    <th>Chức Danh</th>
                    <th>SĐT</th>
                    <th>Email</th>
                    <th>Ngày Vào</th>
                    <th>Địa Chỉ</th>
                    <th>Bộ Phận</th>
                    <th>CCCD</th>
                </tr>";

        while ($row = mysqli_fetch_assoc($employees)) {
            echo "<tr>
                    <td>{$row['MaNhanVien']}</td>
                    <td>{$row['TenNhanVien']}</td>
                    <td>{$row['HoNhanVien']}</td>
                    <td>{$row['ChucDanhNV']}</td>
                    <td>{$row['SoDienThoaiNV']}</td>
                    <td>{$row['EmailNhanVien']}</td>
                    <td>{$row['NgayVaoLam']}</td>
                    <td>{$row['DiaChi']}</td>
                    <td>{$row['TenBoPhan']}</td>
                    <td>{$row['CMND_CCCD']}</td>
                </tr>";
        }
        echo "</table>";
    }

   public function importExcel() {
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['excel_file'])) {
        $file = $_FILES['excel_file']['tmp_name'];
        $libPath = dirname(__DIR__, 2) . "/Public/Classes/PHPExcel.php";

        if (file_exists($libPath)) {
            require_once $libPath;
        } else {
            die("Không tìm thấy thư viện tại: " . $libPath);
        }

        try {
            $objPHPExcel = PHPExcel_IOFactory::load($file);
            $sheet = $objPHPExcel->getSheet(0);
            $highestRow = $sheet->getHighestRow();

            $successCount = 0;
            $duplicateCount = 0;

            for ($row = 2; $row <= $highestRow; $row++) {
                $maNV = trim($sheet->getCellByColumnAndRow(0, $row)->getValue());
                if (empty($maNV)) continue;

                // 1. KIỂM TRA TRÙNG MÃ (Sử dụng hàm checkDuplicate bạn đã có trong Model)
                if ($this->empModel->checkDuplicate($maNV)) {
                    $duplicateCount++;
                    continue; // Nếu trùng thì bỏ qua dòng này, chạy dòng tiếp theo
                }

                // 2. XỬ LÝ NGÀY THÁNG (Chuyển từ số 44936 về YYYY-MM-DD)
                $cellNgayVao = $sheet->getCellByColumnAndRow(6, $row)->getValue();
                if(is_numeric($cellNgayVao)){
                    $ngayVaoLam = date("Y-m-d", PHPExcel_Shared_Date::ExcelToPHP($cellNgayVao));
                } else {
                    $ngayVaoLam = $cellNgayVao;
                }

                $data = [
                    'MaNhanVien'    => $maNV,
                    'TenNhanVien'   => trim($sheet->getCellByColumnAndRow(1, $row)->getValue()),
                    'HoNhanVien'    => trim($sheet->getCellByColumnAndRow(2, $row)->getValue()),
                    'ChucDanhNV'    => trim($sheet->getCellByColumnAndRow(3, $row)->getValue()),
                    'SoDienThoaiNV' => trim($sheet->getCellByColumnAndRow(4, $row)->getValue()),
                    'EmailNhanVien' => trim($sheet->getCellByColumnAndRow(5, $row)->getValue()),
                    'NgayVaoLam'    => $ngayVaoLam,
                    'DiaChi'        => trim($sheet->getCellByColumnAndRow(7, $row)->getValue()),
                    'MaBoPhan'      => trim($sheet->getCellByColumnAndRow(8, $row)->getValue()),
                    'CMND_CCCD'     => trim($sheet->getCellByColumnAndRow(9, $row)->getValue()),
                ];

                if ($this->empModel->save($data, "0")) {
                    $successCount++;
                }
            }
            
            echo "<script>
                alert('Import hoàn tất! Thêm mới: $successCount, Bỏ qua trùng: $duplicateCount');
                window.location.href='?controller=EmployeeController&action=index';
            </script>";

        } catch (Exception $e) {
            die("Lỗi: " . $e->getMessage());
        }
    }
}
} 
?>