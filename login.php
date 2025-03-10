<?php
// เริ่ม session
session_start();

// ถ้าล็อกอินแล้วให้ไปที่หน้า dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

// ตรวจสอบการส่งฟอร์ม
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // ตรวจสอบค่าว่าง
    if (empty($username) || empty($password)) {
        header("Location: index.html?error=empty");
        exit;
    } else {
        // ตรวจสอบการล็อกอิน (ในที่นี้เป็นการตรวจสอบแบบง่าย ตามโจทย์)
        if ($username === 'pm4' && $password === '123421') {
            // สร้าง session
            $_SESSION['user_id'] = 1;
            $_SESSION['username'] = $username;
            
            // Redirect ไปที่หน้า dashboard
            header("Location: dashboard.php");
            exit;
        } else {
            header("Location: index.html?error=invalid");
            exit;
        }
    }
} else {
    // ถ้าไม่ได้เข้ามาด้วยวิธี POST ให้กลับไปที่หน้าล็อกอิน
    header("Location: index.html");
    exit;
}
?>
