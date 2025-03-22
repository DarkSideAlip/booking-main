<?php
session_start();
include 'db_connect.php'; // ไฟล์เชื่อมต่อฐานข้อมูล

if (isset($_GET['id'])) {
    $booking_id = $_GET['id'];

    // เตรียมคำสั่ง SQL เพื่อดึงข้อมูลจากตาราง booking
    $sql = "SELECT * FROM booking WHERE Booking_ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $booking_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // ดึงข้อมูลเรคคอร์ดแรก (หากมีหลายแถว คุณสามารถ loop ได้)
        $booking = $result->fetch_assoc();

        // แสดงผลเป็นตาราง
        echo "<table class='table table-bordered'>";

        echo "<tr><th>Booking_ID</th><td>" . $booking['Booking_ID'] . "</td></tr>";
        echo "<tr><th>Personnel_ID</th><td>" . $booking['Personnel_ID'] . "</td></tr>";
        echo "<tr><th>Date_Start</th><td>" . $booking['Date_Start'] . "</td></tr>";
        echo "<tr><th>Time_Start</th><td>" . $booking['Time_Start'] . "</td></tr>";
        echo "<tr><th>Time_End</th><td>" . $booking['Time_End'] . "</td></tr>";
        echo "<tr><th>Hall_ID</th><td>" . $booking['Hall_ID'] . "</td></tr>";
        echo "<tr><th>Equipment_ID</th><td>" . $booking['Equipment_ID'] . "</td></tr>";
        echo "<tr><th>Attendee_Count</th><td>" . $booking['Attendee_Count'] . "</td></tr>";
        echo "<tr><th>Booking_Detail</th><td>" . $booking['Booking_Detail'] . "</td></tr>";
        echo "<tr><th>Status_ID</th><td>" . $booking['Status_ID'] . "</td></tr>";
        echo "<tr><th>Approver_ID</th><td>" . $booking['Approver_ID'] . "</td></tr>";
        echo "<tr><th>Topic_Name</th><td>" . $booking['Topic_Name'] . "</td></tr>";
        echo "<tr><th>Approval_Stage</th><td>" . $booking['Approval_Stage'] . "</td></tr>";

        // หากมีไฟล์อัปโหลด (Booking_File_Path ไม่ว่าง) ก็แสดงรูป
        if (!empty($booking['Booking_File_Path'])) {
            echo "<tr><th>รูปที่อัปโหลด</th>
                  <td>
                      <img src='" . $booking['Booking_File_Path'] . "' 
                           style='max-width:400px; height:auto;'
                           alt='Uploaded Image'>
                  </td></tr>";
        } else {
            echo "<tr><th>รูปที่อัปโหลด</th><td>ไม่มีไฟล์แนบ</td></tr>";
        }

        // แสดง File_Type, File_Size, Uploaded_At (ถ้ามีในตาราง)
        echo "<tr><th>File_Type</th><td>" . $booking['File_Type'] . "</td></tr>";
        echo "<tr><th>File_Size</th><td>" . $booking['File_Size'] . "</td></tr>";
        echo "<tr><th>Uploaded_At</th><td>" . $booking['Uploaded_At'] . "</td></tr>";

        echo "</table>";

    } else {
        echo "ไม่พบข้อมูล Booking_ID = " . $booking_id;
    }

} else {
    echo "ไม่พบข้อมูล (ไม่ระบุ Booking_ID)";
}
?>
