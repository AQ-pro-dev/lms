import "./bootstrap";
import { Calendar } from "@fullcalendar/core";
import dayGridPlugin from "@fullcalendar/daygrid";
import interactionPlugin from "@fullcalendar/interaction";
import timeGridPlugin from "@fullcalendar/timegrid";

let calendar;

function initCalendar(events = []) {
    const calendarEl = document.getElementById("calendar");
    if (!calendarEl) return;

    if (calendar) {
        calendar.destroy(); // Properly destroy the existing instance
    }

    calendar = new Calendar(calendarEl, {
        plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
        initialView: "dayGridMonth",
        headerToolbar: {
            left: "prev,next today",
            center: "title",
            right: "dayGridMonth,timeGridWeek,timeGridDay",
        },
        events: events,
        eventContent: function (arg) {
            return {
                html: `<div class="calendarEvent-details">
                            <strong>${arg.event.title}</strong><br/>
                            <small>${arg.event.extendedProps.spotsLeft} spots left</small><br/>
                        </div>`,
            };
        },
        eventClick: function (info) {
            const event = info.event;
            const eventData = {
                title: event.title,
                date: event.start.toISOString(),
                description: event.extendedProps.description,
                spotsLeft: event.extendedProps.spotsLeft,
                class_time: event.extendedProps.class_time,
                onsite: event.extendedProps.onsite || null,
                online: event.extendedProps.online || null,
            };
            Livewire.dispatch("selectEvent", eventData, "calendarEvent");

            window.dispatchEvent(
                new CustomEvent("calendarEventSelected", {
                    detail: eventData,
                })
            );
        },
    });

    calendar.render();
}

window.addEventListener("eventsLoaded", (event) => {
    if (Array.isArray(event.detail)) {
        const events = event.detail.flat();
        initCalendar(events);
    }
});

window.addEventListener("calendarEventSelected", (event) => {
    const selectedEvent = event.detail;
    const classDate = new Date(selectedEvent.date);
    const formattedClassDate = classDate.toLocaleDateString("en-US", {
        year: "numeric",
        month: "long",
        day: "numeric",
    });

    let formattedClassTime = "N/A";
    if (selectedEvent.class_time) {
        const timeWithDate =
            selectedEvent.date.split("T")[0] + "T" + selectedEvent.class_time;
        const classTime = new Date(timeWithDate);
        if (!isNaN(classTime)) {
            formattedClassTime = classTime.toLocaleTimeString([], {
                hour: "2-digit",
                minute: "2-digit",
                hour12: true,
            });
        }
    }

    const modalTitle = document.querySelector(
        "#calendarEventModal .modal-title"
    );
    const spotsLeft = document.querySelector("#calendarEventModal .spotsLeft");
    const date = document.querySelector("#calendarEventModal .date");
    const description = document.querySelector(
        "#calendarEventModal .description"
    );
    const classTime = document.querySelector("#calendarEventModal .class_time");

    modalTitle.textContent = selectedEvent.title || "Event Details";
    spotsLeft.textContent = selectedEvent.spotsLeft || "N/A";
    description.textContent =
        selectedEvent.description || "No description available";
    date.textContent = formattedClassDate || "N/A";
    classTime.textContent = formattedClassTime || "N/A";
    const location = document.querySelector("#calendarEventModal .location");

    if (selectedEvent.onsite) {
        location.textContent = `${selectedEvent.onsite}`;
    } else if (selectedEvent.online) {
        location.innerHTML = `<a href="${selectedEvent.online}" target="_blank">Join via Teams</a>`;
    } else {
        location.textContent = "N/A";
    }

    $("#calendarEventModal").modal("show");
});

document.addEventListener("livewire:load", () => {
    initCalendar([]);
    Livewire.on("updateCalendar", (events) => {
        initCalendar(events);
    });
});
