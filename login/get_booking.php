<?php
header("Content-Type: application/json");
include 'db_connect.php'; // เชื่อมต่อฐานข้อมูล

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die(json_encode(["error" => "เชื่อมต่อฐานข้อมูลไม่สำเร็จ: " . $conn->connect_error]));
}

// ฟังก์ชันสุ่มสีให้กับจุดในปฏิทิน (หรือจะใช้เงื่อนไข Status_ID ก็ได้)
function getRandomColor() {
    $colors = ["dot-red", "dot-blue", "dot-green", "dot-yellow", "dot-orange", "dot-purple"];
    return $colors[array_rand($colors)];
}

// ตัวอย่าง: ดึงเฉพาะรายการที่ Status_ID = 4 (อนุมัติแล้ว) 
// (ปรับตามเงื่อนไขจริงในระบบของคุณ)
$sql = "
    SELECT 
        b.Booking_ID,
        b.Date_Start,
        b.Time_Start,
        b.Time_End,
        b.Attendee_Count,
        b.Booking_Detail,
        b.Topic_Name,
        b.Status_ID,
        
        -- ตาราง hall
        h.Hall_Name,
        
        -- ตาราง personnel
        p.First_Name,
        p.Last_Name,
        p.Phone  -- สมมุติว่าเอามาใช้แทนเบอร์โทร
        
    FROM booking b
    LEFT JOIN hall h ON b.Hall_ID = h.Hall_ID
    LEFT JOIN personnel p ON b.Personnel_ID = p.Personnel_ID
    
    WHERE b.Status_ID = 4
";

$result = $conn->query($sql);

$bookings = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        
        // รวมชื่อเป็นสตริงเดียว เช่น "สมชาย ใจดี"
        $bookerName = $row['First_Name'] . ' ' . $row['Last_Name'];
        
        // สร้างฟิลด์ booking_time เป็นช่วงเวลา "HH:MM - HH:MM"
        $bookingTime = $row["Time_Start"] . " - " . $row["Time_End"];
        
        // กำหนดรายละเอียดเพิ่มเติม
        // เช่น "Topic_Name - Booking_Detail (Attendee_Count คน)"
        $detailsText = "{$row['Topic_Name']} - {$row['Booking_Detail']} ({$row['Attendee_Count']} คน)";

        // สามารถกำหนดสีตาม Status_ID หรือสุ่มสี
        $color = getRandomColor();

        $bookings[] = [
            // วันที่ (YYYY-MM-DD) สำหรับใช้เทียบในปฏิทิน
            "date"         => $row["Date_Start"],

            // ข้อมูลห้อง
            "room_name"    => $row["Hall_Name"],

            // ชื่อผู้จอง
            "booker_name"  => $bookerName,

            // สมมุติว่าเอา Email มาแทน "เบอร์โทร"
            "booker_phone" => $row["Phone"],

            // ช่วงเวลา
            "booking_time" => $bookingTime,

            // รายละเอียด
            "details"      => $detailsText,

            // สีจุดในปฏิทิน
            "color"        => $color
        ];
    }
}

$conn->close();

// ส่งข้อมูลออกไปในรูปแบบ JSON
echo json_encode($bookings);
?>