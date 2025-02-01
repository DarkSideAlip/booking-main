<?php
header("Content-Type: application/json");
include 'db_connect.php'; // เชื่อมต่อฐานข้อมูล

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die(json_encode(["error" => "เชื่อมต่อฐานข้อมูลไม่สำเร็จ: " . $conn->connect_error]));
}

// ฟังก์ชันสุ่มสีให้กับจุดในปฏิทิน
function getRandomColor() {
    $colors = ["dot-red", "dot-blue", "dot-green", "dot-yellow", "dot-orange", "dot-purple"];
    return $colors[array_rand($colors)];
}

// คำสั่ง SQL ดึงข้อมูลเฉพาะที่ `Status_ID = 1`
$sql = "SELECT 
            b.Date_Start, 
            b.Time_Start, 
            b.Time_End, 
            h.Hall_Name, 
            b.Attendee_Count, 
            b.Booking_Detail, 
            b.Topic_Name 
        FROM booking b
        LEFT JOIN hall h ON b.Hall_ID = h.Hall_ID
        WHERE b.Status_ID = 2";  // เฉพาะที่อนุมัติแล้ว (Status_ID = 2)

$result = $conn->query($sql);

$bookings = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $bookings[] = [
            "date" => $row["Date_Start"],
            "details" => "{$row['Topic_Name']} - {$row['Booking_Detail']} ({$row['Attendee_Count']} คน)",
            "color" => "dot-green" // สีสามารถกำหนดตามเงื่อนไขเพิ่มเติม
        ];
    }
}

// ปิดการเชื่อมต่อฐานข้อมูล
$conn->close();

// ส่งข้อมูลออกไปในรูปแบบ JSON
echo json_encode($bookings);
?>
