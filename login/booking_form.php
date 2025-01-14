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

// ดึงข้อมูลห้องประชุมทั้งหมดจากฐานข้อมูล
$sql = "SELECT Hall_ID, Hall_Name, Capacity FROM HALL";
$result = $conn->query($sql);

if (!$result) {
    die("Error in fetching halls: " . $conn->error);
}

// เก็บข้อมูลห้องประชุมในอาร์เรย์
$halls = [];
while ($row = $result->fetch_assoc()) {
    $halls[] = $row;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $hall_id = intval($_POST['hall_id']);
    $attendees = intval($_POST['attendees']);
    $date_start = $_POST['date_start'];
    $time_start = $_POST['time_start'];
    $time_end = $_POST['time_end'];
    $topic_name = $_POST['topic_name'];
    $booking_detail = $_POST['booking_detail'];
    $status_id = 1; // สถานะ "รอตรวจสอบ"
    $approver_id = $_SESSION['personnel_id'];
    

    // ตรวจสอบความจุของห้องประชุม
    $sql = "SELECT Capacity FROM HALL WHERE Hall_ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $hall_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $hall = $result->fetch_assoc();
        $capacity = $hall['Capacity'];

        if ($attendees > $capacity) {
            echo "<script>alert('จำนวนผู้เข้าประชุมเกินความจุสูงสุดของห้องประชุม (ความจุ: $capacity คน)'); window.history.back();</script>";
            exit;
        }
    } else {
        echo "<script>alert('ไม่พบข้อมูลห้องประชุมที่เลือก'); window.history.back();</script>";
        exit;
    }

    // ตรวจสอบเวลาการจอง
    if (strtotime($time_start) >= strtotime($time_end)) {
        echo "<script>alert('เวลาเริ่มต้นต้องน้อยกว่าเวลาสิ้นสุด'); window.history.back();</script>";
        exit;
    }

    // ตรวจสอบการจองซ้อน
    $sql = "SELECT * FROM booking 
            WHERE Hall_ID = ? 
              AND Date_Start = ? 
              AND ((Time_Start < ? AND Time_End > ?) 
                   OR (Time_Start < ? AND Time_End > ?) 
                   OR (Time_Start >= ? AND Time_End <= ?))";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssssss", $hall_id, $date_start, $time_end, $time_end, $time_start, $time_start, $time_start, $time_end);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<script>alert('ไม่สามารถจองห้องประชุมในช่วงเวลาที่เลือกได้'); window.history.back();</script>";
        exit;
    }

    // บันทึกข้อมูลการจอง
    $sql = "INSERT INTO booking (Personnel_ID, Date_Start, Time_Start, Time_End, Hall_ID, Attendee_Count, Booking_Detail, Status_ID, Approver_ID, Topic_Name) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssiisiis", $_SESSION['personnel_id'], $date_start, $time_start, $time_end, $hall_id, $attendees, $booking_detail, $status_id, $approver_id, $topic_name);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo "<script>alert('บันทึกการจองสำเร็จ!'); window.location.href='main.php';</script>";
    } else {
        echo "<script>alert('เกิดข้อผิดพลาดในการบันทึกข้อมูล'); window.history.back();</script>";
    }
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
                <div class="mb-3">
                    <label for="hall_id" class="form-label">เลือกห้องประชุม</label>
                    <select id="hall_id" name="hall_id" class="form-select" required onchange="updateCapacity()">
                        <option value="" data-capacity="0">-- กรุณาเลือกห้องประชุม --</option>
                        <?php foreach ($halls as $hall): ?>
                        <option value="<?php echo $hall['Hall_ID']; ?>"
                            data-capacity="<?php echo $hall['Capacity']; ?>">
                            <?php echo htmlspecialchars($hall['Hall_Name']) . " (ความจุ: " . $hall['Capacity'] . " คน)"; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- หัวข้อการจอง -->
                <div class="mb-3">
                    <label for="topic_name" class="form-label">หัวข้อการจอง</label>
                    <input type="text" id="topic_name" name="topic_name" class="form-control" required>
                </div>

                <!-- จำนวนผู้เข้าประชุม -->
                <div class="mb-3">
                    <label for="attendees" class="form-label">จำนวนผู้เข้าประชุม</label>
                    <input type="number" id="attendees" name="attendees" class="form-control" required min="1">
                    <small id="capacity-info" class="text-muted">ความจุสูงสุด: - คน</small>
                </div>

                <!-- วันที่เริ่มต้น -->
                <div class="mb-3">
                    <label for="date_start" class="form-label">วันที่จอง</label>
                    <input type="date" id="date_start" name="date_start" class="form-control" required>
                </div>

                <!-- เวลาเริ่มต้น -->
                <div class="mb-3">
                    <label for="time_start" class="form-label">เวลาเริ่มต้น</label>
                    <input type="time" id="time_start" name="time_start" class="form-control" required>
                </div>

                <!-- เวลาสิ้นสุด -->
                <div class="mb-3">
                    <label for="time_end" class="form-label">เวลาสิ้นสุด</label>
                    <input type="time" id="time_end" name="time_end" class="form-control" required>
                </div>

                <!-- คำอธิบายการจอง -->
                <div class="mb-3">
                    <label for="booking_detail" class="form-label">คำอธิบาย</label>
                    <textarea id="booking_detail" name="booking_detail" class="form-control" rows="3"
                        required></textarea>
                </div>

                <button type="submit" class="btn btn-dark">บันทึกการจอง</button>
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

    let currentDate = `${yyyy}-${mm}-${dd}`;
    document.getElementById("date_start").value = currentDate;

    function updateCapacity() {
        const hallSelect = document.getElementById("hall_id");
        const attendeesInput = document.getElementById("attendees");
        const capacityInfo = document.getElementById("capacity-info");

        // ดึงความจุจาก data-capacity
        const selectedOption = hallSelect.options[hallSelect.selectedIndex];
        const capacity = selectedOption.getAttribute("data-capacity");

        // อัปเดตข้อมูลความจุในฟอร์ม
        attendeesInput.max = capacity || 0; // กำหนดค่า max
        capacityInfo.textContent = `ความจุสูงสุด: ${capacity || "-"} คน`;
    }
    </script>



</body>

</html>