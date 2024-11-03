<?php
require 'database/config.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="asset/images/logo.webp" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <title>CompassED LMS</title>
    <style>
        :root {
            --primary-color: #00bcd4;
            --secondary-color: #0097a7;
            --background-dark: #121212;
            --surface-dark: #1a1a1a;
            --card-dark: #2e2e2e;
            --text-primary: #f0f0f0;
            --text-secondary: #a0a0a0;
            --danger-color: #f44336;
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--background-dark);
            color: var(--text-primary);
            min-height: 100vh;
            padding: 20px;
        }

        /* Navbar Styles */
        .navbar {
            background: var(--surface-dark);
            padding: 1rem 2rem;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        }

        .nav-links {
            display: flex;
            justify-content: flex-end;
            list-style: none;
        }

        .menu {
            display: flex;
            gap: 2rem;
        }

        .menu li a {
            color: var(--text-primary);
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
        }

        .menu li a:hover {
            color: var(--primary-color);
        }

        /* Calendar Container */
        .container {
            max-width: 1200px;
            margin: 80px auto 20px;
            background: var(--surface-dark);
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }

        /* Calendar Header */
        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding: 0 1rem;
        }

        #monthYear {
            font-size: 2rem;
            font-weight: 600;
            color: var(--primary-color);
        }

        #prevMonth, #nextMonth {
            background-color: var(--card-dark);
            border: none;
            color: var(--text-primary);
            font-size: 1.5rem;
            padding: 0.8rem 1.5rem;
            border-radius: 8px;
            cursor: pointer;
            transition: var(--transition);
        }

        #prevMonth:hover, #nextMonth:hover {
            background-color: var(--primary-color);
            transform: translateY(-2px);
        }

        /* Calendar Grid */
        #calendar {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 15px;
            padding: 1rem;
        }

        .header {
            font-weight: 500;
            text-align: center;
            padding: 1rem;
            background: var(--card-dark);
            color: var(--primary-color);
            border-radius: 8px;
        }

        .day {
            background: var(--card-dark);
            padding: 1rem;
            border-radius: 8px;
            min-height: 100px;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .day:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .event {
            background: var(--primary-color);
            color: white;
            padding: 0.5rem;
            border-radius: 6px;
            font-size: 0.85rem;
            cursor: grab;
            transition: var(--transition);
        }

        .event:hover {
            background: var(--secondary-color);
        }

        /* Event Form */
        #eventForm {
            background: var(--card-dark);
            padding: 2rem;
            border-radius: 12px;
            margin-top: 2rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        #eventForm input {
            width: 100%;
            padding: 1rem;
            margin-bottom: 1rem;
            background: var(--surface-dark);
            color: var(--text-primary);
            border: 1px solid var(--primary-color);
            border-radius: 8px;
            font-size: 1rem;
        }

        #eventForm input:focus {
            outline: none;
            border-color: var(--secondary-color);
        }

        /* Buttons */
        #saveEvent, #deleteEvent {
            width: 100%;
            padding: 1rem;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
        }

        #saveEvent {
            background-color: var(--primary-color);
            color: white;
            margin-bottom: 1rem;
        }

        #deleteEvent {
            background-color: var(--danger-color);
            color: white;
        }

        #saveEvent:hover, #deleteEvent:hover {
            filter: brightness(110%);
            transform: translateY(-2px);
        }

        /* Mobile Responsiveness */
        @media screen and (max-width: 1024px) {
            .container {
                padding: 1.5rem;
            }

            #calendar {
                gap: 10px;
            }

            .day {
                min-height: 80px;
                padding: 0.8rem;
            }
        }

        @media screen and (max-width: 768px) {
            body {
                padding: 10px;
            }

            .container {
                margin-top: 60px;
                padding: 1rem;
            }

            #monthYear {
                font-size: 1.5rem;
            }

            #calendar {
                grid-template-columns: repeat(7, 1fr);
                gap: 5px;
            }

            .header, .day {
                padding: 0.5rem;
                font-size: 0.9rem;
            }

            .day {
                min-height: 60px;
            }

            .menu {
                gap: 1rem;
            }
        }

        @media screen and (max-width: 480px) {
            .navbar {
                padding: 0.8rem 1rem;
            }

            .calendar-header {
                flex-direction: row;
                gap: 0.5rem;
            }

            #monthYear {
                font-size: 1.2rem;
            }

            #prevMonth, #nextMonth {
                padding: 0.5rem 1rem;
                font-size: 1.2rem;
            }

            #calendar {
                grid-template-columns: repeat(7, 1fr);
                gap: 3px;
            }

            .header, .day {
                padding: 0.3rem;
                font-size: 0.8rem;
            }

            .day {
                min-height: 50px;
            }

            .event {
                padding: 0.3rem;
                font-size: 0.75rem;
            }

            .menu {
                gap: 0.8rem;
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <ul class="nav-links">
            <div class="menu">
                <li><a href="index.php">Home</a></li>
                <li><a href="about.php">About</a></li>
                <li><a href="calendar.php">Calendar</a></li>
                <li><a href="login.php">Login</a></li>
            </div>
        </ul>
    </nav>

    <div class="container">
        <div class="calendar-header">
            <button id="prevMonth"><i class='bx bx-chevron-left'></i></button>
            <span id="monthYear"></span>
            <button id="nextMonth"><i class='bx bx-chevron-right'></i></button>
        </div>
        <div id="calendar"></div>

        <input type="hidden" id="eventDate" />
        <input type="hidden" id="eventTitle" placeholder="Event Title" />
        <input type="hidden" id="eventId" />
        <input type="hidden" id="saveEvent" />
        <input type="hidden" id="deleteEvent" />
    </div>
    
    <script>
    document.addEventListener('DOMContentLoaded', function () {
    const calendar = document.getElementById('calendar');
    const monthYear = document.getElementById('monthYear');
    const eventForm = document.getElementById('eventForm');
    const eventDateInput = document.getElementById('eventDate');
    const eventTitleInput = document.getElementById('eventTitle');
    const eventIdInput = document.getElementById('eventId');
    const saveEventButton = document.getElementById('saveEvent');
    const deleteEventButton = document.getElementById('deleteEvent');
    const prevMonthButton = document.getElementById('prevMonth');
    const nextMonthButton = document.getElementById('nextMonth');

    let currentDate = new Date();

    // Fetch events from the backend
    function fetchEvents() {
        fetch('controller/AdminController/get_events.php')
            .then(response => response.json())
            .then(events => {
                renderCalendar(events);
            })
            .catch(error => console.error('Error fetching events:', error));
    }

    // Render calendar and populate it with events
    function renderCalendar(events) {
        calendar.innerHTML = ''; // Clear the calendar content
        monthYear.textContent = `${currentDate.toLocaleString('default', { month: 'long' })} ${currentDate.getFullYear()}`; // Update month-year display

        const headers = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        headers.forEach(day => {
            const headerDiv = document.createElement('div');
            headerDiv.classList.add('header');
            headerDiv.textContent = day;
            calendar.appendChild(headerDiv);
        });

        const year = currentDate.getFullYear();
        const month = currentDate.getMonth();
        const firstDay = new Date(year, month, 1).getDay();
        const daysInMonth = new Date(year, month + 1, 0).getDate();

        // Fill in blank days for alignment
        for (let i = 0; i < firstDay; i++) {
            const blankDiv = document.createElement('div');
            calendar.appendChild(blankDiv);
        }

        // Fill in days with events
        for (let day = 1; day <= daysInMonth; day++) {
            const dayDiv = document.createElement('div');
            dayDiv.classList.add('day');
            dayDiv.textContent = day;

            const dateKey = `${year}-${(month + 1).toString().padStart(2, '0')}-${day.toString().padStart(2, '0')}`;
            const dayEvents = events.filter(event => event.date === dateKey);

            // Display events for the specific day
            dayEvents.forEach(event => {
                const eventDiv = document.createElement('div');
                eventDiv.classList.add('event');
                eventDiv.textContent = event.title;
                dayDiv.appendChild(eventDiv);
            });

            // When a day is clicked, open the form for adding or editing the event
            dayDiv.addEventListener('click', () => {
                eventDateInput.value = dateKey;
                eventTitleInput.value = dayEvents.length ? dayEvents[0].title : '';
                eventIdInput.value = dayEvents.length ? dayEvents[0].id : '';
                eventForm.classList.remove('hidden');
                deleteEventButton.classList.toggle('hidden', !dayEvents.length);
            });

            calendar.appendChild(dayDiv);
        }
    }
    

    // Navigation for previous month
    prevMonthButton.addEventListener('click', () => {
        currentDate.setMonth(currentDate.getMonth() - 1);
        fetchEvents();
    });

    // Navigation for next month
    nextMonthButton.addEventListener('click', () => {
        currentDate.setMonth(currentDate.getMonth() + 1);
        fetchEvents();
    });

    // Initial fetch of events
    fetchEvents();
});
</script>
</body>
</html>