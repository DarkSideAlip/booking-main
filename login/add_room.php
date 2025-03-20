<?php
session_start();
include 'db_connect.php'; // เชื่อมต่อกับฐานข้อมูล

// ดึงข้อมูลจากตาราง hall
$sql = "SELECT Hall_ID, Hall_Name, Hall_Detail, Hall_Size, Capacity, Status_Hall FROM hall";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // ดึงข้อมูลจากฟอร์ม
    $hall_name = $_POST['hall_name'];
    $hall_detail = $_POST['hall_detail'];
    $hall_size = $_POST['hall_size'];
    $capacity = $_POST['capacity'];
    $status_id = $_POST['status_hall']; // ดึงข้อมูลสถานะห้อง

    // ตรวจสอบห้องที่มีชื่อซ้ำ
    $sql_check = "SELECT * FROM hall WHERE Hall_Name = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("s", $hall_name);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        $_SESSION['message'] = "<div class='alert alert-danger'>ห้องนี้มีอยู่แล้วในระบบ!</div>";
    } else {
        // เพิ่มห้องใหม่
        $sql = "INSERT INTO hall (Hall_Name, Hall_Detail, Hall_Size, Capacity, Status_Hall) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $hall_name, $hall_detail, $hall_size, $capacity, $status_hall);

        if ($stmt->execute()) {
            $_SESSION['message'] = "<div class='alert alert-success'>ห้องถูกเพิ่มสำเร็จ!</div>";
        } else {
            $_SESSION['message'] = "<div class='alert alert-danger'>เกิดข้อผิดพลาด: " . $stmt->error . "</div>";
        }
    }

    // หลังจากเพิ่มห้องสำเร็จหรือไม่สำเร็จ จะทำการรีไดเร็กต์กลับไปที่หน้า member.php
    header("Location: booking.php");
    exit; // จำเป็นต้องมีเพื่อหยุดการทำงานของ PHP script หลังจากรีไดเร็กต์
}
?>
