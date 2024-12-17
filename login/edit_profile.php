<?php
session_start();

// ตรวจสอบว่าผู้ใช้ล็อกอินแล้วหรือไม่
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: index.php');
    exit;
}

include 'db_connect.php'; // เชื่อมต่อกับฐานข้อมูล
include 'auth_check.php'; // เรียกใช้งานการตรวจสอบการเข้าสู่ระบบและสถานะผู้ใช้

// ดึงข้อมูลของผู้ใช้ที่ล็อกอินจากตาราง personnel
$personnel_id = $_SESSION['personnel_id'];
$sql = "SELECT First_Name, Last_Name, Email, Phone, ID_Line, Position_ID, Subject_Group_ID, Role_ID FROM personnel WHERE Personnel_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $personnel_id);
$stmt->execute();
$stmt->bind_result($first_name, $last_name, $email, $phone, $id_line, $position_id, $subject_group_id, $role_id);
$stmt->fetch();
$stmt->close();

// ดึงข้อมูลตำแหน่ง
$positions = [];
$position_query = "SELECT Position_ID, Position_Name FROM position";
$result = $conn->query($position_query);
while ($row = $result->fetch_assoc()) {
    $positions[] = $row;
}

// ดึงข้อมูลสถานะ
$roles = [];
$role_query = "SELECT Role_ID, Role_Name FROM role";
$result = $conn->query($role_query);
while ($row = $result->fetch_assoc()) {
    $roles[] = $row;
}

// ดึงข้อมูลกลุ่มสาระการเรียนรู้
$subject_groups = [];
$subject_group_query = "SELECT Subject_Group_ID, Subject_Group_Name FROM subject_group";
$result = $conn->query($subject_group_query);
while ($row = $result->fetch_assoc()) {
    $subject_groups[] = $row;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ดึงข้อมูลจากฟอร์ม
    $new_first_name = $_POST['first_name'];
    $new_last_name = $_POST['last_name'];
    $new_email = $_POST['email'];
    $new_phone = $_POST['phone'];
    $new_id_line = $_POST['id_line'];

    $new_position_id = $position_id;
    $new_subject_group_id = $subject_group_id;
    $new_role_id = $role_id;

    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // ตรวจสอบว่ามีข้อมูลซ้ำหรือไม่
    $duplicate_check_sql = "SELECT Personnel_ID FROM personnel WHERE (Email = ? OR ID_Line = ?) AND Personnel_ID != ?";
    $stmt = $conn->prepare($duplicate_check_sql);
    $stmt->bind_param('ssi', $new_email, $new_id_line, $personnel_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // ข้อมูลซ้ำ
        echo "<script>
                alert('อีเมลหรือ Line ID นี้มีผู้ใช้งานแล้ว!');
                window.location.href='edit_profile.php';
              </script>";
        exit;
    } elseif (empty($new_first_name) || empty($new_last_name) || empty($new_email)) {
        echo "<script>
                alert('กรุณากรอกข้อมูลให้ครบถ้วนเพื่ออัปเดตข้อมูล!');
                window.location.href='edit_profile.php';
              </script>";
        exit;
    } elseif (!empty($new_password) && $new_password !== $confirm_password) {
        echo "<script>
                alert('รหัสผ่านไม่ตรงกัน!');
                window.location.href='edit_profile.php';
              </script>";
        exit;
    } else {
        // ตรวจสอบว่ามีการเปลี่ยนแปลงข้อมูลหรือไม่
        if (
            $new_first_name == $first_name &&
            $new_last_name == $last_name &&
            $new_email == $email &&
            $new_phone == $phone &&
            $new_id_line == $id_line &&
            empty($new_password) // ถ้าไม่มีการเปลี่ยนรหัสผ่าน
        ) {
            // ถ้าไม่มีการเปลี่ยนแปลงข้อมูล
            echo "<script>
                    alert('ไม่มีการเปลี่ยนแปลงข้อมูล!');
                    window.location.href='edit_profile.php';
                  </script>";
            exit;
        }

        // ถ้ามีการเปลี่ยนแปลงข้อมูล ทำการอัปเดต
        $sql = "UPDATE personnel SET First_Name = ?, Last_Name = ?, Email = ?, Phone = ?, ID_Line = ?, Position_ID = ?, Subject_Group_ID = ?, Role_ID = ?";

        if (!empty($new_password)) {
            // เข้ารหัสรหัสผ่านใหม่ด้วย password_hash() ก่อนบันทึกลงฐานข้อมูล
            $sql .= ", Password = ?";
            $stmt = $conn->prepare($sql);
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT); // เข้ารหัสรหัสผ่านใหม่
            $stmt->bind_param('sssssssss', $new_first_name, $new_last_name, $new_email, $new_phone, $new_id_line, $new_position_id, $new_subject_group_id, $new_role_id, $hashed_password);
        } else {
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('ssssssss', $new_first_name, $new_last_name, $new_email, $new_phone, $new_id_line, $new_position_id, $new_subject_group_id, $new_role_id);
        }

        if ($stmt->execute()) {
            // อัปเดต session หลังจากอัปเดตข้อมูลสำเร็จ
            $_SESSION['first_name'] = $new_first_name;
            $_SESSION['last_name'] = $new_last_name;
            $_SESSION['phone'] = $new_phone;
            $_SESSION['id_line'] = $new_id_line;

            // รีเฟรชหน้าเพื่อดึงข้อมูลใหม่
            echo "<script>
                    alert('ข้อมูลอัปเดตเรียบร้อยแล้ว!');
                    window.location.href='edit_profile.php';
                  </script>";
            exit;
        } else {
            echo "<script>
                    alert('เกิดข้อผิดพลาด: " . addslashes($stmt->error) . "');
                    window.location.href='edit_profile.php';
                  </script>";
            exit;
        }
    }
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไขข้อมูลผู้ใช้</title>
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

    .container-custom {
        background-color: #fff;
        padding: 20px;
        border-radius: 5px;
        box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        max-width: 1000px;
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
        max-width: 1000px;
        width: 100%;

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

    .footer {
        width: 100%;
        background-color: #f8f9fa;
        padding: 20px;
        font-size: 16px;
        color: #6c757d;
        margin-top: auto;
        position: relative;
    }
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
            <div style="font-size: 20px">แก้ไขข้อมูลผู้ใช้งาน</div>
        </div>
        <div class="container container-custom">
            <form action="edit_profile.php" method="POST">
                <div class="mb-3">
                    <label for="email" class="form-label">อีเมล</label>
                    <input type="email" id="email" class="form-control" name="email" value="<?php echo $email; ?>"
                        readonly>
                </div>
                <div class="mb-3">
                    <label for="new_password" class="form-label">รหัสผ่านใหม่</label>
                    <input type="password" id="new_password" class="form-control" name="new_password"
                        placeholder="รหัสผ่านต้องไม่น้อยกว่า 4 ตัวอักษร">
                </div>
                <div class="mb-3">
                    <label for="confirm_password" class="form-label">ยืนยันรหัสผ่าน</label>
                    <input type="password" id="confirm_password" class="form-control" name="confirm_password"
                        placeholder="กรอกรหัสผ่านอีกครั้ง">
                </div>
                <div class="mb-3">
                    <label for="first_name" class="form-label">ชื่อจริง</label>
                    <input type="text" id="first_name" class="form-control" name="first_name"
                        placeholder="แก้ไขชื่อของคุณ" value="<?php echo $first_name; ?>" required>
                </div>
                <div class="mb-3">
                    <label for="last_name" class="form-label">นามสกุล</label>
                    <input type="text" id="last_name" class="form-control" name="last_name"
                        placeholder="แก้ไขนามสกุลของคุณ" value="<?php echo $last_name; ?>" required>
                </div>
                <div class="mb-3">
                    <label for="position_id" class="form-label">ตำแหน่ง</label>
                    <select class="selectpicker form-select" data-live-search="true" id="position_id" name="position_id"
                        disabled>
                        <?php foreach ($positions as $position): ?>
                        <option value="<?php echo $position['Position_ID']; ?>"
                            <?php if ($position['Position_ID'] == $position_id) echo 'selected'; ?>>
                            <?php echo $position['Position_Name']; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="subject_group_id" class="form-label">กลุ่มสาระการเรียนรู้</label>
                    <select class="selectpicker form-select" data-live-search="true" id="subject_group_id"
                        name="subject_group_id" disabled>
                        <?php foreach ($subject_groups as $group): ?>
                        <option value="<?php echo $group['Subject_Group_ID']; ?>"
                            <?php if ($group['Subject_Group_ID'] == $subject_group_id) echo 'selected'; ?>>
                            <?php echo $group['Subject_Group_Name']; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="role_id" class="form-label">สถานะสมาชิก</label>
                    <select class="selectpicker form-select" data-live-search="true" id="role_id" name="role_id"
                        disabled>
                        <?php foreach ($roles as $role): ?>
                        <option value="<?php echo $role['Role_ID']; ?>"
                            <?php if ($role['Role_ID'] == $role_id) echo 'selected'; ?>>
                            <?php echo $role['Role_Name']; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="id_line" class="form-label">Line ID</label>
                    <input type="text" id="id_line" class="form-control" name="id_line" value="<?php echo $id_line; ?>"
                        required>
                </div>
                <div class="mb-3">
                    <label for="phone" class="form-label">เบอร์โทรศัพท์</label>
                    <input type="text" id="phone" class="form-control" name="phone" value="<?php echo $phone; ?>"
                        required>
                </div>

                <button class="btn btn-outline-dark">บันทึกข้อมูลผู้ใช้ใหม่</button>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        Copyright 2025 © - BangWa Developer
    </div>

    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

</body>

</html>