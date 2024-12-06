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
            height: 100%;
        }

        .container {
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }

        .event-container {
            width: 100%;
            /* ทำให้กล่องเหตุการณ์เต็มความกว้าง */
            margin-top: 20px;
            /* เพิ่มระยะห่างจากปฏิทิน */
        }

        .container {
            margin-top: 80px;
            margin-bottom: 80px;
        }
    }

    html,
    body {
        margin: 0;
        padding: 0;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    body {
        margin: 0;
        padding: 0;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    main {
        margin: 0;
        padding: 0;
        flex-grow: 1;
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

    .calendar {
        margin: 0;
        padding: 0;

        /* ทำให้เนื้อหาภายในปฏิทินเลื่อนได้ */
        border: 1px solid #ddd;
        /* เพิ่มกรอบ */
        border-radius: 8px;
        /* มุมโค้ง */
        padding: 20px;
        /* ระยะห่างภายในปฏิทิน */
        background-color: #f9f9f9;
        /* สีพื้นหลัง */
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        /* เงา */
    }

    .calendar .header {
        display: flex;
        justify-content: space-between;
        /* กระจายปุ่มซ้ายขวา */
        align-items: center;
        /* จัดให้อยู่กลางแนวตั้ง */
        margin-bottom: 20px;
        /* ระยะห่างระหว่าง header และ days */
    }

    .calendar .header h2 {
        font-size: 20px;
        /* ขนาดฟอนต์ */
        margin: 0;
        /* ตัด margin */
        padding: 0;
        /* ตัด padding */
    }

    .header button {
        background: none;
        border: none;
        font-size: 18px;
        cursor: pointer;
    }

    .calendar .day {
        text-align: center;
        /* จัดข้อความให้อยู่กลาง */
        padding: 10px;
        /* เพิ่ม padding ของแต่ละช่อง */
        border-radius: 50%;
        /* มุมโค้ง */
    }

    .calendar .days {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        /* 7 คอลัมน์ */
        gap: 10px;
        /* ระยะห่างระหว่างวัน */
        padding: 10px;
        /* ระยะห่างรอบ ๆ ตารางวัน */
        background-color: #fff;
        /* สีพื้นหลัง */
        border-radius: 8px;
        /* มุมโค้ง */
        position: relative;
        box-sizing: border-box;
        /* ไม่ให้ขนาดเปลี่ยนแปลงเมื่อมีการเพิ่ม padding หรือ border */
        padding: 10px;
        margin: 0;
        transition: padding 0.3s ease, transform 0.3s ease;
        /* ให้การเปลี่ยนแปลง padding และ transform เป็นไปอย่างราบรื่น */
    }

    .day:hover {
        background-color: #e5e7eb;
        color: #000000;
        font-weight: bold;
        border-radius: 50%;
        padding: auto;
        /* ทำให้เป็นวงกลม */
        width: 40px;
        /* กำหนดความกว้าง */
        height: 40px;
        /* กำหนดความสูง */
        display: flex;
        /* ใช้ flexbox เพื่อให้ข้อความอยู่กลาง */
        justify-content: center;
        /* จัดข้อความให้อยู่กลางแนวนอน */
        align-items: center;
        /* จัดข้อความให้อยู่กลางแนวตั้ง */
        margin: 0 auto;
        /* จัดให้อยู่กลาง */
        transform: scale(1.1);
        /* ขยายขนาดให้ดูเด่นขึ้น แต่ไม่ทำให้ขนาดอื่นๆ เปลี่ยนแปลง */
        z-index: 2;
        /* เพิ่มความเด่นให้วันที่เมื่อ hover */
    }


    .calendar .day.current {
        background-color: #0e02b8;
        color: #fff;
        font-weight: bold;
        border-radius: 50%;
        /* ทำให้เป็นวงกลม */
        width: 40px;
        /* กำหนดความกว้าง */
        height: 40px;
        /* กำหนดความสูง */
        display: flex;
        /* ใช้ flexbox เพื่อให้ข้อความอยู่กลาง */
        justify-content: center;
        /* จัดข้อความให้อยู่กลางแนวนอน */
        align-items: center;
        /* จัดข้อความให้อยู่กลางแนวตั้ง */
        margin: 0 auto;
        /* จัดให้อยู่กลาง */
    }

    .calendar .day.inactive {
        color: #bbb;
        padding: auto;
    }



    /* ปรับ padding ของ container */
    .container {
        height: 100vh;
        /* ความสูงเต็มหน้าจอ */
        /* กำหนดความสูงที่ต้องการ */
        display: flex;
        overflow-y: auto;
        /* ทำให้มีการเลื่อนขึ้นลงได้ */
        justify-content: space-between;
        align-items: flex-start;
        gap: 20px;
        /* ระยะห่างระหว่าง calendar และ event-container */
        padding: 20px;
        /* ระยะห่างจากขอบ container */
    }


    .calendar .day.selected {
        background-color: #000000;
        color: #fff;
        font-weight: bold;
        border-radius: 50%;
        /* ทำให้เป็นวงกลม */
        width: 40px;
        /* กำหนดความกว้าง */
        height: 40px;
        /* กำหนดความสูง */
        display: flex;
        /* ใช้ flexbox เพื่อให้ข้อความอยู่กลาง */
        justify-content: center;
        /* จัดข้อความให้อยู่กลางแนวนอน */
        align-items: center;
        /* จัดข้อความให้อยู่กลางแนวตั้ง */
        margin: 0 auto;
        /* จัดให้อยู่กลาง */
    }


    .event-container {
        height: 450px;
        flex: 1;
        /* ให้พื้นที่เหมาะสม */
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 20px;
        background-color: #f9f9f9;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .event-container .event-title {
        margin-bottom: 20px;
        font-size: 18px;
        font-weight: bold;
        color: #333;
    }

    .event-container .event-item {
        margin-bottom: 10px;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 8px;
        background-color: #fff;
    }

    .event-item:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
    }

    .event-date {
        font-size: 15px;
        font-weight: bold;
        color: #3b82f6;
        margin-right: 15px;
    }

    .event-description {
        color: #374151;
        font-size: 12px;
        line-height: 1.4;
        text-align: left;
        max-width: 280px;
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

    #monthYear {
        font-size: 24px;
        /* ปรับขนาดฟอนต์เป็น 24px */
        font-weight: bold;
        /* ทำให้ตัวหนา (ถ้าต้องการ) */
        text-align: center;
        /* จัดข้อความให้อยู่ตรงกลาง */
        margin: 10px 0;
        /* เพิ่มระยะห่างระหว่างส่วนหัวกับปฏิทิน */
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

    <div class="full-height">
        <div class="container container-custom">
            <div class="calendar">
                <div class="header">
                    <button onclick="prevMonth()">
                        <i class="fas fa-angle-left"></i>
                    </button>
                    <h2 id="monthYear"></h2>
                    <button onclick="nextMonth()">
                        <i class="fas fa-angle-right"></i>
                    </button>
                </div>
                <div class="days" id="calendarDays"></div>
            </div>
            
            <div class="event-container">
                <h2 class="event-title"><span id="selectedMonthYear"></span></h2>
                <div id="eventDetails"></div>
            </div>
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

    const monthNames = ["มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน", "กรกฎาคม", "สิงหาคม",
        "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม"
    ];
    const daysInWeek = ["อา.", "จ.", "อ.", "พ.", "พฤ.", "ศ.", "ส."];

    let currentDate = new Date();

    const events = {
        "2024-11-05": {
            title: "ค่ายส่งเสริมความเป็นเลิศด้านนาฏศิลป์",
            description: "2 พฤศจิกายน 2024"
        },
        "2024-11-04": {
            title: "นักเรียนที่ลงทะเบียนเรียนซ้ำพร้อมผู้ปกครอง และครูประจำวิชา ประชุมพร้อมกัน ณ หอประชุม เวลา 14.30 น.",
            description: "4 พฤศจิกายน 2024"
        },
        "2024-11-09": {
            title: "ติวสอบวัดระดับ JLPT, A-level",
            description: "9 - 10 พฤศจิกายน 2024"
        },
    };

    // ฟังก์ชัน formatDate สำหรับแปลงวันที่ให้เป็นรูปแบบย่อ
    const formatDate = (date) => {
        const options = {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        };
        return new Date(date).toLocaleDateString('th-TH', options); // ใช้ 'th-TH' สำหรับภาษาไทย
    };

    function showEventDetails(month, year) {
        const eventDetails = document.getElementById("eventDetails");
        const selectedMonthYear = document.getElementById("selectedMonthYear");

        // เปลี่ยนข้อความในหัวข้อจาก "Event for" เป็น "กิจกรรมของเดือน"
        selectedMonthYear.innerText = `กิจกรรมของเดือน ${monthNames[month]} ${year}`;

        // ล้างข้อมูลเหตุการณ์ก่อนหน้า
        eventDetails.innerHTML = '';

        // กรองเหตุการณ์ตามเดือนและปีปัจจุบัน
        for (const [date, event] of Object.entries(events)) {
            const eventDate = new Date(date);
            if (eventDate.getMonth() === month && eventDate.getFullYear() === year) {
                const formattedDate = formatDate(eventDate); // ใช้ formatDate ในการแปลงวันที่
                const eventElement = document.createElement("div");
                eventElement.classList.add("event-item");
                eventElement.innerHTML = `
                    <div class="event-date">${formattedDate}</div>
                    <div class="event-description">${event.title} - ${formatDate(event.description)}</div> <!-- แปลงวันที่ใน description ด้วย formatDate -->
                `;
                eventDetails.appendChild(eventElement);
            }
        }

        // ถ้าไม่มีเหตุการณ์ใดๆ ให้แสดงข้อความ
        if (eventDetails.innerHTML === '') {
            eventDetails.innerHTML = '<p>ไม่มีกิจกรรมสำหรับเดือนนี้</p>';
        }
    }

    function renderCalendar() {
        const month = currentDate.getMonth();
        const year = currentDate.getFullYear();
        document.getElementById("monthYear").innerText = `${monthNames[month]} ${year}`;

        const firstDay = new Date(year, month, 1).getDay();
        const daysInMonth = new Date(year, month + 1, 0).getDate();
        const daysInPrevMonth = new Date(year, month, 0).getDate(); // จำนวนวันของเดือนก่อนหน้า

        const calendarDays = document.getElementById("calendarDays");
        calendarDays.innerHTML = "";

        // แสดงชื่อวัน (อาทิตย์-เสาร์)
        daysInWeek.forEach(day => {
            const dayElement = document.createElement("div");
            dayElement.classList.add("day");
            dayElement.textContent = day;
            calendarDays.appendChild(dayElement);
        });

        // เติมวันที่ของเดือนก่อนหน้าในช่องว่าง (ไม่ใช้สีเทา)
        for (let i = firstDay - 1; i >= 0; i--) {
            const prevDayElement = document.createElement("div");
            prevDayElement.classList.add("day");
            prevDayElement.textContent = daysInPrevMonth - i;
            prevDayElement.classList.add("inactive"); // เพิ่มคลาส inactive สำหรับวันที่เดือนก่อนหน้า
            calendarDays.appendChild(prevDayElement);
        }

        // แสดงวันที่ของเดือนปัจจุบัน
        for (let i = 1; i <= daysInMonth; i++) {
            const dayElement = document.createElement("div");
            dayElement.classList.add("day");
            dayElement.textContent = i;

            // เพิ่มการทำงานเมื่อคลิกวันที่
            dayElement.addEventListener("click", function() {
                const dateString = `2024-11-${i.toString().padStart(2, '0')}`;

                // ลบคลาส 'selected' จากวันที่ที่เคยเลือกแล้ว
                const previouslySelected = document.querySelector(".selected");
                if (previouslySelected) {
                    previouslySelected.classList.remove("selected");
                }

                // เพิ่มคลาส 'selected' ให้กับวันที่ที่ถูกคลิก
                dayElement.classList.add("selected");
            });

            if (i === currentDate.getDate() && month === new Date().getMonth() && year === new Date().getFullYear()) {
                dayElement.classList.add("current");
            }

            calendarDays.appendChild(dayElement);
        }

        // เติมวันที่ของเดือนถัดไปในช่องว่างที่เหลือ (ไม่ใช้สีเทา)
        const totalCells = firstDay + daysInMonth;
        const nextDays = 7 - (totalCells % 7); // จำนวนวันที่ต้องเติมสำหรับเดือนถัดไป
        if (nextDays < 7) { // ตรวจสอบว่ามีช่องว่างในสัปดาห์สุดท้าย
            for (let i = 1; i <= nextDays; i++) {
                const nextDayElement = document.createElement("div");
                nextDayElement.classList.add("day");
                nextDayElement.textContent = i;
                nextDayElement.classList.add("inactive"); // เพิ่มคลาส inactive สำหรับวันที่เดือนถัดไป
                calendarDays.appendChild(nextDayElement);
            }
        }

        // แสดงเหตุการณ์ทั้งหมดสำหรับเดือนนี้
        showEventDetails(month, year);
    }

    function prevMonth() {
        currentDate.setMonth(currentDate.getMonth() - 1);
        renderCalendar();
    }

    function nextMonth() {
        currentDate.setMonth(currentDate.getMonth() + 1);
        renderCalendar();
    }

    renderCalendar();
    </script>





</body>

</html>