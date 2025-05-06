<div class="events-container p-4 dark:bg-gray-900 text-gray-800 dark:text-gray-100">
    <div class="flex justify-between items-center mb-4">
        <input type="text" id="event-search" placeholder="Search events..." class="input" />
        
        <input type="text" id="date-range" class="input max-w-xs" placeholder="Filter by date range" />

        <button id="calendar-toggle" class="btn">
            <ion-icon name="calendar-outline"></ion-icon>
            <span class="ml-1">Calendar View</span>
        </button>
    </div>

    <div class="flex flex-wrap gap-4 mb-4" id="event-filters">
        <!-- Checkbox Filters -->
        <?php
        $terms = get_terms(['taxonomy' => 'event_type', 'hide_empty' => false]);
        // var_dump($terms); // Debugging line to check the terms fetched
        foreach ($terms as $term) {
            echo "<label class='flex items-center gap-1'><input type='checkbox' value='{$term->slug}' class='event-filter' data-tax='event_type'> {$term->name}</label>";
        }
        ?>
    </div>

    <!-- Upcoming Featured Event -->
    <div class="relative w-full rounded-2xl overflow-hidden bg-gray-100 dark:bg-gray-800 mb-4 shadow-lg">
        <div class="absolute inset-0 bg-gradient-to-br from-blue-500 to-blue-700 opacity-50"></div>
        <img src="<?php get_template_directory_uri() . '/assets/images/events-bg.'; ?>'" alt="" class="w-full h-64 object-cover opacity-80" />
        <div class="absolute inset-0 bg-gradient-to-br from-black/60 to-transparent flex flex-col justify-end p-6 text-white">
            <h2 class="text-2xl font-semibold">Upcoming Event: Partner Growth Summit</h2>
            <p class="text-sm">April 30, 2025 – Singapore</p>
            <a href="#" class="mt-2 inline-block text-blue-300 hover:text-white transition">View all events →</a>
        </div>
    </div>

    <?php
    $events = new WP_Query([
        'post_type' => 'events',
        // filters here
    ]);

    set_query_var('events', $events);
    ?>

    <!-- <div id="event-results" class="grid gap-4 md:grid-cols-2 xl:grid-cols-3"> -->
    <div class="grid md:grid-cols-2 gap-6" id="eventList">
        <?php get_template_part('template-parts/dashboard/events', 'loop'); ?>
    </div>

    <div id="event-pagination" class="mt-6">
        <!-- Pagination will be rendered here -->
    </div>

    <!-- Pagination Placeholder -->
    <div class="flex justify-center pt-6">
        <nav class="flex space-x-2 text-sm">
            <a href="#" class="px-3 py-1 border rounded-lg hover:bg-blue-600 hover:text-white dark:hover:bg-blue-500">1</a>
            <a href="#" class="px-3 py-1 border rounded-lg hover:bg-blue-600 hover:text-white dark:hover:bg-blue-500">2</a>
            <a href="#" class="px-3 py-1 border rounded-lg hover:bg-blue-600 hover:text-white dark:hover:bg-blue-500">Next →</a>
        </nav>
    </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', () => {
    flatpickr("#date-range", {
        mode: "range",
        onChange: fetchEvents
    });

    document.getElementById('event-search').addEventListener('input', fetchEvents);
    document.querySelectorAll('.event-filter').forEach(cb => cb.addEventListener('change', fetchEvents));
    document.getElementById('calendar-toggle').addEventListener('click', toggleCalendarView);

    function fetchEvents() {
        const search = document.getElementById('event-search').value;
        const range = document.getElementById('date-range').value;
        const filters = [...document.querySelectorAll('.event-filter:checked')].map(cb => ({
            tax: cb.dataset.tax,
            value: cb.value
        }));

        fetch(`/wp-json/prm/v1/events`, {
            method: "POST",
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ search, range, filters })
        })
        .then(res => res.text())
        .then(html => {
            document.getElementById('eventList').innerHTML = html;
        });
    }

    function toggleCalendarView() {
        document.getElementById('eventList').classList.toggle('calendar-view');
    }
});
</script>
