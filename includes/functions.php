<?php
// เริ่ม session ถ้ายังไม่ได้เริ่ม
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// ฟังก์ชันตรวจสอบการเข้าสู่ระบบ
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// ฟังก์ชันบังคับให้ผู้ใช้ล็อกอินก่อนเข้าใช้งาน
function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: index.php");
        exit;
    }
}

// ฟังก์ชันตรวจสอบการบันทึกข้อมูลซ้ำในวันเดียวกันและคาบเดียวกัน
function checkDuplicateAttendance($pdo, $student_id, $period) {
    $today = date('Y-m-d');
    
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM attendance WHERE student_id = ? AND date = ? AND period = ?");
    $stmt->execute([$student_id, $today, $period]);
    
    return $stmt->fetchColumn() > 0;
}

// ฟังก์ชันบันทึกการเข้าเรียน
function recordAttendance($pdo, $student_id, $period) {
    $today = date('Y-m-d');
    $now = date('H:i:s');
    
    // ตรวจสอบข้อมูลซ้ำก่อนบันทึก
    if (checkDuplicateAttendance($pdo, $student_id, $period)) {
        return [
            'success' => false,
            'message' => 'นักเรียนรหัส ' . $student_id . ' ได้บันทึกการเข้าเรียนในคาบนี้ไปแล้ว'
        ];
    }
    
    try {
        $stmt = $pdo->prepare("INSERT INTO attendance (student_id, date, time, period) VALUES (?, ?, ?, ?)");
        $stmt->execute([$student_id, $today, $now, $period]);
        
        return [
            'success' => true,
            'message' => 'บันทึกข้อมูลสำเร็จ'
        ];
    } catch (PDOException $e) {
        return [
            'success' => false,
            'message' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $e->getMessage()
        ];
    }
}

// ฟังก์ชันค้นหาข้อมูลนักเรียนจากรหัสนักเรียน
function getStudentInfo($pdo, $student_id) {
    $stmt = $pdo->prepare("SELECT * FROM students WHERE student_id = ?");
    $stmt->execute([$student_id]);
    
    return $stmt->fetch();
}
?>
