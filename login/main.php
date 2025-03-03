<?php
session_start();
include 'auth_check.php'; // เรียกใช้งานการตรวจสอบการเข้าสู่ระบบและสถานะผู้ใช้
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>หน้าหลัก</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
    @media (max-width: 991px) {
        #navbarNav {
            padding-top: 10px;
            padding-bottom: 10px;
        }

        html,
        body {
            margin: 0;
            padding: 0;
        }

        .calendar td {
            height: 80px;
        }

        .booking-dot {
            width: 8px;
            height: 8px;
        }
    }

    html,
    body {
        margin: 0;
        padding: 0;
    }

    body {
        margin: 0;
        padding: 0;
    }

    main {
        margin: 0;
        padding: 0;
    }

    .responsive-img {
        max-width: 100%;
        height: auto;
    }

    .navbar {
        padding-top: 0.5rem;
        padding-bottom: 0.5rem;
    }

    .nav-link {
        padding-top: 0.5rem;
        padding-bottom: 0.5rem;
    }

    .full-height {
        margin-top: 50px;
    }

    .full-height {
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        align-items: center;
        position: relative;
        padding-bottom: 20px;
        flex-grow: 1;
        overflow: auto;
        padding-bottom: 20px;
    }

    .container-custom-1 {
        background-color: #fff;
        padding: 20px;
        border-radius: 5px;
        box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        max-width: 1200px;
        width: 100%;
        margin-bottom: 100px;
        /* กำหนดระยะห่างระหว่างปฏิทินกับ footer */
    }

    .text-center {
        color: white;
        padding: 20px;
        border-radius: 5px 5px 0 0;
        height: 70px;
        display: flex;
        align-items: center;
        max-width: 1200px;
        width: 100%;

    }

    .full-height2 {
        margin-top: 50px;
    }

    .full-height2 {
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        align-items: center;
        position: relative;
        padding-bottom: 20px;
        flex-grow: 1;
        overflow: auto;
        padding-bottom: 20px;
    }

    .container-custom-2 {
        background-color: #fff;
        padding: 20px;
        border-radius: 5px;
        box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        max-width: 1200px;
        width: 100%;
        margin-bottom: 100px;
        /* กำหนดระยะห่างระหว่างปฏิทินกับ footer */
        flex-wrap: wrap; /* ช่วยให้รายการที่เกินขนาดไปยังบรรทัดถัดไป */
    }

    .text-center2 {
        color: white;
        padding: 20px;
        border-radius: 5px 5px 0 0;
        height: 70px;
        display: flex;
        align-items: center;
        max-width: 1200px;
        width: 100%;

    }

    .container-custom-2 {
        background-color: #fff;
        padding: 20px;
        display: flex;
        border-radius: 5px;
        box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        max-width: 1200px;
        width: 100%;
        margin-bottom: 20px;
    }


    .footer {
        position: fixed;
        bottom: 0;
        width: 100%;
        background-color: #f8f9fa;
        padding: 20px;
        font-size: 16px;
        color: #6c757d;
    }

    .calendar-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        width: 100%;
        max-width: 1200px;
        margin: 0 auto;
    }

    #month-year {
        font-size: 1.5em;
        text-align: left;
        color: #fff;
        margin: 0;
    }

    table.calendar {
        width: 100%;
        border-collapse: collapse;
    }

    th,
    td {
        width: 14%;
        text-align: center;
        padding: 10px;
        border: 1px solid #ddd;
    }

    td {
        cursor: pointer;
    }

    td:hover {
        background-color: #f0f0f0;
    }

    .navigation {
        display: flex;
        gap: 10px;
    }

    /* ตั้งค่าขนาดและลักษณะของเซลล์ในปฏิทิน */
    .calendar {
        border-collapse: collapse;
        width: 100%;
        table-layout: fixed;
    }

    .calendar th,
    .calendar td {
        width: 14.2%;
        height: 100px;
        text-align: center;
        vertical-align: top;
        position: relative;
        border: 1px solid #ddd;
        padding: 5px;
    }

    /* จุดสีของกิจกรรม */
    .booking-dots {
        display: flex;
        justify-content: center;
        margin-top: 5px;
        gap: 5px;
    }

    .booking-dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
    }

    /* สีของจุดที่ตรงกับ Message Box */
    .dot-gray {
        background-color: #999999;
    }

    /* สีเทา */
    .dot-red {
        background-color: #d0021b;
    }

    /* สีแดง */
    .dot-yellow {
        background-color: #f5a623;
    }

    /* สีเหลือง */
    .dot-blue {
        background-color: #007bff;
    }

    /* สีน้ำเงิน */
    .dot-green {
        background-color: #4CAF50;
    }

    /* สีเขียว */

    /* ไฮไลต์วันที่ปัจจุบัน */
    .current-day {
        background-color: black;
        color: white;
        font-weight: bold;
    }

    /* รูปแบบการจัดวางข้อมูลใน Message Box */
    .booking-item {
        display: flex;
        align-items: center;
        gap: 10px;
        /* ระยะห่างระหว่างจุดสีและข้อความ */
        margin-bottom: 10px;
    }

    /* จุดสีใน Modal */
    .booking-item .booking-dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        display: inline-block;
        flex-shrink: 0;
        /* ป้องกันไม่ให้จุดสีเปลี่ยนขนาด */
    }

    /* ข้อความกิจกรรม */
    .booking-item p {
        margin: 0;
        font-size: 16px;
    }


    .dot-red {
        background-color: #d0021b;
    }

    .dot-blue {
        background-color: #007bff;
    }

    .dot-green {
        background-color: #4CAF50;
    }

    .dot-yellow {
        background-color: #f5a623;
    }

    .dot-orange {
        background-color: #FFA500;
    }

    .dot-purple {
        background-color: #800080;
    }

    .upcoming {
        width: 350px;
        height: 160px;
        background-color:rgb(1, 66, 136);
        border-radius: 10px;
        margin-right: 15px;
    }

    .activing {
        width: 350px;
        height: 160px;
        background-color:rgb(0, 79, 3);
        border-radius: 10px;
        margin-right: 15px; 
    }

    .disactiving {
        width: 350px;
        height: 160px;
        background-color:rgb(124, 106, 0);
        border-radius: 10px;
        margin-right: 15px;
    }

    .allroom {
        width: 350px;
        height: 160px;
        margin-top: 15px;
        background-color:rgb(106, 1, 1);
        border-radius: 10px;
        margin-right: 15px;
    }

    .allbooking {
        width: 350px;
        height: 160px;
        background-color:rgb(72, 1, 81);
        margin-top: 15px;
        border-radius: 10px;
        margin-right: 15px;

    }


    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark p-3">
        <div class="container-fluid">
            <a href="main.php" class="navbar-brand d-flex align-items-center">
                <img class="responsive-img" src="LOGO.png" alt="system booking" width="45" height="45">
                <span class="ms-3">ระบบจองห้องประชุม</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a href="main.php"
                            class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'main.php') ? 'active' : ''; ?>">หน้าหลัก</a>
                    </li>

                    <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>

                    <!-- Dropdown เมนูสำหรับ "รายการจองของฉัน" -->
                    <li class="nav-item dropdown">
                        <!-- เช็คไฟล์ PHP สำหรับ active -->
                        <a class="nav-link dropdown-toggle <?php echo (in_array(basename($_SERVER['PHP_SELF']), ['upcoming_bookings.php', 'disactive_bookings.php', 'active_bookings.php']) ? 'active' : ''); ?>"
                            href="#" id="myBookingsDropdown" role="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            รายการจองของฉัน
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="myBookingsDropdown">
                            <li><a class="dropdown-item <?php echo (basename($_SERVER['PHP_SELF']) == 'upcoming_bookings.php') ? 'active' : ''; ?>"
                                    href="upcoming_bookings.php">รอตรวจสอบ</a></li>
                            <li><a class="dropdown-item <?php echo (basename($_SERVER['PHP_SELF']) == 'active_bookings.php') ? 'active' : ''; ?>"
                                    href="disactive_bookings.php">อนุมัติ</a></li>
                            <li><a class="dropdown-item <?php echo (basename($_SERVER['PHP_SELF']) == 'disactive_bookings.php') ? 'active' : ''; ?>"
                                    href="active_bookings.php">ไม่อนุมัติ</a></li>
                        </ul>
                    </li>

                    <li class="nav-item">
                        <a href="booking.php"
                            class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'booking.php') ? 'active' : ''; ?>">จองห้อง</a>
                    </li>

                    <?php if ($_SESSION['role_id'] == 2): ?>
                    <li class="nav-item">
                        <a href="members.php"
                            class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'members.php') ? 'active' : ''; ?>">สมาชิก</a>
                    </li>
                    <li class="nav-item">
                        <a href="reports.php"
                            class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'reports.php') ? 'active' : ''; ?>">รายงาน</a>
                    </li>
                    <li class="nav-item">
                        <a href="settings.php"
                            class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'settings.php') ? 'active' : ''; ?>">ตั้งค่า</a>
                    </li>
                    <?php elseif ($_SESSION['role_id'] == 3 || $_SESSION['role_id'] == 4): ?>
                    <li class="nav-item">
                        <a href="reports.php"
                            class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'reports.php') ? 'active' : ''; ?>">รายงาน</a>
                    </li>
                    <?php endif; ?>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            สวัสดี, <?php echo $_SESSION['username']; ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="edit_profile.php">แก้ไขข้อมูล</a></li>
                            <li><a class="dropdown-item" href="logout.php">ออกจากระบบ</a></li>
                        </ul>
                    </li>

                    <?php else: ?>
                    <li class="nav-item">
                        <a href="booking.php" class="nav-link">จองห้อง</a>
                    </li>
                    <li class="nav-item">
                        <a href="index.php" class="nav-link">เข้าสู่ระบบ</a>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>


    <!-- Modal ต้อนรับ -->
    <div class="modal fade" id="alertModal" tabindex="-1" aria-labelledby="alertModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="alertModalLabel">Alert</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?php if (isset($_SESSION['username'])): ?>
                    Hello <?php echo $_SESSION['username']; ?>, Welcome to the meeting room booking website!
                    <?php else: ?>
                    Hello Teacher, Welcome to the meeting room booking website!
                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal สำหรับแสดงรายละเอียดการจอง -->
    <div class="modal fade" id="bookingModal" tabindex="-1" aria-labelledby="bookingModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="bookingModalLabel">รายละเอียดกิจกรรม</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="bookingModalBody">
                    <!-- รายละเอียดจะถูกเพิ่มที่นี่ -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                </div>
            </div>
        </div>
    </div>

    <div class="full-height2">
        <div class="text-center2 bg-dark">
            <div style="font-size: 20px">Dashboard</div>
        </div>
        <div class="container-custom-2">
            <div class="upcoming">
                <div style="padding: 20px; font-size: 30px;">
                    <i class="fa-solid fa-user-check" style="color: #ffffff;"></i>
                </div>
                    <div style="display: block;">
                        <div style="display: flex;">รายการจองห้องของฉัน</div>
                        <div></div>
                    </div>
                <hr>
                <div style="color: #ffffff; font-size: 16px; margin-left: 16px;">จองห้อง รอตรวจสอบ</div>
            </div>
            <div class="activing">
                <div>
                    <div style="padding: 20px; font-size: 30px;">
                        <i class="fa-solid fa-check" style="color: #ffffff;"></i>
                    </div>
                    <div>   
                        <div></div>
                        <div></div>
                    </div>
                </div>
                <hr>
                <div style="color: #ffffff; font-size: 16px; margin-left: 16px;">จองห้อง อนุมัติ</div>
            </div>
            <div class="disactiving">
                <div>
                    <div style="padding: 20px; font-size: 30px;">
                        <i class="fa-solid fa-xmark" style="color: #ffffff;"></i>
                    </div>
                    <div>
                        <div></div>
                        <div></div>
                    </div>
                </div>
                <hr>
                <div style="color: #ffffff; font-size: 16px; margin-left: 16px;">จองห้อง ไม่อนุมัติ</div>
            </div>

            <div class="allbooking">
                <div>
                    <div style="padding: 20px; font-size: 30px;">
                        <i class="fa-solid fa-book" style="color: #ffffff;"></i>
                    </div>
                    <div>
                        <div></div>
                        <div></div>
                    </div>
                </div>
                <hr>
                <div style="color: #ffffff; font-size: 16px; margin-left: 16px;">จองห้อง รอตรวจสอบ</div>
            </div>
            <div class="allroom">
                <div>
                    <div style="padding: 20px; font-size: 30px;">
                        <i class="fa-solid fa-building" style="color: #ffffff;"></i>
                    </div>
                    <div>
                        <div></div>
                        <div></div>
                    </div>
                </div>
                <hr>
                <div style="color: #ffffff; font-size: 16px; margin-left: 16px;">ห้องทั้งหมด</div>
            </div>
        </div>
    </div>


    <div class="full-height">
        <div class="text-center bg-dark">
            <div class="calendar-header">
                <h2 id="month-year"></h2>
                <div class="navigation">
                    <button id="prev-month" class="btn btn-outline-secondary"><</button>
                            <button id="next-month" class="btn btn-outline-secondary">></button>
                </div>
            </div>
        </div>
        <div class="container-custom-1">
            <table class="calendar">
                <thead>
                    <tr>
                        <th>อา.</th>
                        <th>จ.</th>
                        <th>อ.</th>
                        <th>พ.</th>
                        <th>พฤ.</th>
                        <th>ศ.</th>
                        <th>ส.</th>
                    </tr>
                </thead>
                <tbody id="calendar-body"></tbody>
            </table>
        </div>
    </div>

    <div class="footer">
        Copyright 2025 © - BangWa Developer
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    // แสดง Modal เมื่อหน้าเว็บโหลด
    window.onload = function() {
        var alertModal = new bootstrap.Modal(document.getElementById('alertModal'));
        alertModal.show();
    };

    const monthNames = [
        "มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน",
        "กรกฎาคม", "สิงหาคม", "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม"
    ];

    let bookings = [];
    let currentDate = new Date();

    // ดึงข้อมูลจากฐานข้อมูลผ่าน PHP
    async function fetchBookings() {
        try {
            const response = await fetch("get_booking.php"); // เรียกไฟล์ PHP
            bookings = await response.json();
            renderCalendar(currentDate);
        } catch (error) {
            console.error("Error fetching bookings:", error);
        }
    }


    function renderCalendar(date) {
        const month = date.getMonth();
        const year = date.getFullYear();

        document.getElementById('month-year').textContent = `${monthNames[month]} ${year}`;

        const firstDay = new Date(year, month, 1);
        const lastDay = new Date(year, month + 1, 0);
        const totalDays = lastDay.getDate();
        const startDay = firstDay.getDay();

        const calendarBody = document.getElementById('calendar-body');
        calendarBody.innerHTML = '';

        let row = document.createElement('tr');
        const today = new Date();

        for (let i = 0; i < startDay; i++) {
            const cell = document.createElement('td');
            row.appendChild(cell);
        }

        for (let day = 1; day <= totalDays; day++) {
            if (row.children.length === 7) {
                calendarBody.appendChild(row);
                row = document.createElement('tr');
            }

            const cell = document.createElement('td');
            const fullDate = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;

            cell.innerHTML = `<span class="day-number">${day}</span>`;

            // ตรวจสอบว่ามีการจองในวันนี้หรือไม่
            const bookingForDate = bookings.filter(booking => booking.date === fullDate);

            // คลิกได้ทุกวัน (มีหรือไม่มีการจอง)
            cell.addEventListener('click', () => showBookingDetails(bookingForDate, fullDate));

            if (bookingForDate.length > 0) {
                cell.classList.add('has-booking');

                // สร้างจุดสีของกิจกรรมแบบสุ่มจำนวน (1-3 จุดต่อวัน)
                const dotsContainer = document.createElement('div');
                dotsContainer.classList.add('booking-dots');

                let numDots = Math.min(bookingForDate.length, Math.floor(Math.random() * 3) + 1); // 1 ถึง 3 จุดสุ่ม

                for (let i = 0; i < numDots; i++) {
                    const dot = document.createElement('div');
                    dot.classList.add('booking-dot', bookingForDate[i].color);
                    dotsContainer.appendChild(dot);
                }

                cell.appendChild(dotsContainer);
            }


            // ไฮไลต์วันที่ปัจจุบัน
            if (
                day === today.getDate() &&
                month === today.getMonth() &&
                year === today.getFullYear()
            ) {
                cell.classList.add('current-day');
            }

            row.appendChild(cell);
        }

        if (row.children.length > 0) {
            calendarBody.appendChild(row);
        }
    }

    /* ฟังก์ชันแสดงรายละเอียดการจอง */
    function showBookingDetails(bookings, date) {
        const modalBody = document.getElementById('bookingModalBody');
        const modalTitle = document.getElementById('bookingModalLabel');

        if (bookings.length > 0) {
            // แสดงรายการจองปกติ
            modalTitle.textContent = `รายละเอียดการจอง (${date})`;
            modalBody.innerHTML = bookings.map(booking => `
            <div class="booking-item">
                <span class="booking-dot ${booking.color}"></span>
                <p>${booking.details}</p>
            </div>
        `).join('');
        } else {
            // หากไม่มีรายการจอง
            modalTitle.textContent = `ไม่มีรายการจอง (${date})`;
            modalBody.innerHTML =
                `<p style="text-align: center; font-weight: bold; color: red;">ไม่มีการจองในวันนี้</p>`;
        }

        // เปิด Modal
        const bookingModal = new bootstrap.Modal(document.getElementById('bookingModal'));
        bookingModal.show();
    }

    /* ปุ่มเปลี่ยนเดือน */
    document.getElementById('prev-month').addEventListener('click', () => {
        currentDate.setMonth(currentDate.getMonth() - 1);
        renderCalendar(currentDate);
    });

    document.getElementById('next-month').addEventListener('click', () => {
        currentDate.setMonth(currentDate.getMonth() + 1);
        renderCalendar(currentDate);
    });

    /* ดึงข้อมูลจากฐานข้อมูลและแสดงปฏิทิน */
    fetchBookings();



   
    </script>





</body>

</html>