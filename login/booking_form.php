<?php
session_start();

// ตรวจสอบว่าผู้ใช้ล็อกอินหรือไม่
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // ถ้ายังไม่ได้ล็อกอิน ให้เปลี่ยนเส้นทางไปที่หน้า index.php
    header('Location: index.php');
    exit;
}

// หากล็อกอินแล้ว จะแสดงข้อมูลห้องประชุมได้
include 'db_connect.php';
include 'auth_check.php'; // เรียกใช้งานการตรวจสอบการเข้าสู่ระบบและสถานะผู้ใช้

// ดึงข้อมูลห้องประชุมจากฐานข้อมูล
$sql = "SELECT hall_id, hall_name, hall_detail, hall_size, capacity FROM hall"; 
$result = $conn->query($sql);


// ดึงข้อมูลชื่อผู้อนุมัติจากบัญชีที่ล็อกอินในปัจจุบัน
$approver_name = ""; // ตัวแปรเก็บชื่อผู้อนุมัติ
if (isset($_SESSION['personnel_id'])) {
    $personnel_id = $_SESSION['personnel_id']; // ดึง Personnel_ID จาก session
    $approver_query = "SELECT First_Name FROM personnel WHERE Personnel_ID = ?";
    $stmt = $conn->prepare($approver_query);
    $stmt->bind_param('i', $personnel_id);
    $stmt->execute();
    $stmt->bind_result($approver_name);
    $stmt->fetch();
    $stmt->close();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // รับค่าจากฟอร์ม
    $room_id = $_POST['hall_id'] ?? null;
    $attendees = $_POST['attendees'] ?? null;
    $date_start = $_POST['date_start'] ?? null;
    $date_end = $_POST['date_end'] ?? null;
    $start_time = $_POST['start_time'] ?? null;
    $end_time = $_POST['end_time'] ?? null;
    $booking_detail = $_POST['description'] ?? '';
    $status_id = 1; // รอดำเนินการ
    $approver_id = $_SESSION['personnel_id'] ?? null;

    // ตรวจสอบข้อมูลที่จำเป็น
    if (!$room_id || !$attendees || !$date_start || !$date_end || !$start_time || !$end_time || !$approver_id) {
        echo "<p style='color: red;'>กรุณากรอกข้อมูลให้ครบถ้วน</p>";
        exit;
    }

    // ตรวจสอบความจุห้องประชุม
    $sql_check_capacity = "SELECT capacity FROM hall WHERE hall_id = ?";
    $stmt = $conn->prepare($sql_check_capacity);
    $stmt->bind_param('i', $room_id);
    $stmt->execute();
    $stmt->bind_result($capacity);
    $stmt->fetch();
    $stmt->close();

    if ($attendees > $capacity) {
        echo "<p style='color: red;'>จำนวนผู้เข้าประชุมเกินขีดจำกัด (รองรับได้ $capacity คน)</p>";
        exit;
    }

    // เพิ่มข้อมูลการจอง (Booking)
    $sql_insert_booking = "INSERT INTO booking 
        (Day_Start, Day_End, Time_Start, Time_End, Personnel_ID, Hall_ID, Attendee_Count, Booking_Detail, Status_ID, Approver_ID) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt_insert = $conn->prepare($sql_insert_booking);
    $stmt_insert->bind_param('sssssiisis', 
        $date_start, $date_end, $start_time, $end_time, $approver_id, 
        $room_id, $attendees, $booking_detail, $status_id, $approver_id);

    if ($stmt_insert->execute()) {
        echo "<p style='color: green;'>การจองห้องประชุมสำเร็จ!</p>";
    } else {
        echo "<p style='color: red;'>ข้อผิดพลาดในการบันทึก: " . $stmt_insert->error . "</p>";
    }

    $stmt_insert->close();
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เพิ่มการจอง</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <style>
    @media (max-width: 991px) {
        #navbarNav {
            padding-top: 10px;
            padding-bottom: 10px;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

    }

    html,
    body {
        margin: 0;
        padding: 0;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    body {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    main {
        flex-grow: 1;
        /* ให้ส่วนเนื้อหาขยายเต็มพื้นที่ที่เหลือ */
        overflow: auto;
        padding-bottom: 20px;
    }

    /* Navbar brand logo */
    .navbar-brand .responsive-img {
        max-width: 100%;
        height: auto;
    }

    /* Adjusting padding for Navbar */
    .navbar {
        padding-top: 0.5rem;
        padding-bottom: 0.5rem;
    }

    /* Adjust padding for nav-link */
    .nav-link {
        padding-top: 0.5rem;
        padding-bottom: 0.5rem;
    }

    /* Dropdown menu styling */
    .nav-item .dropdown-menu {
        background-color: #343a40;
        color: #ffffff;
    }

    .dropdown-menu .dropdown-item {
        color: #ffffff;
    }

    .dropdown-menu .dropdown-item:hover {
        background-color: #495057;
        color: #ffffff;
    }

    .full-height {
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        align-items: center;
        height: calc(100vh - 56px);
        position: relative;
        padding-top: 60px;
        padding-bottom: 20px;
        flex-grow: 1;
        overflow: auto;
        padding-bottom: 20px;
    }

    .text-center {
        color: white;
        padding: 20px;
        border-radius: 5px 5px 0 0;
        height: 70px;
        display: flex;
        align-items: center;
        border: 1px solid #e0e0e0;
        max-width: 1200px;
        width: 100%;

    }

    .container-custom {
        background-color: #fff;
        padding: 20px;
        border-radius: 5px;
        box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        max-width: 1200px;
        width: 100%;
        margin-bottom: 20px;
    }

    .footer {
        width: 100%;
        background-color: #f8f9fa;
        padding: 20px;
        font-size: 16px;
        color: #6c757d;
        margin-top: auto;
        position: relative;
    }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark p-3">
        <div class="container-fluid">
            <a href="main.php" class="navbar-brand d-flex align-items-center">
                <img class="responsive-img" src="LOGO.png" alt="system booking" width="45" height="45">
                <span class="ms-3">ระบบจองห้องประชุม</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a href="main.php"
                            class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'main.php') ? 'active' : ''; ?>">หน้าหลัก</a>
                    </li>

                    <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>

                    <!-- Dropdown เมนูสำหรับ "รายการจองของฉัน" -->
                    <li class="nav-item dropdown">
                        <!-- เช็คไฟล์ PHP สำหรับ active -->
                        <a class="nav-link dropdown-toggle <?php echo (in_array(basename($_SERVER['PHP_SELF']), ['upcoming_bookings.php', 'disactive_bookings.php', 'active_bookings.php']) ? 'active' : ''); ?>"
                            href="#" id="myBookingsDropdown" role="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            รายการจองของฉัน
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="myBookingsDropdown">
                            <li><a class="dropdown-item <?php echo (basename($_SERVER['PHP_SELF']) == 'upcoming_bookings.php') ? 'active' : ''; ?>"
                                    href="upcoming_bookings.php">รอตรวจสอบ</a></li>
                            <li><a class="dropdown-item <?php echo (basename($_SERVER['PHP_SELF']) == 'active_bookings.php') ? 'active' : ''; ?>"
                                    href="active_bookings.php">อนุมัติ</a></li>
                            <li><a class="dropdown-item <?php echo (basename($_SERVER['PHP_SELF']) == 'disactive_bookings.php') ? 'active' : ''; ?>"
                                    href="disactive_bookings.php">ไม่อนุมัติ</a></li>
                        </ul>
                    </li>

                    <li class="nav-item">
                        <a href="booking.php"
                            class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'booking.php') ? 'active' : ''; ?>">จองห้อง</a>
                    </li>

                    <?php if ($_SESSION['role_id'] == 2): ?>
                    <li class="nav-item">
                        <a href="members.php"
                            class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'members.php') ? 'active' : ''; ?>">สมาชิก</a>
                    </li>
                    <li class="nav-item">
                        <a href="reports.php"
                            class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'reports.php') ? 'active' : ''; ?>">รายงาน</a>
                    </li>
                    <li class="nav-item">
                        <a href="settings.php"
                            class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'settings.php') ? 'active' : ''; ?>">ตั้งค่า</a>
                    </li>
                    <?php elseif ($_SESSION['role_id'] == 3 || $_SESSION['role_id'] == 4): ?>
                    <li class="nav-item">
                        <a href="reports.php"
                            class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'reports.php') ? 'active' : ''; ?>">รายงาน</a>
                    </li>
                    <?php endif; ?>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            สวัสดี, <?php echo $_SESSION['username']; ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="edit_profile.php">แก้ไขข้อมูล</a></li>
                            <li><a class="dropdown-item" href="logout.php">ออกจากระบบ</a></li>
                        </ul>
                    </li>

                    <?php else: ?>
                    <li class="nav-item">
                        <a href="booking.php" class="nav-link active">จองห้อง</a>
                    </li>
                    <li class="nav-item">
                        <a href="index.php" class="nav-link">เข้าสู่ระบบ</a>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="full-height">
        <div class="text-center bg-dark">
            <div style="font-size: 20px">เพิ่มการจองห้อง</div>
        </div>
        <div class="container-custom">
            <form action="booking_form.php" method="POST">
                <!-- เลือกห้องประชุม -->
                <div class="mb-3">
                    <label for="hall_id" class="form-label">ชื่อห้องประชุม</label>
                    <select name="hall_id" id="hall_id" class="form-control" required>
                        <option value="">-- เลือกห้องประชุม --</option>
                        <?php 
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<option value='" . $row['hall_id'] . "'>";
                                    echo $row['hall_name'] . " (" . $row['hall_size'] . ") - รองรับ " . $row['capacity'] . " คน";
                                    echo "</option>";
                                }
                            } else {
                                echo "<option value=''>ไม่มีห้องประชุม</option>";
                            }
                        ?>
                    </select>
                </div>

                <!-- จำนวนผู้เข้าประชุม -->
                <div class="mb-3">
                    <label for="attendees" class="form-lajbel">จำนวนผู้เข้าประชุม</label>
                    <input type="number" id="attendees" name="attendees" class="form-control" required min="1">
                </div>

                <!-- วันที่เริ่มต้น -->
                <div class="mb-3">
                    <label for="date_start" class="form-label">วันที่เริ่มต้น</label>
                    <input type="date" id="date_start" name="date_start" class="form-control" readonly>
                </div>

                <!-- เวลาเริ่มต้น -->
                <div class="mb-3">
                    <label for="start_time" class="form-label">เวลาเริ่มต้น</label>
                    <input type="time" id="start_time" name="start_time" class="form-control" readonly>
                </div>

                <!-- วันที่สิ้นสุด -->
                <div class="mb-3">
                    <label for="date_end" class="form-label">วันที่สิ้นสุด</label>
                    <input type="date" id="date_end" name="date_end" class="form-control" required>
                </div>

                <!-- เวลาสิ้นสุด -->
                <div class="mb-3">
                    <label for="end_time" class="form-label">เวลาสิ้นสุด</label>
                    <input type="time" id="end_time" name="end_time" class="form-control" required>
                </div>

                <!-- คำอธิบายการจอง -->
                <div class="mb-3">
                    <label for="description" class="form-label">คำอธิบาย</label>
                    <textarea id="description" name="description" class="form-control" rows="3" required></textarea>
                </div>

                <button type="submit" class="btn btn-primary">บันทึกการจอง</button>
            </form>

        </div>

    </div>


    <!-- Footer -->
    <div class="footer">
        Copyright 2025 © - BangWa Developer
    </div>

    <script src="js/bootstrap.bundle.min.js"></script>

    <script>
    // ดึงวันที่ปัจจุบัน
    let today = new Date();
    let now = new Date();

    // แปลงเป็นรูปแบบ YYYY-MM-DD
    let yyyy = today.getFullYear();
    let mm = String(today.getMonth() + 1).padStart(2, '0'); // เดือนต้องเพิ่ม 1 เพราะเดือนเริ่มจาก 0
    let dd = String(today.getDate()).padStart(2, '0'); // วันต้องเติม 0 ข้างหน้า
    let hours = String(now.getHours()).padStart(2, '0'); // ชั่วโมง
    let minutes = String(now.getMinutes()).padStart(2, '0'); // นาที

    let currentDate = `${yyyy}-${mm}-${dd}`;
    let currentTime = `${hours}:${minutes}`;

    // กำหนดค่าให้กับ input
    document.getElementById("date_start").value = currentDate;
    document.getElementById("start_time").value = currentTime;
    </script>



</body>

</html>