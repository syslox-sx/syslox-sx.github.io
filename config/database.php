<?php
// ตั้งค่าการเชื่อมต่อกับ Supabase PostgreSQL
$host = 'db.tkrxalwhftfcmcqtvkmw.supabase.co'; // แก้ไขเป็นโฮสต์ของคุณจาก Supabase
$db = 'sxdb';
$user = 'postgres'; // ปกติจะใช้ postgres เป็น default
$pass = 'syst1346';
$port = '5432'; // ปกติใช้พอร์ต 5432 สำหรับ PostgreSQL

try {
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$db", $user, $pass);
    // ตั้งค่า PDO ให้แสดง error และใช้ prepared statements
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("ไม่สามารถเชื่อมต่อกับฐานข้อมูลได้: " . $e->getMessage());
}
?>
