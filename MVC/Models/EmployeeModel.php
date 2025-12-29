<?php
class EmployeeModel extends connectDB {
    
    // 1. Lấy danh sách: Đảm bảo lấy nv.* để có đủ Email, DiaChi...
    public function getList($keyword = "") {
        $sql = "SELECT nv.*, bp.TenBoPhan 
                FROM hotels_employees nv 
                LEFT JOIN hotels_departments bp ON nv.MaBoPhan = bp.MaBoPhan";
        
        if (!empty($keyword)) {
            $sql .= " WHERE nv.MaNhanVien LIKE '%$keyword%' 
                      OR nv.TenNhanVien LIKE '%$keyword%' 
                      OR nv.CMND_CCCD LIKE '%$keyword%'
                      OR nv.SoDienThoaiNV LIKE '%$keyword%'";
        }
        
        return mysqli_query($this->con, $sql);
    }

    public function getDepartments() {
        return mysqli_query($this->con, "SELECT MaBoPhan, TenBoPhan FROM hotels_departments");
    }

    // 2. Hàm Lưu: Đã bổ sung đầy đủ EmailNhanVien và DiaChi
   public function save($data, $isEdit) {
    $ma = mysqli_real_escape_string($this->con, $data['MaNhanVien']);
    $ho = mysqli_real_escape_string($this->con, $data['HoNhanVien']);
    $ten = mysqli_real_escape_string($this->con, $data['TenNhanVien']);
    $cccd = mysqli_real_escape_string($this->con, $data['CMND_CCCD']);
    $sdt = mysqli_real_escape_string($this->con, $data['SoDienThoaiNV']);
    $email = mysqli_real_escape_string($this->con, $data['EmailNhanVien']);
    $chucdanh = mysqli_real_escape_string($this->con, $data['ChucDanhNV']);
    $diachi = mysqli_real_escape_string($this->con, $data['DiaChi']);

    $mabp   = !empty($data['MaBoPhan']) ? "'" . $data['MaBoPhan'] . "'" : "NULL";
    $ngayvao = !empty($data['NgayVaoLam']) ? "'" . $data['NgayVaoLam'] . "'" : "NULL";

    if($isEdit == "0") {
        // INSERT
        $sql = "INSERT INTO hotels_employees 
                (MaNhanVien, HoNhanVien, TenNhanVien, CMND_CCCD, SoDienThoaiNV, EmailNhanVien, ChucDanhNV, MaBoPhan, NgayVaoLam, DiaChi)
                VALUES 
                ('$ma','$ho','$ten','$cccd','$sdt','$email','$chucdanh',$mabp,$ngayvao,'$diachi')";
    } else {
        // UPDATE
        $sql = "UPDATE hotels_employees SET
                HoNhanVien='$ho',
                TenNhanVien='$ten',
                CMND_CCCD='$cccd',
                SoDienThoaiNV='$sdt',
                EmailNhanVien='$email',
                ChucDanhNV='$chucdanh',
                MaBoPhan=$mabp,
                NgayVaoLam=$ngayvao,
                DiaChi='$diachi'
                WHERE MaNhanVien='$ma'";
    }

    // DEBUG để thấy lỗi rõ ràng
    if(!mysqli_query($this->con,$sql)){
        die("SQL ERROR: " . mysqli_error($this->con) . "<br>Query: " . $sql);
    }

    return true;
}
   public function delete($id) {
    // Câu lệnh xóa dựa trên khóa chính MaNhanVien
    $sql = "DELETE FROM hotels_employees WHERE MaNhanVien = '$id'";
    
    return mysqli_query($this->con, $sql);
    }

    public function importSave($data) {
    $ma = mysqli_real_escape_string($this->con, $data['MaNhanVien']);
    
    // Kiểm tra xem mã nhân viên đã tồn tại chưa
    $check = mysqli_query($this->con, "SELECT MaNhanVien FROM hotels_employees WHERE MaNhanVien = '$ma'");
    
    if (mysqli_num_rows($check) > 0) {
        // Nếu tồn tại thì UPDATE
        return $this->save($data, "1"); 
    } else {
        // Nếu chưa có thì INSERT
        return $this->save($data, "0");
    }
    }

    public function checkDuplicate($id) {
    $id = mysqli_real_escape_string($this->con, $id);
    $sql = "SELECT MaNhanVien FROM hotels_employees WHERE MaNhanVien = '$id'";
    $result = mysqli_query($this->con, $sql);
    return mysqli_num_rows($result) > 0;
}
}