<?php
// เริ่ม session
session_start();

// เรียกใช้ฟังก์ชันต่างๆ
require_once 'includes/functions.php';
require_once 'config/database.php';

// ตรวจสอบการล็อกอิน
requireLogin();

// ตรวจสอบพารามิเตอร์คาบเรียน
if (!isset($_GET['period'])) {
    header("Location: dashboard.php");
    exit;
}

$period = $_GET['period'];
$message = '';
$messageType = '';
$studentInfo = null;

// ตรวจสอบการส่งฟอร์ม
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['student_id']) && !empty($_POST['student_id'])) {
        $student_id = $_POST['student_id'];
        
        // ค้นหาข้อมูลนักเรียน
        $studentInfo = getStudentInfo($pdo, $student_id);
        
        if (!$studentInfo) {
            $message = 'ไม่พบข้อมูลนักเรียนรหัส ' . htmlspecialchars($student_id);
            $messageType = 'danger';
        }
    } else if (isset($_POST['save']) && isset($_POST['confirm_student_id'])) {
        $student_id = $_POST['confirm_student_id'];
        
        // บันทึกการเข้าเรียน
        $result = recordAttendance($pdo, $student_id, $period);
        
        if ($result['success']) {
            $message = $result['message'];
            $messageType = 'success';
            $studentInfo = null; // ล้างข้อมูลนักเรียนหลังบันทึก
        } else {
            $message = $result['message'];
            $messageType = 'danger';
            $studentInfo = getStudentInfo($pdo, $student_id); // โหลดข้อมูลนักเรียนอีกครั้ง
        }
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สแกนบาร์โค้ด - ระบบบันทึกการมาเรียน</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            padding-top: 20px;
        }
        .scanner-container {
            position: relative;
            width: 100%;
            max-width: 500px;
            margin: 0 auto;
        }
        #scanner-canvas {
            width: 100%;
        }
        .student-info {
            margin-top: 20px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .nav-tabs {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>บันทึกการมาเรียน <?php echo htmlspecialchars($period); ?></h2>
            <div>
                <a href="dashboard.php" class="btn btn-secondary me-2">กลับไปหน้าเลือกคาบเรียน</a>
                <a href="logout.php" class="btn btn-danger">ออกจากระบบ</a>
            </div>
        </div>
        
        <?php if (!empty($message)): ?>
            <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                <?php echo $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <div class="card">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs" id="scannerTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="camera-tab" data-bs-toggle="tab" data-bs-target="#camera" type="button" role="tab" aria-controls="camera" aria-selected="true">สแกนบาร์โค้ด</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="manual-tab" data-bs-toggle="tab" data-bs-target="#manual" type="button" role="tab" aria-controls="manual" aria-selected="false">กรอกรหัสด้วยตนเอง</button>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="scannerTabsContent">
                    <!-- แท็บสแกนบาร์โค้ด -->
                    <div class="tab-pane fade show active" id="camera" role="tabpanel" aria-labelledby="camera-tab">
                        <div class="scanner-container">
                            <div id="scanner-placeholder">
                                <div class="alert alert-info">กำลังเริ่มกล้อง...</div>
                            </div>
                            <video id="scanner-video" style="display: none;"></video>
                            <canvas id="scanner-canvas" style="display: none;"></canvas>
                        </div>
                    </div>
                    
                    <!-- แท็บกรอกรหัสด้วยตนเอง -->
                    <div class="tab-pane fade" id="manual" role="tabpanel" aria-labelledby="manual-tab">
                        <form method="post" action="" class="row g-3">
                            <div class="col-md-8">
                                <input type="text" class="form-control" id="student_id" name="student_id" placeholder="กรอกรหัสนักเรียน" required>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary w-100">ค้นหา</button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- แสดงข้อมูลนักเรียนและปุ่มบันทึก -->
                <?php if ($studentInfo): ?>
                <div class="student-info mt-4">
                    <h4>ข้อมูลนักเรียน</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>รหัสประจำตัว:</strong> <?php echo htmlspecialchars($studentInfo['student_id']); ?></p>
                            <p><strong>ชื่อ-นามสกุล:</strong> <?php echo htmlspecialchars($studentInfo['name']); ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>ชั้น:</strong> <?php echo htmlspecialchars($studentInfo['class']); ?></p>
                            <p><strong>ห้อง:</strong> <?php echo htmlspecialchars($studentInfo['room']); ?></p>
                        </div>
                    </div>
                    
                    <form method="post" action="">
                        <input type="hidden" name="confirm_student_id" value="<?php echo htmlspecialchars($studentInfo['student_id']); ?>">
                        <button type="submit" name="save" class="btn btn-success btn-lg w-100 mt-3">บันทึกข้อมูล</button>
                    </form>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/@zxing/library@latest"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const cameraTab = document.getElementById('camera-tab');
            const scannerVideo = document.getElementById('scanner-video');
            const scannerCanvas = document.getElementById('scanner-canvas');
            const scannerPlaceholder = document.getElementById('scanner-placeholder');
            let codeReader = null;
            
            // ฟังก์ชันเริ่มกล้อง
            function startCamera() {
                scannerVideo.style.display = 'block';
                scannerCanvas.style.display = 'block';
                scannerPlaceholder.style.display = 'none';
                
                codeReader = new ZXing.BrowserMultiFormatReader();
                codeReader.decodeFromVideoDevice(null, 'scanner-video', (result, err) => {
                    if (result) {
                        console.log('Result:', result.getText());
                        // ส่งรหัสนักเรียนไปยังเซิร์ฟเวอร์
                        submitStudentId(result.getText());
                        // หยุดกล้องชั่วคราว
                        if (codeReader) {
                            codeReader.reset();
                            setTimeout(() => {
                                startCamera();
                            }, 3000);
                        }
                    }
                    if (err && !(err instanceof ZXing.NotFoundException)) {
                        console.error(err);
                    }
                }).catch(err => {
                    console.error(err);
                    scannerPlaceholder.innerHTML = `
                        <div class="alert alert-danger">
                            ไม่สามารถเข้าถึงกล้องได้ กรุณาตรวจสอบการอนุญาตการใช้งานกล้อง
                        </div>
                    `;
                    scannerVideo.style.display = 'none';
                    scannerCanvas.style.display = 'none';
                    scannerPlaceholder.style.display = 'block';
                });
            }
            
            // ฟังก์ชันส่งรหัสนักเรียน
            function submitStudentId(studentId) {
                // สร้าง form สำหรับส่งข้อมูล
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = window.location.href;
                
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'student_id';
                input.value = studentId;
                
                form.appendChild(input);
                document.body.appendChild(form);
                form.submit();
            }
            
            // เริ่มกล้องเมื่อคลิกที่แท็บกล้อง
            cameraTab.addEventListener('click', function() {
                if (!codeReader) {
                    startCamera();
                }
            });
            
            // เริ่มกล้องโดยอัตโนมัติเมื่อโหลดหน้า
            startCamera();
            
            // ทำความสะอาดเมื่อออกจากหน้า
            window.addEventListener('beforeunload', function() {
                if (codeReader) {
                    codeReader.reset();
                    codeReader = null;
                }
            });
        });
    </script>
</body>
</html>
