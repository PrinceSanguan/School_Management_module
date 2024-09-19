<?php
include "../database/database.php";

session_start();

// Check if the user is logged in
if (!isset($_SESSION['userId']) || !isset($_SESSION['userRole'])) {
  $_SESSION['error'] = "Please log in to access this page.";
  header("Location: ../../login.php");
  exit();
}

// Check if the user is an admin or the verified user
if ($_SESSION['userRole'] !== 'admin' && $_SESSION['userId'] != $subject['userId']) {
  $_SESSION['error'] = "You do not have permission to access this page.";
  header("Location: ../../index.php");
  exit();
}


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Calendar</title>
    <style>
/* General styling */
body {
    font-family: 'Arial', sans-serif;
    background-color: #121212;
    color: #f0f0f0;
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

.container {
    max-width: 1000px;
    width: 90%;
    margin: auto;
    background: #1a1a1a;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0px 10px 20px rgba(0, 0, 0, 0.5);
}

.calendar-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

#prevMonth, #nextMonth {
    background-color: #2e2e2e;
    border: none;
    color: #fff;
    font-size: 1.2em;
    padding: 10px;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

#prevMonth:hover, #nextMonth:hover {
    background-color: #00bcd4;
}

#monthYear {
    font-size: 1.8em;
    font-weight: bold;
    color: #00bcd4;
}

#calendar {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 10px;
}

.header {
    font-weight: bold;
    text-align: center;
    padding: 10px 0;
    background: #2a2a2a;
    color: #00bcd4;
    border-radius: 5px;
}

.day {
    background: #2e2e2e;
    padding: 15px;
    border-radius: 5px;
    position: relative;
    cursor: pointer;
    transition: transform 0.3s ease;
}

.day:hover {
    transform: scale(1.05);
}

.event {
    background: #ff4081;
    color: #fff;
    padding: 5px;
    border-radius: 3px;
    margin-top: 5px;
    font-size: 0.8em;
    cursor: grab;
}

#eventForm {
    background: #2e2e2e;
    padding: 20px;
    border-radius: 10px;
    margin-top: 20px;
    box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.5);
}

#eventForm.hidden {
    display: none;
}

#eventForm h2 {
    color: #00bcd4;
    margin-bottom: 10px;
}

#eventForm input {
    width: 100%;
    padding: 10px;
    margin-bottom: 10px;
    background: #3e3e3e;
    color: #fff;
    border: none;
    border-radius: 5px;
}

#saveEvent, #deleteEvent {
    padding: 10px;
    width: 100%;
    background-color: #00bcd4;
    border: none;
    color: #fff;
    cursor: pointer;
    border-radius: 5px;
    transition: background-color 0.3s ease;
}

#saveEvent:hover, #deleteEvent:hover {
    background-color: #0097a7;
}

#deleteEvent {
    background-color: #f44336;
}

#deleteEvent.hidden {
    display: none;
}
    </style>
</head>
<body>
    <div class="container">
        <div class="calendar-header">
            <button id="prevMonth">&lt;</button>
            <span id="monthYear"></span>
            <button id="nextMonth">&gt;</button>
        </div>
        <div id="calendar"></div>
        <div id="eventForm" class="hiden">
            <h2>Add/Edit Event</h2>
            <input type="date" id="eventDate" />
            <input type="text" id="eventTitle" placeholder="Event Title" />
            <input type="hidden" id="eventId" />
            <button id="saveEvent">Save Event</button>
            <button id="deleteEvent" class="hidden">Delete Event</button>
        </div>
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
        fetch('../controller/AdminController/get_events.php')
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

    // Save event (either add new or update existing)
    saveEventButton.addEventListener('click', () => {
        const date = eventDateInput.value;
        const title = eventTitleInput.value;
        const id = eventIdInput.value;

        if (date && title) {
            const url = id ? '../controller/AdminController/update_event.php' : '../controller/AdminController/add_event.php';
            const data = new URLSearchParams();
            data.append('date', date);
            data.append('title', title);
            if (id) {
                data.append('id', id);
            }

            fetch(url, {
                method: 'POST',
                body: data
            })
            .then(response => response.text())
            .then(() => {
                eventDateInput.value = '';
                eventTitleInput.value = '';
                eventIdInput.value = '';
                eventForm.classList.add('hidden');
                fetchEvents(); // Refresh the calendar after saving
            });
        }
    });

    // Delete event
    deleteEventButton.addEventListener('click', () => {
    const id = eventIdInput.value;
    
    if (id) {
        fetch('../controller/AdminController/delete_event.php', {
            method: 'POST',
            body: new URLSearchParams({ id })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.text();
        })
        .then(data => {
            console.log('Server response:', data);
            eventDateInput.value = '';
            eventTitleInput.value = '';
            eventIdInput.value = '';
            eventForm.classList.add('hidden');
            fetchEvents(); // Refresh the calendar after deleting
        })
        .catch(error => {
            console.error('There was a problem with the fetch operation:', error);
        });
    } else {
        console.error('No event ID provided');
    }
});

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