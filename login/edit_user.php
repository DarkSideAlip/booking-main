<?php
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['personnel_id'])) {
    $personnel_id = $_POST['personnel_id'];
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

    // ทำการอัปเดตข้อมูล
    $sql = "UPDATE personnel SET first_name=?, last_name=?, username=?, password=?, phone=?, email=?, telegram_id=?, role_id=?, position_id=?, subject_group_id=? WHERE personnel_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssssiii", $first_name, $last_name, $username, $password, $phone, $email, $telegram_id, $role_id, $position_id, $subject_group_id, $personnel_id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "<div class='alert alert-success'>ข้อมูลถูกอัปเดตสำเร็จ!</div>";
    } else {
        $_SESSION['message'] = "<div class='alert alert-danger'>เกิดข้อผิดพลาด: " . $stmt->error . "</div>";
    }

    header("Location: members.php");
}
?>
