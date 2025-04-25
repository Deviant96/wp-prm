document.addEventListener('DOMContentLoaded', function () {
    flatpickr("#event_date", {
        dateFormat: "Y-m-d"
    });

    const startPicker = flatpickr("#event_start_time", {
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        time_24hr: true
    });

    const endPicker = flatpickr("#event_end_time", {
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        time_24hr: true
    });

    const form = document.querySelector('#post');
    form.addEventListener('submit', function (e) {
        const start = document.querySelector('#event_start_time').value;
        const end = document.querySelector('#event_end_time').value;

        if (start && end && start >= end) {
            e.preventDefault();
            alert("End time must be after start time.");
        }
    });
});
