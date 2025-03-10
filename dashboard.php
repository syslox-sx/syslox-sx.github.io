<?php
// เริ่ม session
session_start();

// เรียกใช้ฟังก์ชันต่างๆ
require_once 'includes/functions.php';

// ตรวจสอบการล็อกอิน
requireLogin();

// คาบเรียนทั้งหมด
$periods = ['คาบ-0', 'คาบ-1', 'คาบ-2', 'คาบ-3', 'คาบ-4', 'คาบ-5', 'คาบ-6', 'คาบ-7', 'คาบ-8', 'คาบ-9'];
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เลือกคาบเรียน - ระบบบันทึกการมาเรียน</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            padding-top: 20px;
        }
        .period-button {
            margin-bottom: 15px;
            height: 100px;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .header {
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header d-flex justify-content-between align-items-center">
            <h2>ระบบบันทึกการมาเรียน</h2>
            <div>
                <span class="me-3">ผู้ใช้: <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                <a href="logout.php" class="btn btn-danger">ออกจากระบบ</a>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h4>เลือกคาบเรียน</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php foreach ($periods as $period): ?>
                    <div class="col-md-4">
                        <a href="scan.php?period=<?php echo urlencode($period); ?>" class="btn btn-primary w-100 period-button">
                            <?php echo htmlspecialchars($period); ?>
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
