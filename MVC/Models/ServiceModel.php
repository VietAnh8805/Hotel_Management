<?php
class ServiceModel extends connectDB {
    
    // Lấy tất cả dịch vụ
    public function getAllServices() {
        $sql = "SELECT * FROM hotelservice_services ORDER BY MaDichVu ASC";
        return $this->select($sql);
    }

    // Tìm kiếm dịch vụ theo từ khóa
    public function searchServices($keyword) {
        $keyword = mysqli_real_escape_string($this->con, $keyword);
        $sql = "SELECT * FROM hotelservice_services 
                WHERE MaDichVu LIKE '%$keyword%' 
                OR TenDichVu LIKE '%$keyword%' 
                OR MoTaDichVu LIKE '%$keyword%'
                ORDER BY MaDichVu ASC";
        return $this->select($sql);
    }

    // Lấy thông tin 1 dịch vụ theo mã
    public function getServiceById($maDichVu) {
        $maDichVu = mysqli_real_escape_string($this->con, $maDichVu);
        $sql = "SELECT * FROM hotelservice_services WHERE MaDichVu = '$maDichVu'";
        return $this->selectOne($sql);
    }

    // Thêm dịch vụ mới
    public function insertService($data) {
        $maDichVu = mysqli_real_escape_string($this->con, $data['MaDichVu']);
        $tenDichVu = mysqli_real_escape_string($this->con, $data['TenDichVu']);
        $moTaDichVu = mysqli_real_escape_string($this->con, $data['MoTaDichVu']);
        $chiPhiDichVu = floatval($data['ChiPhiDichVu']);
        
        $sql = "INSERT INTO hotelservice_services (MaDichVu, TenDichVu, MoTaDichVu, ChiPhiDichVu) 
                VALUES ('$maDichVu', '$tenDichVu', '$moTaDichVu', $chiPhiDichVu)";
        
        return $this->execute($sql);
    }

    // Cập nhật dịch vụ
    public function updateService($data) {
        $maDichVu = mysqli_real_escape_string($this->con, $data['MaDichVu']);
        $tenDichVu = mysqli_real_escape_string($this->con, $data['TenDichVu']);
        $moTaDichVu = mysqli_real_escape_string($this->con, $data['MoTaDichVu']);
        $chiPhiDichVu = floatval($data['ChiPhiDichVu']);
        
        $sql = "UPDATE hotelservice_services 
                SET TenDichVu = '$tenDichVu',
                    MoTaDichVu = '$moTaDichVu',
                    ChiPhiDichVu = $chiPhiDichVu
                WHERE MaDichVu = '$maDichVu'";
        
        return $this->execute($sql);
    }

    // Xóa dịch vụ
    public function deleteService($maDichVu) {
        $maDichVu = mysqli_real_escape_string($this->con, $maDichVu);
        $sql = "DELETE FROM hotelservice_services WHERE MaDichVu = '$maDichVu'";
        return $this->execute($sql);
    }

    // Kiểm tra mã dịch vụ đã tồn tại chưa
    public function isServiceExists($maDichVu) {
        $maDichVu = mysqli_real_escape_string($this->con, $maDichVu);
        $sql = "SELECT COUNT(*) as count FROM hotelservice_services WHERE MaDichVu = '$maDichVu'";
        $result = $this->selectOne($sql);
        return $result['count'] > 0;
    }

    // Lấy danh sách dịch vụ đã sử dụng trong một đặt phòng
    public function getServicesUsedByBooking($maDatPhong) {
        $maDatPhong = intval($maDatPhong);
        $sql = "SELECT su.*, s.TenDichVu, s.MoTaDichVu 
                FROM hotelservice_servicesused su
                JOIN hotelservice_services s ON su.MaDichVu = s.MaDichVu
                WHERE su.MaDatPhong = $maDatPhong
                ORDER BY su.NgaySuDung DESC";
        return $this->select($sql);
    }

    // Tính tổng chi phí dịch vụ của một đặt phòng
    public function getTotalServiceCostByBooking($maDatPhong) {
        $maDatPhong = intval($maDatPhong);
        $sql = "SELECT SUM(ThanhTien) as TongChiPhi 
                FROM hotelservice_servicesused 
                WHERE MaDatPhong = $maDatPhong";
        $result = $this->selectOne($sql);
        return $result['TongChiPhi'] ?? 0;
    }

    // Thêm dịch vụ đã sử dụng
    public function insertServiceUsed($data) {
        $maDichVu = mysqli_real_escape_string($this->con, $data['MaDichVu']);
        $maDatPhong = intval($data['MaDatPhong']);
        $soLuong = intval($data['SoLuong']);
        $donGia = floatval($data['DonGia']);
        $thanhTien = floatval($data['ThanhTien']);
        
        $sql = "INSERT INTO hotelservice_servicesused (MaDichVu, MaDatPhong, SoLuong, DonGia, ThanhTien) 
                VALUES ('$maDichVu', $maDatPhong, $soLuong, $donGia, $thanhTien)";
        
        return $this->execute($sql);
    }

    // Đếm tổng số dịch vụ
    public function countAllServices() {
        $sql = "SELECT COUNT(*) as total FROM hotelservice_services";
        $result = $this->selectOne($sql);
        return $result['total'] ?? 0;
    }

    // Lấy dịch vụ có chi phí cao nhất
    public function getTopExpensiveServices($limit = 5) {
        $limit = intval($limit);
        $sql = "SELECT * FROM hotelservice_services 
                ORDER BY ChiPhiDichVu DESC 
                LIMIT $limit";
        return $this->select($sql);
    }

    // Lấy dịch vụ được sử dụng nhiều nhất
    public function getMostUsedServices($limit = 5) {
        $limit = intval($limit);
        $sql = "SELECT s.*, COUNT(su.MaDichVuSuDung) as SoLanSuDung, SUM(su.ThanhTien) as TongDoanhThu
                FROM hotelservice_services s
                LEFT JOIN hotelservice_servicesused su ON s.MaDichVu = su.MaDichVu
                GROUP BY s.MaDichVu
                ORDER BY SoLanSuDung DESC
                LIMIT $limit";
        return $this->select($sql);
    }
}
?>