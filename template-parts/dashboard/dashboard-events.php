<div class="events-container p-4 dark:bg-gray-900 text-gray-800 dark:text-gray-100">
    <!-- Filters -->
    <div class="flex justify-between items-center mb-4">
        <div class="flex items-center gap-4 relative">
            <label for="event-search" class="text-sm">Search Events:</label>
            <input type="text" id="event-search" placeholder="Search events..." class="input" /> 
            
            <!-- Loading spinner (hidden) -->
            <div id="loadingSpinnerSearch" class="absolute left-full ml-2 hidden">
                <svg class="animate-spin h-4 w-4 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
        </div>
        
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
        'post_type' => 'tbyte_prm_events',
        'posts_per_page' => 3,
        'paged' => get_query_var('paged') ? get_query_var('paged') : 1,
        // filters here
    ]);
    set_query_var('events', $events);
    ?>

    <!-- <div id="event-results" class="grid gap-4 md:grid-cols-2 xl:grid-cols-3"> -->
    <div class="grid md:grid-cols-2 gap-6" id="eventList">
        <?php get_template_part('template-parts/dashboard/events/events', 'loop'); ?>
    </div>

    <div id="event-pagination" class="mt-6 flex justify-center">
        <?php
        $total_pages = $events->max_num_pages;
        if ($total_pages > 1) {
            $current_page = max(1, get_query_var('paged'));
            
            echo '<nav class="flex items-center space-x-2">';
            
            // Previous button
            if ($current_page > 1) {
                echo '<a href="' . get_pagenum_link($current_page - 1) . '" class="px-4 py-2 border rounded-lg hover:bg-blue-600 hover:text-white transition-colors duration-200 flex items-center" data-page="' . ($current_page - 1) . '">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Previous
                </a>';
            }
            
            // Page numbers
            $range = 2; // how many pages to show around current page
            $show_items = ($range * 2) + 1;
            
            for ($i = 1; $i <= $total_pages; $i++) {
                if (1 != $total_pages && (!($i >= $current_page + $range + 1 || $i <= $current_page - $range - 1) || $total_pages <= $show_items)) {
                    if ($current_page == $i) {
                        echo '<span class="px-4 py-2 bg-blue-600 text-white rounded-lg">' . $i . '</span>';
                    } else {
                        echo '<a href="' . get_pagenum_link($i) . '" class="px-4 py-2 border rounded-lg hover:bg-blue-600 hover:text-white transition-colors duration-200" data-page="' . $i . '">' . $i . '</a>';
                    }
                }
            }
            
            // Next button
            if ($current_page < $total_pages) {
                echo '<a href="' . get_pagenum_link($current_page + 1) . '" class="px-4 py-2 border rounded-lg hover:bg-blue-600 hover:text-white transition-colors duration-200 flex items-center" data-page="' . ($current_page + 1) . '">
                    Next
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>';
            }
            
            echo '</nav>';
        }
        ?>
    </div>

    <!-- Loading spinner (hidden by default) -->
    <div id="loadingSpinner" class="flex justify-center my-8 hidden">
        <svg class="animate-spin -ml-1 mr-3 h-8 w-8 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
    </div>
</div>

<!-- Load events filter -->
<script src="<?php echo get_template_directory_uri(); ?>/assets/js/events-filter.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const eventList = document.getElementById('eventList');
        const pagination = document.getElementById('event-pagination');
        const loadingSpinner = document.getElementById('loadingSpinner');
        
        if (!pagination) return;
        
        // Handle pagination clicks
        pagination.addEventListener('click', function(e) {
            e.preventDefault();
            
            const link = e.target.closest('a');
            if (!link) return;
            
            const page = link.getAttribute('data-page');
            if (!page) return;
            
            loadEvents(page);
        });
        
        // Load events via AJAX
        function loadEvents(page) {
            loadingSpinner.classList.remove('hidden');
            
            // Get current URL parameters
            const url = new URL(window.location.href);
            const params = new URLSearchParams(url.search);
            
            // Update page parameter
            params.set('page', page);
            params.set('posts_per_page', 3); // Match your posts_per_page value
            
            // Make API request
            fetch(`${wpApiSettings.root}prm/v1/tbyte_prm_events?${params.toString()}`)
                .then(response => response.json())
                .then(data => {
                    if (data.items && data.items.length > 0) {
                        // Generate HTML from the JSON data
                        const html = generateEventHTML(data.items);
                        eventList.innerHTML = html;
                        
                        // Update pagination
                        updatePagination(data.pagination);
                        
                        // Scroll to top smoothly
                        window.scrollTo({
                            top: eventList.offsetTop - 20,
                            behavior: 'smooth'
                        });
                    } else {
                        eventList.innerHTML = '<p class="col-span-full text-center py-8">No events found</p>';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    eventList.innerHTML = '<p class="col-span-full text-center py-8 text-red-500">Error loading events</p>';
                })
                .finally(() => {
                    loadingSpinner.classList.add('hidden');
                });
        }
        
        // Generate HTML for events from JSON data
        function generateEventHTML(events) {
            return events.map(event => {
                const date = event.date ? formatDate(event.date) : '';
                const eventType = event.type && event.type.length > 0 ? event.type.join(', ') : '';
                const eventImage = event.image || '';
                const eventDescription = event.description || '';
                
                return `
                    <div class="flex flex-col sm:flex-row bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden">
                        <div class="sm:w-1/3 bg-gray-200 dark:bg-gray-700 h-32 sm:h-auto">
                            ${!eventImage ? `
                                <div class="w-full h-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                                    <!-- Your not-found image placeholder would go here -->
                                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            ` : `
                                <div class="w-full h-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                                    <img src="${eventImage}" alt="Event" class="w-full h-full object-cover">
                                </div>
                            `}
                        </div>
                        <div class="p-4 flex flex-col justify-between flex-grow">
                            <div>
                                <div class="text-sm text-gray-500 dark:text-gray-400 mb-1">${date} – ${eventType}</div>
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">
                                    ${event.title}
                                </h3>
                                <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">${eventDescription}</p>
                            </div>
                            <div class="mt-3 flex justify-between items-center">
                                <a href="${event.link}" class="text-blue-600 dark:text-blue-400 hover:underline text-sm">View Details</a>
                                <ion-icon name="calendar-outline" class="text-gray-400 dark:text-gray-500 text-xl"></ion-icon>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
        }
        
        // Format date from YYYY-MM-DD to Month Day, Year
        function formatDate(dateString) {
            const options = { year: 'numeric', month: 'long', day: 'numeric' };
            return new Date(dateString).toLocaleDateString('en-US', options);
        }
        
        // Update pagination based on API response
        function updatePagination(pagination) {
            console.error('Updating Pagination. Page: ', pagination);
            if (!pagination || pagination.total_pages <= 1) {
                pagination.innerHTML = '';
                return;
            }
            
            const currentPage = pagination.current_page;
            const totalPages = pagination.total_pages;

            console.error('Current Page: ', currentPage);
            console.error('Total Pages: ', totalPages);
            
            let html = '<nav class="flex items-center space-x-2">';
            
            // Previous button
            if (currentPage > 1) {
                html += `<a href="#" class="px-4 py-2 border rounded-lg hover:bg-blue-600 hover:text-white transition-colors duration-200 flex items-center" data-page="${currentPage - 1}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Previous
                </a>`;
            }
            
            // Page numbers
            const range = 2;
            const showItems = (range * 2) + 1;
            
            console.error('Current Page2: ', currentPage);
            for (let i = 1; i <= totalPages; i++) {
                if (1 != totalPages && (!(i >= currentPage + range + 1 || i <= currentPage - range - 1) || totalPages <= showItems)) {
                    if (currentPage == i) {
                        html += `<span class="px-4 py-2 bg-blue-600 text-white rounded-lg">${i}</span>`;
                    } else {
                        html += `<a href="#" class="px-4 py-2 border rounded-lg hover:bg-blue-600 hover:text-white transition-colors duration-200" data-page="${i}">${i}</a>`;
                    }
                }
            }
            
            // Next button
            if (currentPage < totalPages) {
                html += `<a href="#" class="px-4 py-2 border rounded-lg hover:bg-blue-600 hover:text-white transition-colors duration-200 flex items-center" data-page="${currentPage + 1}">
                    Next
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>`;
            }
            
            html += '</nav>';
            pagination.innerHTML = html;
        }
        
        // Add history pushState for AJAX navigation
        window.addEventListener('popstate', function() {
            const params = new URLSearchParams(window.location.search);
            const page = params.get('page') || 1;
            loadEvents(page);
        });
    });
</script>