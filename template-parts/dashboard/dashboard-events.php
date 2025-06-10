<div class="events-container p-4  text-gray-800 ">
    <!-- Filters -->
    <div class="flex justify-between items-center mb-4">
        <!-- Search Bar -->
        <div class="flex items-center gap-4 relative">
            <div class="max-w-md mx-auto">
                <label for="event-search" class="block text-sm font-medium text-gray-700 mb-1">Search Events</label>
                <div class="relative rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                    </svg>
                    </div>
                    <input 
                    type="text" 
                    id="event-search" 
                    placeholder="Search events..." 
                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out sm:text-sm" 
                    />
                </div>
            </div>
            
            <!-- Loading spinner (hidden) -->
            <div id="loadingSpinnerSearch" class="absolute left-full ml-2 hidden">
                <svg class="animate-spin h-4 w-4 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
        </div>
  
        <!-- Date Range -->
        <div>
            <label for="date-range" class="block text-sm font-medium text-gray-700 mb-1">Date Range</label>
            <div class="relative rounded-md shadow-sm">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                </svg>
            </div>
            <input 
                type="text" 
                id="date-range" 
                placeholder="Filter by date range" 
                class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out sm:text-sm" 
            />
            </div>
        </div>

        <!-- Calendar View Button -->
        <div>
            <label for="calendar-toggle" class="block text-sm font-medium text-gray-700 mb-1">List View</label>
            <button id="calendar-toggle" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out">
                <ion-icon name="calendar-outline" class="h-5 w-5 text-gray-500"></ion-icon>
                <span class="ml-2">Calendar View</span>
            </button>
        </div>
    </div>

    <div class="flex flex-wrap gap-3 mb-6" id="event-filters">
        <!-- Checkbox Filters -->
        <?php
        $terms = get_terms(['taxonomy' => 'event_type', 'hide_empty' => false]);
        foreach ($terms as $term) {
            echo "<label class='inline-flex items-center px-3 py-2 rounded-md bg-gray-50 border border-gray-200 hover:bg-gray-100 hover:border-gray-300 cursor-pointer transition-colors duration-150'>
                    <input type='checkbox' value='{$term->slug}' class='h-4 w-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500 mr-2 event-filter' data-tax='event_type'>
                    <span class='text-sm font-medium text-gray-700'>{$term->name}</span>
                    </label>";
        }
        ?>
    </div>

    <!-- Upcoming Featured Event -->
    <div class="relative w-full rounded-2xl overflow-hidden bg-gray-100 mb-8 shadow-lg hover:shadow-xl transition-shadow duration-300 group">
        <div class="absolute inset-0 bg-gradient-to-br from-blue-600/80 to-indigo-800/80 opacity-90"></div>
        <img src="<?php echo get_template_directory_uri() . '/events/images/events-bg.jpg'; ?>" alt="Partner Growth Summit" class="w-full h-72 md:h-80 object-cover opacity-90 group-hover:opacity-100 transition-opacity duration-500" />
        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/30 to-transparent flex flex-col justify-end p-6 md:p-8 text-white">
            <div class="max-w-2xl">
                <span class="inline-block px-3 py-1 mb-3 text-xs font-semibold tracking-wider text-blue-100 bg-blue-900/50 rounded-full backdrop-blur-sm">
                    FEATURED EVENT
                </span>
                <h2 class="text-3xl md:text-4xl font-bold leading-tight mb-2">Partner Growth Summit 2025</h2>
                <div class="flex items-center gap-3 text-sm mb-4">
                    <span class="flex items-center gap-1.5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        April 30 - May 2, 2025
                    </span>
                    <span class="flex items-center gap-1.5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Marina Bay Sands, Singapore
                    </span>
                </div>
                <a href="#" class="inline-flex items-center px-4 py-2 bg-white/10 hover:bg-white/20 backdrop-blur-sm rounded-lg border border-white/20 transition-all duration-300 group-hover:border-white/40 text-white">
                    <span class="text-sm font-semibold">Join Us</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="ml-2 h-4 w-4 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </a>
            </div>
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
<script src="<?php echo get_template_directory_uri(); ?>/events/js/events-filter.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const eventList = document.getElementById('eventList');
        const paginationEl = document.getElementById('event-pagination');
        const loadingSpinner = document.getElementById('loadingSpinner');
        
        // Generate HTML for events from JSON data
        function generateEventHTML(events) {
            return events.map(event => {
                const date = event.date ? formatDate(event.date) : '';
                const eventType = event.type && event.type.length > 0 ? event.type.join(', ') : '';
                const eventImage = event.image || '';
                const eventDescription = event.description || '';
                
                return `
                    <div class="flex flex-col sm:flex-row bg-white  border border-gray-200  rounded-xl overflow-hidden">
                        <div class="sm:w-1/3 bg-gray-200  h-32 sm:h-auto">
                            ${!eventImage ? `
                                <div class="w-full h-full bg-gray-100  flex items-center justify-center">
                                    <!-- Your not-found image placeholder would go here -->
                                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            ` : `
                                <div class="w-full h-full bg-gray-100  flex items-center justify-center">
                                    <img src="${eventImage}" alt="Event" class="w-full h-full object-cover">
                                </div>
                            `}
                        </div>
                        <div class="p-4 flex flex-col justify-between flex-grow">
                            <div>
                                <div class="text-sm text-gray-500  mb-1">${date} – ${eventType}</div>
                                <h3 class="text-lg font-semibold text-gray-800 ">
                                    ${event.title}
                                </h3>
                                <p class="text-sm text-gray-600  mt-1">${eventDescription}</p>
                            </div>
                            <div class="mt-3 flex justify-between items-center">
                                <a href="${event.link}" class="text-blue-600  hover:underline text-sm">View Details</a>
                                <ion-icon name="calendar-outline" class="text-gray-400  text-xl"></ion-icon>
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

        const pagination = document.getElementById('event-pagination');
        pagination.addEventListener('click', (e) => {
            console.error('Pagination clicked:', e.target);
            console.error(!e.target.classList.contains('cursor-not-allowed'));
            if (e.target.tagName === 'A' && !e.target.classList.contains('cursor-not-allowed')) {
                console.error('Pagination link clicked:', e.target);
                e.preventDefault();
                const page = parseInt(e.target.dataset.page, 10);

                // Add loading state
                let originalText = e.target.innerHTML;
                e.target.innerHTML = '<span class="animate-pulse">Loading...</span>';
                e.target.classList.add('cursor-not-allowed', 'opacity-50');

                fetchAndRenderEvents(page).then(data => {
                    console.error('Events fetched successfully - Pagination clicked');
                    e.target.innerHTML = originalText;
                    e.target.classList.remove('cursor-not-allowed', 'opacity-50');
                    currentPage = page;
                    renderPagination3(data.pagination);
                })
                .catch(error => {
                    console.error('Error in fetchAndRenderEvents:', error);
                });
            }
        });

        function renderPagination3(pagination) {
            const paginationContainer = document.getElementById('event-pagination');
            paginationContainer.innerHTML = '';

            // Add smooth transition class to container
            paginationContainer.className += ' space-x-2 transition-all duration-300';

            // Previous button (always visible but disabled when on first page)
            const prevLink = document.createElement('a');
            prevLink.href = '#';
            prevLink.dataset.page = pagination.current_page - 1;
            prevLink.className = `px-4 py-2 rounded-md transition-all duration-300 ${
                pagination.current_page === 1 
                    ? 'bg-gray-200 text-gray-500 cursor-not-allowed' 
                    : 'bg-white text-[#086ad7] hover:bg-blue-50 border border-blue-200 cursor-pointer'
            }`;
            prevLink.innerHTML = '&larr; Previous';
            prevLink.addEventListener('click', (e) => {
                if (pagination.current_page === 1) e.preventDefault();
            });
            paginationContainer.appendChild(prevLink);

            // Page numbers
            for (let i = 1; i <= pagination.total_pages; i++) {
                let pageLink;
                if (i === pagination.current_page) {
                    pageLink = document.createElement('span');
                    pageLink.className = 'px-4 py-2 bg-blue-500 text-white rounded-md transition-all duration-300';
                } else {
                    pageLink = document.createElement('a');
                    pageLink.className = 'px-4 py-2 bg-white text-blue-600 hover:bg-blue-50 border border-blue-200 cursor-pointer rounded-md transition-all duration-300';
                    pageLink.href = '#';
                }
                pageLink.dataset.page = i;
                pageLink.textContent = i;
                paginationContainer.appendChild(pageLink);
            }

            // Next button (always visible but disabled when on last page)
            const nextLink = document.createElement('a');
            nextLink.href = '#';
            nextLink.dataset.page = pagination.current_page + 1;
            nextLink.className = `px-4 py-2 rounded-md transition-all duration-300 ${
                pagination.current_page === pagination.total_pages 
                    ? 'bg-gray-200 text-gray-500 cursor-not-allowed' 
                    : 'bg-white text-blue-600 hover:bg-blue-50 border border-blue-200 cursor-pointer'
            }`;
            nextLink.innerHTML = 'Next &rarr;';
            nextLink.addEventListener('click', (e) => {
                console.error("Clicked next button")
                if (pagination.current_page === pagination.total_pages) e.preventDefault();
            });
            paginationContainer.appendChild(nextLink);

            console.error(pagination.current_page, pagination.total_pages)

            // Add fade-in animation to the container
            paginationContainer.style.opacity = '0';
            setTimeout(() => {
                paginationContainer.style.opacity = '1';
                paginationContainer.style.transition = 'opacity 300ms ease-in-out';
            }, 10);
        }
        
        // Update pagination based on API response
        function updatePagination(pagination) {
            if (!pagination || pagination.total_pages <= 1) {
                pagination.innerHTML = '';
                return;
            }
            
            const currentPage = pagination.current_page;
            const totalPages = pagination.total_pages;
            
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
            paginationEl.innerHTML = html;
        }

        

        
        let currentPage = 1;
        let totalPages = 1;

        async function fetchAndRenderEvents(page = 1, perPage = 5, searchParams = {}) {
            let events;

            // Get current URL parameters
            // const url = new URL(window.location.href);
            // const params = new URLSearchParams(url.search);
            
            // Update page parameter
            // params.set('page', page);
            // params.set('posts_per_page', 3); // Match your posts_per_page value
            
            // Validate page number
            pageNumber = Math.max(1, Math.min(page, totalPages));
            currentPage = pageNumber;

            // const tbody = document.getElementById('events-table-body2');
            const tbody = eventList;
            // const eventList = document.getElementById('events-list');
            // Add fade-out animation
            tbody.style.opacity = '0.5';
            tbody.style.transition = 'opacity 300ms ease-in-out';

            // Helper function to create a table cell
            const createTableCell = (content, className = 'p-2') => {
                const td = document.createElement('td');
                td.textContent = content;
                td.className = className;
                return td;
            };
            
            // console.error(perPage, page, searchParams);

            // Build query string
            const query = new URLSearchParams({
                page: page,
                posts_per_page: perPage,
                // ...searchParams // Include any filters/search terms
            }).toString();

            showLoading('eventList'); // Show loading skeleton

            console.error(query);

            try {
                const url = `${wpApiSettings.root}prm/v1/tbyte_prm_events?${query}`;

                const response = await fetch(url);

                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }

                const data = await response.json();

                // Assuming the API returns an object with a 'data' property
                events = JSON.parse(data.items);

                // Clear existing rows
                tbody.innerHTML = '';

                // Render each event
                events.forEach(event => {
                    const row = document.createElement('div');
                    row.className = 'flex flex-col sm:flex-row bg-white border border-gray-200 rounded-xl overflow-hidden';
                    row.innerHTML = `
                            <div class="sm:w-1/3 bg-gray-200 h-32 sm:h-auto">
                                ${!event.image ? `
                                    <div class="w-full h-full bg-gray-100 flex items-center justify-center">
                                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                ` : `
                                    <div class="w-full h-full bg-gray-100 flex items-center justify-center">
                                        <img src="${event.image}" alt="Event" class="w-full h-full object-cover">
                                    </div>
                                `}
                            </div>
                            <div class="p-4 flex flex-col justify-between flex-grow">
                                <div>
                                    <div class="text-sm text-gray-500 mb-1">${event.date} – ${event.type ? event.type.join(', ') : ''}</div>
                                    <h3 class="text-lg font-semibold text-gray-800">${event.title}</h3>
                                    ${event.tags ? `
                                        <div class="mt-3 flex flex-wrap gap-2">
                                            ${event.tags.map(tag => `
                                                <span class="text-xs px-2 py-1 bg-gray-100 rounded-full">${tag}</span>
                                            `).join('')}
                                        </div>
                                    ` : ''}
                                </div>
                                <div class="mt-3 flex justify-between items-center">
                                    <a href="#" data-event-preview data-event-id="${event.id}" class="text-blue-600 hover:underline text-sm">View Details</a>
                                    <ion-icon name="calendar-outline" class="text-gray-400 text-xl"></ion-icon>
                                </div>
                            </div>
                    `;
                    tbody.appendChild(row);
                });

                // Fade in the new content
                setTimeout(() => {
                    tbody.style.opacity = '1';
                }, 300);

                // Scroll to top smoothly
                window.scrollTo({
                    top: eventList.offsetTop - 20,
                    behavior: 'smooth'
                });

                // updateQueryParam('page', page);
                // return data;
                return data;
            } catch (error) {
                tbody.style.opacity = '1'; // Reset opacity on error
                throw error;
            }
        }
        
        function showLoading(elementId) {
            const element = document.getElementById(elementId);
            const skeletonCard = `
                <div class="border border-gray-200 rounded-xl overflow-hidden animate-pulse">
                    <div class="bg-gray-300 h-40"></div>
                    <div class="p-4 space-y-3">
                        <div class="h-4 bg-gray-300 rounded w-3/4"></div>
                        <div class="h-5 bg-gray-300 rounded"></div>
                        <div class="flex gap-2">
                            <span class="bg-gray-300 rounded-full w-12 h-6"></span>
                            <span class="bg-gray-300 rounded-full w-12 h-6"></span>
                        </div>
                        <div class="flex justify-between">
                            <div class="h-4 bg-gray-300 rounded w-20"></div>
                            <div class="w-5 h-5 bg-gray-300 rounded"></div>
                        </div>
                    </div>
                </div>
            `;
            
            // Create 3 skeleton cards in a grid
            element.innerHTML = `
                ${skeletonCard}
                ${skeletonCard}
                ${skeletonCard}
            `;
        }

        function hideLoading(elementId) {
            const element = document.getElementById(elementId);
            element.innerHTML = '';
        }
        
        // Add history pushState for AJAX navigation
        // window.addEventListener('popstate', function() {
        //     const params = new URLSearchParams(window.location.search);
        //     const page = params.get('page') || 1;
        //     loadEvents(page);
        // });

        // Initial load
        fetchAndRenderEvents()
            .then(data  => {
                if (data.pagination) {
                    renderPagination3(data.pagination);
                }
            })
            .catch(error => {
                showError('Error loading events.');
            });
    });
</script>


<script>
    class EventPreview {
        constructor() {
            this.modal = null;
            this.initEventListeners();
        }

        initEventListeners() {
            // Delegate click events for event preview links
            document.addEventListener('click', (e) => {
            const previewLink = e.target.closest('[data-event-preview]');
            if (previewLink) {
                e.preventDefault();
                this.open(previewLink.dataset.eventId);
            }

            // Close modal when clicking close button or backdrop
            if (e.target.classList.contains('event-preview-backdrop')) {
                this.close();
            }
            if (e.target.classList.contains('event-preview-close')) {
                this.close();
            }
            });
        }

        async open(eventId) {
            // Create modal if it doesn't exist
            if (!this.modal) {
            this.createModal();
            }

            // Show loading state
            this.modal.querySelector('.event-preview-content').innerHTML = `
            <div class="flex justify-center items-center h-64">
                <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-500"></div>
            </div>
            `;

            // Show modal
            this.modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';

            try {
                // Fetch event data
                const response = await fetch(`${wpApiSettings.root}prm/v1/tbyte_prm_events/${eventId}`);
                if (!response.ok) throw new Error('Event not found');
                
                const event = await response.json();
                console.error("event: ", event)
                this.renderEvent(event);
                } catch (error) {
                console.error('Error loading event:', error);
                this.showError(error);
            }
        }

        createModal() {
            this.modal = document.createElement('div');
            this.modal.className = 'fixed inset-0 z-50 hidden';
            this.modal.innerHTML = `
                <div class="event-preview-backdrop absolute inset-0 bg-black/50 cursor-pointer"></div>
                <div class="absolute inset-0 flex items-center justify-center p-4 pointer-events-none">
                <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl max-h-[90vh] overflow-hidden flex flex-col pointer-events-auto">
                    <button class="event-preview-close absolute top-4 right-4 z-10 text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    </button>
                    <div class="event-preview-content overflow-y-auto flex-1"></div>
                </div>
                </div>
            `;
            document.body.appendChild(this.modal);
            
            // Add click handler for backdrop
            this.modal.querySelector('.event-preview-backdrop').addEventListener('click', () => this.close());
        }

        renderEvent(event) {
            console.error(event)
            const content = this.modal.querySelector('.event-preview-content');

            const formattedStartDate = new Date(event.start_date).toLocaleDateString();
            const formattedEndDate = new Date(event.end_date).toLocaleDateString();
            const formattedTime = event.start_time && event.end_time
                ? `${event.start_time} - ${event.end_time}`
                : event.start_time || event.end_time || '';

            const tagsHTML = event.tags.map(tag =>
                `<span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">${tag}</span>`
            ).join('');

            const costHTML = event.cost
                ? `${event.currency_position === 'before' ? event.currency_symbol : ''}${event.cost}${event.currency_position === 'after' ? ' ' + event.currency_symbol : ''}`
                : 'Free';

            content.innerHTML = `
            <div class="p-6">
                <!-- Header -->
                <div class="flex flex-col md:flex-row justify-between md:items-center mb-6 border-b pb-4 border-gray-200">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">${event.title}</h1>
                        <p class="text-sm text-gray-500 mt-1">${event.location || ''}</p>
                    </div>
                    ${event.is_featured ? `<span class="mt-4 md:mt-0 inline-block bg-yellow-100 text-yellow-800 text-xs font-semibold px-3 py-1 rounded-full">★ Featured</span>` : ''}
                </div>

                <!-- Image -->
                ${event.image ? `
                <div class="mb-6">
                    <img src="${event.image}" alt="${event.title}" class="w-full h-64 object-cover rounded-lg shadow-sm">
                </div>
                ` : ''}

                <!-- Meta info -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm text-gray-700 mb-6">
                    <div>
                        <p class="text-gray-500 mb-1">Start Date</p>
                        <p>${formattedStartDate}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 mb-1">End Date</p>
                        <p>${formattedEndDate}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 mb-1">Time</p>
                        <p>${formattedTime}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 mb-1">Cost</p>
                        <p class="font-medium">${costHTML}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 mb-1">Venue</p>
                        <p>${event.venue || '—'}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 mb-1">Status</p>
                        <p class="${event.event_status === 'cancelled' ? 'text-red-600 font-semibold' : 'text-green-600'}">
                            ${event.event_status.charAt(0).toUpperCase() + event.event_status.slice(1)}
                        </p>
                    </div>
                </div>

                <!-- Tags -->
                ${tagsHTML ? `
                <div class="mb-6">
                    <p class="text-gray-500 text-sm mb-2">Tags</p>
                    <div class="flex flex-wrap gap-2">${tagsHTML}</div>
                </div>
                ` : ''}

                <!-- Map -->
                ${event.show_map_link && event.location ? `
                <div class="mb-6">
                    <a href="https://www.google.com/maps/search/?q=${encodeURIComponent(event.location)}"
                    target="_blank" rel="noopener"
                    class="text-sm text-blue-600 hover:underline flex items-center gap-1">
                        📍 View on Google Maps
                    </a>
                </div>
                ` : ''}

                <!-- Footer -->
                <footer class="pt-4 mt-6 border-t border-gray-200 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div class="flex gap-3">
                        <button class="event-share-btn inline-flex items-center px-4 py-2 border border-gray-300 text-sm rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50">
                            Share
                        </button>
                        ${event.event_url ? `
                        <a href="${event.event_url}" target="_blank" rel="noopener"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50">
                            Event Website
                        </a>` : ''}
                    </div>
                    <a href="${window.location.origin}/events/${event.id}"
                    class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        View Full Page
                    </a>
                </footer>
            </div>
            `;

            // Initialize share button
            const shareBtn = content.querySelector('.event-share-btn');
            if (shareBtn) {
                shareBtn.addEventListener('click', () => this.handleShare(event));
            }
        }


        handleShare(event) {
            if (navigator.share) {
            navigator.share({
                title: event.title,
                text: event.description || '',
                url: `${window.location.origin}/events/${event.id}`
            }).catch(err => {
                console.log('Error sharing:', err);
            });
            } else {
            // Fallback for browsers without Web Share API
            const tempInput = document.createElement('input');
            tempInput.value = `${window.location.origin}/events/${event.id}`;
            document.body.appendChild(tempInput);
            tempInput.select();
            document.execCommand('copy');
            document.body.removeChild(tempInput);
            
            alert('Link copied to clipboard!');
            }
        }

        close() {
            if (this.modal) {
            this.modal.classList.add('hidden');
            document.body.style.overflow = '';
            }
        }
    }

    // Initialize when DOM is ready
    document.addEventListener('DOMContentLoaded', () => {
    window.eventPreview = new EventPreview();
    });
</script>