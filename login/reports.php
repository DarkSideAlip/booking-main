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

$sql = "SELECT 
            b.Booking_ID,
            CONCAT(p.First_Name, ' ', p.Last_Name) AS Booker_Name,
            b.Date_Start,
            b.Time_Start,
            b.Time_End,
            h.Hall_Name,
            b.Attendee_Count,
            b.Topic_Name,
            b.Booking_Detail,
            s.Status_ID,
            CONCAT(a.First_Name, ' ', a.Last_Name) AS Approver_Name
        FROM 
            booking b
        LEFT JOIN personnel p ON b.Personnel_ID = p.Personnel_ID
        LEFT JOIN hall h ON b.Hall_ID = h.Hall_ID
        LEFT JOIN booking_status s ON b.Status_ID = s.Status_ID
        LEFT JOIN personnel a ON b.Approver_ID = a.Personnel_ID
        ORDER BY b.Booking_ID DESC";

$result = $conn->query($sql);

if (!$result) {
    die("Error retrieving data: " . $conn->error);
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายงาน</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

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

    /* จัดข้อความให้อยู่กึ่งกลางทั้งแนวตั้งและแนวนอน */
    .table td,
    .table th {
        text-align: center;
        /* จัดกึ่งกลางแนวนอน */
        vertical-align: middle;
        /* จัดกึ่งกลางแนวตั้ง */
    }

    main {
        flex-grow: 1;
        /* ให้ส่วนเนื้อหาขยายเต็มพื้นที่ที่เหลือ */
        overflow: auto;
        padding-bottom: 20px;
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

    .text-warning {
        color: #ffc107;
        /* สีเหลือง */

    }

    .text-success {
        color: #28a745;
        /* สีเขียว */

    }

    .text-danger {
        color: #dc3545;
        /* สีแดง */

    }


    /* สีพื้นหลังสำหรับสถานะ "รอตรวจสอบ" */
    .status-pending {
        background-color: #ffeeba;
        /* สีเหลืองอ่อน */
        color: #856404;
        /* สีข้อความเหลืองเข้ม */
        font-weight: bold;
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

    table {
        border-collapse: separate;
        border-spacing: 0;
        border: 1px solid #e0e0e0;
        border-radius: 5px;
        overflow: hidden;
    }

    th,
    td {
        border: 1px solid #e0e0e0;
        padding: 20px;
    }

    th {
        background-color: #f8f9fa;
    }

    td {
        background-color: #ffffff;
    }

    td img {
        border-radius: 5px;
        /* กำหนดความโค้งให้กับรูปภาพ */
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
            <div style="font-size: 20px">รายงาน</div>
        </div>
        <div class="container-custom">
            <table id="member-table" class="table table-striped" style="width:100%">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>หัวข้อ</th>
                        <th>ชื่อห้อง</th>
                        <th>ชื่อผู้จอง</th>
                        <th>วันที่และเวลา</th>
                        <th>จำนวนผู้เข้าร่วม</th>
                        <th>สถานะ</th>
                        <th>ผู้อนุมัติ</th>
                        <th>เหตุผล</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                // SQL สำหรับดึงข้อมูลจากฐานข้อมูล
                $sql = "SELECT 
                            b.Booking_ID,
                            b.Topic_Name,
                            h.Hall_Name,
                            CONCAT(p.First_Name, ' ', p.Last_Name) AS Booker_Name,
                            b.Date_Start,
                            b.Time_Start,
                            b.Time_End,
                            b.Attendee_Count,
                            s.Status_ID,
                            CONCAT(a.First_Name, ' ', a.Last_Name) AS Approver_Name,
                            b.Booking_Detail
                        FROM 
                            booking b
                        LEFT JOIN personnel p ON b.Personnel_ID = p.Personnel_ID
                        LEFT JOIN hall h ON b.Hall_ID = h.Hall_ID
                        LEFT JOIN booking_status s ON b.Status_ID = s.Status_ID
                        LEFT JOIN personnel a ON b.Approver_ID = a.Personnel_ID
                        ORDER BY b.Booking_ID DESC";

                $result = $conn->query($sql);

                if ($result && $result->num_rows > 0):
                    while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['Booking_ID']; ?></td>
                        <td><?php echo htmlspecialchars($row['Topic_Name']); ?></td>
                        <td><?php echo htmlspecialchars($row['Hall_Name']); ?></td>
                        <td><?php echo htmlspecialchars($row['Booker_Name']); ?></td>
                        <td>
                            <?php echo htmlspecialchars($row['Date_Start']) . ' ' . 
                htmlspecialchars($row['Time_Start']) . ' - ' . 
                htmlspecialchars($row['Time_End']); ?>
                        </td>
                        <td><?php echo htmlspecialchars($row['Attendee_Count']); ?></td>
                        <td>
                            <?php if ((int)$row['Status_ID'] === 1): ?>
                            <span class="text-warning">รอตรวจสอบ</span>
                            <?php elseif ((int)$row['Status_ID'] === 2): ?>
                            <span class="text-success">อนุมัติ</span>
                            <?php elseif ((int)$row['Status_ID'] === 3): ?>
                            <span class="text-danger">ไม่อนุมัติ</span>
                            <?php else: ?>
                            <span class="text-muted">ไม่ทราบสถานะ</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($row['Approver_Name']); ?></td>
                        <td>
                            <!-- ปุ่มรายละเอียด -->
                            <button type="button" class="btn btn-outline-dark btn-sm" data-bs-toggle="modal"
                                data-bs-target="#detailModal<?php echo $row['Booking_ID']; ?>">
                                รายละเอียด
                            </button>

                            <!-- Modal รายละเอียด -->
                            <div class="modal fade" id="detailModal<?php echo $row['Booking_ID']; ?>" tabindex="-1"
                                aria-labelledby="detailModalLabel<?php echo $row['Booking_ID']; ?>" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title"
                                                id="detailModalLabel<?php echo $row['Booking_ID']; ?>">รายละเอียด</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <?php echo htmlspecialchars($row['Booking_Detail']); ?>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">ปิด</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- แสดงปุ่มอนุมัติ/ไม่อนุมัติเฉพาะสถานะรอตรวจสอบ -->
                            <?php if ((int)$row['Status_ID'] === 1): ?>
                            <!-- ปุ่มอนุมัติ -->
                            <button type="button" class="btn btn-outline-success btn-sm ms-2" data-bs-toggle="modal"
                                data-bs-target="#approveRejectModal<?php echo $row['Booking_ID']; ?>">
                                <i class="fas fa-check-circle"></i>
                            </button>

                            <!-- Modal ติ๊กถูก -->
                            <div class="modal fade" id="approveRejectModal<?php echo $row['Booking_ID']; ?>"
                                tabindex="-1" aria-labelledby="approveRejectModalLabel<?php echo $row['Booking_ID']; ?>"
                                aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title"
                                                id="approveRejectModalLabel<?php echo $row['Booking_ID']; ?>">
                                                การจัดการการจอง
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>คุณต้องการดำเนินการอย่างไรกับการจองนี้?</p>
                                        </div>
                                        <div class="modal-footer">
                                            <!-- ปุ่มอนุมัติ -->
                                            <form method="POST" action="approve.php" style="display:inline;">
                                                <input type="hidden" name="booking_id"
                                                    value="<?php echo $row['Booking_ID']; ?>">
                                                <button type="submit" class="btn btn-success">อนุมัติ</button>
                                            </form>

                                            <!-- ปุ่มไม่อนุมัติ -->
                                            <form method="POST" action="reject.php" style="display:inline;">
                                                <input type="hidden" name="booking_id"
                                                    value="<?php echo $row['Booking_ID']; ?>">
                                                <button type="submit" class="btn btn-danger">ไม่อนุมัติ</button>
                                            </form>

                                            <!-- ปุ่มปิด -->
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">ยกเลิก</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                        </td>
                    </tr>

                    <?php endwhile; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="9" class="text-center">ไม่มีข้อมูลการจอง</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>



    </div>

    <!-- Footer -->
    <div class="footer">
        Copyright 2025 © - BangWa Developer
    </div>

    <!-- JavaScript -->
    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/2.1.7/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.1.7/js/dataTables.bootstrap5.js"></script>
    <script>
    $(document).ready(function() {
        $('#member-table').dataTable();
    });


    // รีเฟรช DataTables หลังลบข้อมูล
    $('#member-table').DataTable().ajax.reload(null, false);
    </script>



</body>

</html>