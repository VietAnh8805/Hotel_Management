<?php
class EmployeeModel extends connectDB {
    
    // 1. Lấy danh sách: Đảm bảo lấy nv.* để có đủ Email, DiaChi...
    public function getList($keyword = "") {
    // 1. Câu lệnh SQL cơ bản lấy dữ liệu và Join với bảng bộ phận
    $sql = "SELECT nv.*, bp.TenBoPhan 
            FROM NhanVien nv 
            LEFT JOIN hotels_departments bp ON nv.MaBoPhan = bp.MaBoPhan";
    
    // 2. Nếu có từ khóa tìm kiếm, thêm điều kiện WHERE
    if (!empty($keyword)) {
        // Tìm theo Mã nhân viên HOẶC Tên nhân viên
        $sql .= " WHERE nv.MaNhanVien LIKE '%$keyword%' 
                  OR nv.TenNhanVien LIKE '%$keyword%'";
    }

    $sql .= " ORDER BY nv.MaNhanVien DESC"; // Sắp xếp mới nhất lên đầu

    return mysqli_query($this->con, $sql);
}

    public function getDepartments() {
        return mysqli_query($this->con, "SELECT MaBoPhan, TenBoPhan FROM hotels_departments");
    }

    // 2. Hàm Lưu: Đã bổ sung đầy đủ EmailNhanVien và DiaChi
    public function save($data, $isEdit) {
        $ma = $data['MaNhanVien'];
        $ho = $data['HoNhanVien'];
        $ten = $data['TenNhanVien'];
        $cccd = $data['CMND_CCCD'];
        $sdt = $data['SoDienThoaiNV'];
        $email = $data['EmailNhanVien']; // TRƯỜNG MỚI
        $chucdanh = $data['ChucDanhNV'];
        $mabp = !empty($data['MaBoPhan']) ? "'" . $data['MaBoPhan'] . "'" : "NULL";
        $ngayvao = !empty($data['NgayVaoLam']) ? "'" . $data['NgayVaoLam'] . "'" : "NULL";
        $diachi = $data['DiaChi']; // TRƯỜNG MỚI

        if ($isEdit == "0") {
            // INSERT đầy đủ 10 cột
            $sql = "INSERT INTO hotels_employees (MaNhanVien, HoNhanVien, TenNhanVien, CMND_CCCD, SoDienThoaiNV, EmailNhanVien, ChucDanhNV, MaBoPhan, NgayVaoLam, DiaChi) 
                    VALUES ('$ma', '$ho', '$ten', '$cccd', '$sdt', '$email', '$chucdanh', $mabp, $ngayvao, '$diachi')";
        } else {
            // UPDATE đầy đủ các cột
            $sql = "UPDATE hotels_employees SET 
                        HoNhanVien = '$ho', 
                        TenNhanVien = '$ten', 
                        CMND_CCCD = '$cccd', 
                        SoDienThoaiNV = '$sdt', 
                        EmailNhanVien = '$email',
                        ChucDanhNV = '$chucdanh', 
                        MaBoPhan = $mabp, 
                        NgayVaoLam = $ngayvao,
                        DiaChi = '$diachi' 
                    WHERE MaNhanVien = '$ma'";
        }
        
        return mysqli_query($this->con, $sql);
    }

    public function delete($id) {
        return mysqli_query($this->con, "DELETE FROM hotels_employees WHERE MaNhanVien = '$id'");
    }
}
?>