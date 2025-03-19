<?php
session_start();
include 'db_connect.php';

// ตรวจสอบว่ามีการส่งข้อมูลจากฟอร์มหรือไม่
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // ดึงข้อมูลจากฟอร์ม
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $telegram_id = $_POST['telegram_id'];
    $role_id = $_POST['role_id'];
    $position_id = $_POST['position_id'];
    $subject_group_id = $_POST['subject_group_id'];

    // ตรวจสอบข้อมูลซ้ำในฐานข้อมูล
    $sql_check = "SELECT * FROM personnel WHERE 
                    (username = ? OR email = ? OR telegram_id = ? OR phone = ?)";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("ssss", $username, $email, $telegram_id, $phone);
    $stmt_check->execute();
    $result = $stmt_check->get_result();

    if ($result->num_rows > 0) {
        // ถ้าพบข้อมูลซ้ำ
        $_SESSION['message'] = "<div class='alert alert-danger'>ข้อมูลซ้ำ! โปรดตรวจสอบข้อมูลของคุณ</div>";
    } else {
        // ถ้าไม่พบข้อมูลซ้ำ ให้ทำการเพิ่มผู้ใช้งาน
        $sql = "INSERT INTO personnel (first_name, last_name, username, password, phone, email, telegram_id, role_id, position_id, subject_group_id) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssssii", $first_name, $last_name, $username, $password, $phone, $email, $telegram_id, $role_id, $position_id, $subject_group_id);

        if ($stmt->execute()) {
            $_SESSION['message'] = "<div class='alert alert-success'>เพิ่มผู้ใช้งานใหม่สำเร็จ!</div>";
        } else {
            $_SESSION['message'] = "<div class='alert alert-danger'>เกิดข้อผิดพลาด: " . $stmt->error . "</div>";
        }
    }

    // หลังจากเพิ่มผู้ใช้สำเร็จหรือไม่สำเร็จ จะทำการรีไดเร็กต์กลับไปที่หน้า member.php
    header("Location: members.php");
    exit; // จำเป็นต้องมีเพื่อหยุดการทำงานของ PHP script หลังจากรีไดเร็กต์
}
?>
