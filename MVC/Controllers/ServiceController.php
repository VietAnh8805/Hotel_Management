<?php
class ServiceController extends controller {
    private $serviceModel;

    public function __construct() {
        $this->serviceModel = $this->model('ServiceModel');
    }

    // Hiển thị danh sách dịch vụ
    public function index() {
        $keyword = '';
        $services = [];

        if (isset($_POST['search']) && isset($_POST['keyword'])) {
            $keyword = trim($_POST['keyword']);
            $services = $this->serviceModel->searchServices($keyword);
        } else {
            $services = $this->serviceModel->getAllServices();
        }

        $data = [
            'services' => $services,
            'keyword' => $keyword
        ];

        // Gọi view trực tiếp - KHÔNG dùng $this->view()
        require_once './MVC/Views/pages/service.php';
    }

    // Lưu dịch vụ (Thêm mới hoặc Cập nhật)
    public function saveService() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $isEdit = isset($_POST['isEdit']) ? $_POST['isEdit'] : 0;
            
            $serviceData = [
                'MaDichVu' => trim($_POST['MaDichVu']),
                'TenDichVu' => trim($_POST['TenDichVu']),
                'MoTaDichVu' => trim($_POST['MoTaDichVu']),
                'ChiPhiDichVu' => floatval($_POST['ChiPhiDichVu'])
            ];

            if ($isEdit == "1") {
                // Cập nhật
                $result = $this->serviceModel->updateService($serviceData);
                if ($result) {
                    echo "<script>alert('Cập nhật dịch vụ thành công!'); window.location.href='?controller=ServiceController&action=index';</script>";
                } else {
                    echo "<script>alert('Cập nhật dịch vụ thất bại!'); window.history.back();</script>";
                }
            } else {
                // Thêm mới
                $result = $this->serviceModel->insertService($serviceData);
                if ($result) {
                    echo "<script>alert('Thêm dịch vụ thành công!'); window.location.href='?controller=ServiceController&action=index';</script>";
                } else {
                    echo "<script>alert('Thêm dịch vụ thất bại! Mã dịch vụ có thể đã tồn tại.'); window.history.back();</script>";
                }
            }
        }
    }

    // Xóa dịch vụ
    public function deleteService() {
        if (isset($_GET['id'])) {
            $maDichVu = trim($_GET['id']);
            $result = $this->serviceModel->deleteService($maDichVu);
            
            if ($result) {
                echo "<script>alert('Xóa dịch vụ thành công!'); window.location.href='?controller=ServiceController&action=index';</script>";
            } else {
                echo "<script>alert('Xóa dịch vụ thất bại! Có thể dịch vụ đang được sử dụng.'); window.history.back();</script>";
            }
        }
    }

 // Xuất Excel
    public function exportExcel() {
        $services = $this->serviceModel->getAllServices();

        // Load thư viện PHPExcel
        $libPath = dirname(__DIR__, 2) . "/Public/Classes/PHPExcel.php";
        if (!file_exists($libPath)) {
            die("Không tìm thấy thư viện PHPExcel tại: " . $libPath);
        }
        require_once $libPath;

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        $sheet = $objPHPExcel->getActiveSheet();

        // Tiêu đề cột
        $sheet->setCellValue('A1', 'Mã Dịch Vụ');
        $sheet->setCellValue('B1', 'Tên Dịch Vụ');
        $sheet->setCellValue('C1', 'Mô Tả Dịch Vụ');
        $sheet->setCellValue('D1', 'Chi Phí Dịch Vụ');

        // Dữ liệu
        $rowNumber = 2;
        foreach ($services as $service) {
            $sheet->setCellValue('A' . $rowNumber, $service['MaDichVu']);
            $sheet->setCellValue('B' . $rowNumber, $service['TenDichVu']);
            $sheet->setCellValue('C' . $rowNumber, $service['MoTaDichVu']);
            $sheet->setCellValue('D' . $rowNumber, $service['ChiPhiDichVu']);
            $rowNumber++;
        }

        // Gửi file về trình duyệt
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="services.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }



    // Import Excel
   public function importExcel() {
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['excel_file'])) {

        $file = $_FILES['excel_file']['tmp_name'];

        // Load thư viện PHPExcel
        $libPath = dirname(__DIR__, 2) . "/Public/Classes/PHPExcel.php";
        if (!file_exists($libPath)) {
            die("Không tìm thấy thư viện PHPExcel tại: " . $libPath);
        }
        require_once $libPath;

        try {
            $objPHPExcel = PHPExcel_IOFactory::load($file);
            $sheet = $objPHPExcel->getSheet(0);
            $highestRow = $sheet->getHighestRow();

            // DÙNG ServiceModel
            $model = $this->model("ServiceModel");

            $success = 0;
            $failed  = 0;

            // Bỏ dòng tiêu đề
            for ($row = 2; $row <= $highestRow; $row++) {

                $maDV  = trim($sheet->getCellByColumnAndRow(0, $row)->getValue()); // A
                $tenDV = trim($sheet->getCellByColumnAndRow(1, $row)->getValue()); // B
                $moTa  = trim($sheet->getCellByColumnAndRow(2, $row)->getValue()); // C
                $chiPhi= $sheet->getCellByColumnAndRow(3, $row)->getCalculatedValue(); // D

                // Bỏ dòng rỗng
                if (empty($maDV) || empty($tenDV)) {
                    $failed++;
                    continue;
                }

                // Kiểm tra trùng mã dịch vụ
               if ($model->isServiceExists($maDV)) {
                    $failed++;
                    continue;
                }   

                // Insert
                $model->insertService([
                    'MaDichVu'     => $maDV,
                    'TenDichVu'    => $tenDV,
                    'MoTaDichVu'   => $moTa,
                    'ChiPhiDichVu' => (float)$chiPhi
                ]);

                $success++;
            }

            echo "<script>
                alert('Import thành công: $success dòng\\nThất bại: $failed dòng');
                window.location.href='?controller=ServiceController&action=index';
            </script>";

        } catch (Exception $e) {
            echo "<script>alert('Lỗi đọc file Excel: " . addslashes($e->getMessage()) . "');</script>";
        }
    }
}

}
?>