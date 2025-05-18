document.addEventListener('DOMContentLoaded', () => {
    const datePicker = flatpickr("#date-range", {
        mode: "range",
        dateFormat: "Y-m-d",
        defaultDate: [new Date(), new Date(new Date().setDate(new Date().getDate() + 7))],
        onChange: debounce((selectedDates) => {
            const startDate = selectedDates[0];
            const endDate = selectedDates[1];
            if (startDate && endDate) {
                document.getElementById('date-range').value = `${startDate.toISOString().split('T')[0]} to ${endDate.toISOString().split('T')[0]}`;
                fetchEvents();
            }
        }, 500),
        onReady: (selectedDates, dateStr) => {
            const today = new Date();
            const nextWeek = new Date(today);
            nextWeek.setDate(today.getDate() + 7);
            document.getElementById('date-range').value = `${today.toISOString().split('T')[0]} to ${nextWeek.toISOString().split('T')[0]}`;
        }
    });

    document.getElementById('event-search').addEventListener('input', debounce(fetchEvents, 300));
    document.querySelectorAll('.event-filter').forEach(cb => cb.addEventListener('change', debounce(fetchEvents, 200)));
    document.getElementById('event-search').addEventListener('keydown', (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            fetchEvents();
        }
    });
    document.getElementById('event-search').addEventListener('blur', () => {
        if (document.getElementById('event-search').value === '') {
            document.getElementById('event-search').value = '';
        }
    });

    document.getElementById('calendar-toggle').addEventListener('click', function(e) {
        e.preventDefault();
        this.classList.toggle('active');
        this.classList.toggle('bg-blue-600');
        this.classList.toggle('text-white');
        this.classList.toggle('text-gray-800');
        this.classList.toggle('bg-gray-200');
        toggleCalendarView();
        const isCalendarView = document.getElementById('eventList').classList.contains('calendar-view');
        this.querySelector('span').textContent = isCalendarView ? 'List View' : 'Calendar View';
        this.querySelector('ion-icon').setAttribute('name', isCalendarView ? 'list-outline' : 'calendar-outline');
    });

    let currentPage = 1;
    let isLoading = false;

    async function fetchEvents() {
        if (isLoading) return;
        isLoading = true;
        showSearchLoadingIndicator();
        // const loadingSpinner = document.getElementById('loadingSpinner');
        // loadingSpinner.classList.remove('hidden');

        const search = document.getElementById('event-search').value;
        const range = datePicker.selectedDates.length > 0 ? datePicker.selectedDates.map(date => date.toISOString().split('T')[0]) : null;

        const filters = [...document.querySelectorAll('.event-filter:checked')].map(cb => ({
            tax: cb.dataset.tax,
            value: cb.value
        }));

        try {
            const response = await fetch(`${wpApiSettings.root}prm/v1/tbyte_prm_events`, {
                method: "POST",
                headers: { 
                    'Content-Type': 'application/json',
                    'X-WP-Nonce': wpApiSettings.nonce,
                },
                body: JSON.stringify({ 
                    search, 
                    range, 
                    filters, 
                    page: currentPage 
                })
            });

            if (!response.ok) {
                throw new Error('Network response was not ok');
            }

            const data = await response.json();
            updateEventList(data);
        } catch (error) {
            console.error('Error fetching events:', error);
            showError('Error fetching events. Please try again later.');
        } finally {
            isLoading = false;
            hideSearchLoadingIndicator();
        }
    }

    function updateEventList(data) {
        const eventList = document.getElementById('eventList');
        
        if (data.items.length === 0) {
            eventList.innerHTML = '<div class="no-events">No events found matching your criteria.</div>';
            return;
        }
        
        // Build HTML from the data (you might want to use a template)
        let html = '';
        data.items.forEach(event => {
            console.error(event); // Debugging line to check the event data
            const id = event.id || '';
            const title = event.title || 'No Title';
            const date = event.date ? formatDate(event.date) : '';
            const eventType = event.type && event.type.length > 0 ? event.type.join(', ') : '';
            const description = event.excerpt || '';
            const image = event.thumbnail || '';
            const location = event.venue || 'Location not specified';
            const link = event.link || '#';

            html += `
                <div class="flex flex-col sm:flex-row bg-white  border border-gray-200  rounded-xl overflow-hidden" data-id="${id}">
                    <div class="sm:w-1/3 bg-gray-200  h-32 sm:h-auto">
                        ${!event.image ? `
                            <div class="w-full h-full bg-gray-100  flex items-center justify-center">
                                <!-- Your not-found image placeholder would go here -->
                                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        ` : `
                            <div class="w-full h-full bg-gray-100  flex items-center justify-center">
                                <img src="${event.image}" alt="Event" class="w-full h-full object-cover">
                            </div>
                        `}
                    </div>
                    <div class="p-4 flex flex-col justify-between flex-grow">
                        <div>
                            <div class="text-sm text-gray-500  mb-1">${formatDate(date)} – ${eventType}</div>
                            <h3 class="text-lg font-semibold text-gray-800 ">
                                ${title}
                            </h3>
                            <small>${location}</small>
                        </div>
                        <div class="mt-3 flex justify-between items-center">
                            <a href="${link}" class="text-blue-600  hover:underline text-sm">View Details</a>
                            <ion-icon name="calendar-outline" class="text-gray-400  text-xl"></ion-icon>
                        </div>
                    </div>
                </div>
            `;
        });
        
        eventList.innerHTML = html;
        
        // Update pagination if needed
        if (data.pagination && data.pagination.total_pages > 1) {
            updatePaginationControls(data.pagination);
        }
    }

    function toggleCalendarView() {
        const eventList = document.getElementById('eventList');
        eventList.classList.toggle('calendar-view');

        if (eventList.classList.contains('calendar-view')) {
            eventList.classList.remove('grid');
            eventList.classList.add('calendar-grid');
            fetchCalendarViewEvents();
        } else {
            eventList.classList.remove('calendar-grid');
            eventList.classList.add('grid');
        }
    }

    function fetchCalendarViewEvents() {
        // Fetch events in calendar view format
        // This is a placeholder; implement your logic to fetch and display events in calendar view
        console.log('Fetching events for calendar view...');
    }

    function updatePaginationControls(pagination) {
        const paginationContainer = document.getElementById('event-pagination');
        paginationContainer.innerHTML = '';

        if (pagination.current_page > 1) {
            const prevLink = document.createElement('a');
            prevLink.href = '#';
            prevLink.className = 'pagination-link';
            prevLink.textContent = 'Previous';
            prevLink.dataset.page = pagination.current_page - 1;
            paginationContainer.appendChild(prevLink);
        }

        for (let i = 1; i <= pagination.total_pages; i++) {
            const pageLink = document.createElement('a');
            pageLink.href = '#';
            pageLink.className = 'pagination-link';
            pageLink.textContent = i;
            pageLink.dataset.page = i;
            if (i === pagination.current_page) {
                pageLink.classList.add('active');
            }
            paginationContainer.appendChild(pageLink);
        }

        if (pagination.current_page < pagination.total_pages) {
            const nextLink = document.createElement('a');
            nextLink.href = '#';
            nextLink.className = 'pagination-link';
            nextLink.textContent = 'Next';
            nextLink.dataset.page = pagination.current_page + 1;
            paginationContainer.appendChild(nextLink);
        }
    }

    // Utility functions
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    function formatDate(dateString) {
        const options = { year: 'numeric', month: 'long', day: 'numeric' };
        return new Date(dateString).toLocaleDateString('en-US', options);
    }

    function showSearchLoadingIndicator() {
        const loadingSpinnerSearch = document.getElementById('loadingSpinnerSearch');
        loadingSpinnerSearch.classList.remove('hidden');
    }

    function hideSearchLoadingIndicator() {
        const loadingSpinnerSearch = document.getElementById('loadingSpinnerSearch');
        loadingSpinnerSearch.classList.add('hidden');
    }
});