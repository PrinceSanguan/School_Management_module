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
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@200&family=Oswald:wght@200..700&family=Poppins:wght@100;200;300;400;600;700&family=Roboto+Condensed:ital,wght@0,400;0,500;1,400&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Sharp:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <title>CompassED LMS</title>
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

/* Mobile responsiveness */
@media screen and (max-width: 768px) {
    #calendar {
        grid-template-columns: repeat(3, 1fr); /* Reduce columns to 3 */
        gap: 5px;
    }

    #monthYear {
        font-size: 1.5em;
    }

    .day {
        padding: 10px;
    }

    .calendar-header {
        flex-direction: column;
        align-items: flex-start;
    }

    #prevMonth, #nextMonth {
        font-size: 1em;
        padding: 8px;
    }

    #prevMonth, #nextMonth {
        width: 48%; /* Reduce button width */
    }
}

@media screen and (max-width: 480px) {
    #calendar {
        grid-template-columns: repeat(2, 1fr); /* Reduce to 2 columns */
        gap: 5px;
    }

    #monthYear {
        font-size: 1.2em;
    }

    .day {
        padding: 8px;
        font-size: 0.8em;
    }

    #prevMonth, #nextMonth {
        font-size: 0.9em;
        padding: 6px;
    }

    .calendar-header {
        flex-direction: column;
        align-items: center;
    }

    #prevMonth, #nextMonth {
        width: 45%; /* Buttons take less space on mobile */
    }
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