<?php
// เริ่ม session
session_start();

// ลบข้อมูล session ทั้งหมด
$_SESSION = array();

// ลบ cookie ของ session
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// ทำลาย session
session_destroy();

// ไปที่หน้าล็อกอิน (เปลี่ยนจาก index.php เป็น index.html)
header("Location: index.html");
exit;
?>
