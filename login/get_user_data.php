<?php
include 'db_connect.php';

if (isset($_GET['id'])) {
    $personnel_id = $_GET['id'];

    // ตรวจสอบว่า personnel_id เป็นตัวเลขหรือไม่
    if (is_numeric($personnel_id)) {
        $sql = "SELECT * FROM personnel WHERE personnel_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $personnel_id);
        $stmt->execute();
        $result = $stmt->get_result();

        // ตรวจสอบว่าเราได้ผลลัพธ์หรือไม่
        if ($result->num_rows > 0) {
            $userData = $result->fetch_assoc();
            echo json_encode($userData); // ส่งกลับข้อมูลในรูปแบบ JSON
        } else {
            echo json_encode(['message' => 'ไม่พบข้อมูลผู้ใช้']); // ถ้าไม่พบข้อมูล
        }
    } else {
        echo json_encode(['message' => 'ไม่พบข้อมูลผู้ใช้']); // ถ้า ID ไม่ใช่ตัวเลข
    }
} else {
    echo json_encode(['message' => 'ไม่พบข้อมูลผู้ใช้']); // ถ้าไม่มีการส่ง ID มา
}

$conn->close();
?>
