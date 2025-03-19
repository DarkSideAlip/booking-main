<?php
session_start();

// ตรวจสอบว่าผู้ใช้ล็อกอินแล้วหรือไม่
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: index.php');
    exit;
}

include 'db_connect.php';
include 'auth_check.php'; // เรียกใช้งานการตรวจสอบการเข้าสู่ระบบและสถานะผู้ใช้

// ดึงข้อมูลของผู้ใช้ที่ล็อกอินจากตาราง personnel
$personnel_id = $_SESSION['personnel_id'];
$sql = "SELECT First_Name, Last_Name, Email, Phone, Telegram_ID, Position_ID, Subject_Group_ID, Role_ID, Username, Password FROM personnel WHERE Personnel_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $personnel_id);
$stmt->execute();
$stmt->bind_result($first_name, $last_name, $email, $phone, $telegram_id, $position_id, $subject_group_id, $role_id, $username, $hashed_password);
$stmt->fetch();
$stmt->close();

// ตรวจสอบว่าเป็นการแก้ไขข้อมูลหรือไม่
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['personnel_id'])) {
    $personnel_id = $_POST['personnel_id'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $username = $_POST['username'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $telegram_id = $_POST['telegram_id'];
    $role_id = $_POST['role_id'];
    $position_id = $_POST['position_id'];
    $subject_group_id = $_POST['subject_group_id'];

    // ถ้าผู้ใช้กรอกรหัสผ่านใหม่
    $password = $_POST['password'];
    if (!empty($password)) {
        // ตรวจสอบรหัสผ่านเดิมก่อนการอัปเดต
        if (!password_verify($password, $hashed_password)) {
            $_SESSION['message'] = "<div class='alert alert-danger'>รหัสผ่านเดิมไม่ถูกต้อง!</div>";
            header('Location: members.php');
            exit;
        }
        // ถ้ารหัสผ่านใหม่ถูกกรอก, ทำการแฮชรหัสผ่านใหม่
        $password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
    } else {
        // หากไม่มีการกรอกรหัสผ่านใหม่, ใช้รหัสเดิม
        $password = $hashed_password;
    }

    // ตรวจสอบข้อมูลซ้ำในฐานข้อมูล (สำหรับการแก้ไขข้อมูล)
    $sql_check = "SELECT * FROM personnel WHERE 
                    (username = ? OR email = ? OR telegram_id = ? OR phone = ?) 
                    AND personnel_id != ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("ssssi", $username, $email, $telegram_id, $phone, $personnel_id);
    $stmt_check->execute();
    $result = $stmt_check->get_result();

    if ($result->num_rows > 0) {
        // ถ้าพบข้อมูลซ้ำ
        $_SESSION['message'] = "<div class='alert alert-danger'>ข้อมูลซ้ำ! โปรดตรวจสอบข้อมูลของคุณ</div>";
    } else {
        // ถ้าไม่พบข้อมูลซ้ำ ให้ทำการอัปเดตข้อมูล
        $sql = "UPDATE personnel SET first_name=?, last_name=?, username=?, password=?, phone=?, email=?, telegram_id=?, role_id=?, position_id=?, subject_group_id=? WHERE personnel_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssssiii", $first_name, $last_name, $username, $password, $phone, $email, $telegram_id, $role_id, $position_id, $subject_group_id, $personnel_id);

        if ($stmt->execute()) {
            $_SESSION['message'] = "<div class='alert alert-success'>ข้อมูลถูกอัปเดตสำเร็จ!</div>";
        } else {
            $_SESSION['message'] = "<div class='alert alert-danger'>เกิดข้อผิดพลาด: " . $stmt->error . "</div>";
        }
    }
    // รีไดเรคไปหน้ารายการสมาชิก (members.php)
    header('Location: members.php');
    exit;
}
?>
