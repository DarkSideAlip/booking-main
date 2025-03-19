<?php
session_start();
include 'db_connect.php'; // เชื่อมต่อกับฐานข้อมูล

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['hall_id'])) {
    $hall_id = $_POST['hall_id'];
    $hall_name = $_POST['hall_name'];
    $hall_detail = $_POST['hall_detail'];
    $hall_size = $_POST['hall_size'];
    $capacity = $_POST['capacity'];
    $status_hall = $_POST['status_hall'];

    // ดึงข้อมูลเดิมจากฐานข้อมูลเพื่อตรวจสอบการเปลี่ยนแปลง
    $sql_check = "SELECT Hall_Name, Hall_Detail, Hall_Size, Capacity, Status_Hall FROM hall WHERE Hall_ID = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("i", $hall_id);
    $stmt_check->execute();
    $stmt_check->bind_result($old_hall_name, $old_hall_detail, $old_hall_size, $old_capacity, $old_status_hall);
    $stmt_check->fetch();
    $stmt_check->close();

    // ตรวจสอบว่าข้อมูลที่กรอกใหม่มีการเปลี่ยนแปลงหรือไม่
    if ($hall_name == $old_hall_name && $hall_detail == $old_hall_detail && $hall_size == $old_hall_size && $capacity == $old_capacity && $status_hall == $old_status_hall) {
        $_SESSION['message'] = "<div class='alert alert-info'>ไม่มีการเปลี่ยนแปลงข้อมูล</div>";
        header('Location: booking.php');
        exit;
    }

    // ตรวจสอบว่ามีห้องที่ชื่อเดียวกันอยู่แล้วหรือไม่ (เพียงกรณีที่เปลี่ยนชื่อห้อง)
    $sql_check_name = "SELECT * FROM hall WHERE Hall_Name = ? AND Hall_ID != ?";
    $stmt_check_name = $conn->prepare($sql_check_name);
    $stmt_check_name->bind_param("si", $hall_name, $hall_id);
    $stmt_check_name->execute();
    $result_check_name = $stmt_check_name->get_result();

    if ($result_check_name->num_rows > 0) {
        $_SESSION['message'] = "<div class='alert alert-danger'>ห้องนี้มีชื่อซ้ำแล้วในระบบ!</div>";
        header('Location: booking.php');
        exit;
    }

    // ถ้ามีการเปลี่ยนแปลง ให้ทำการอัปเดตข้อมูลห้อง
    $sql_update = "UPDATE hall SET Hall_Name = ?, Hall_Detail = ?, Hall_Size = ?, Capacity = ?, Status_Hall = ? WHERE Hall_ID = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("sssiii", $hall_name, $hall_detail, $hall_size, $capacity, $status_hall, $hall_id);

    if ($stmt_update->execute()) {
        $_SESSION['message'] = "<div class='alert alert-success'>ห้องถูกอัปเดตสำเร็จ!</div>";
    } else {
        $_SESSION['message'] = "<div class='alert alert-danger'>เกิดข้อผิดพลาด: " . $stmt_update->error . "</div>";
    }

    header('Location: booking.php');
    exit;
}
?>
