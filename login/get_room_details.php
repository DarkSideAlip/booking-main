<?php
include 'db_connect.php';

if (isset($_GET['id'])) {
    $hall_id = $_GET['id'];

    // ดึงข้อมูลห้องจากฐานข้อมูล
    $sql = "SELECT * FROM hall WHERE Hall_ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $hall_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $hall = $result->fetch_assoc();

    if ($hall) {
        echo "<table class='table table-bordered'>";
        echo "<tr><th>ชื่อห้อง</th><td>" . $hall['Hall_Name'] . "</td></tr>";
        echo "<tr><th>รายละเอียด</th><td>" . $hall['Hall_Detail'] . "</td></tr>";
        echo "<tr><th>ขนาดห้อง</th><td>" . $hall['Hall_Size'] . "</td></tr>";
        echo "<tr><th>ความจุ</th><td>" . $hall['Capacity'] . " คน</td></tr>";
        echo "</table>";
    } else {
        echo "ไม่พบข้อมูล";
    }
} else {
    echo "ไม่พบข้อมูล";
}


?>
