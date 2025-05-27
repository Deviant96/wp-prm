<?php
$layout = $_GET['layout'] ?? 'grid';
$search = sanitize_text_field($_GET['s'] ?? '');
$doc_type = isset($_GET['doc_type']) ? explode(',', sanitize_text_field($_GET['doc_type'])) : [];
$language = isset($_GET['language']) ? explode(',', sanitize_text_field($_GET['language'])) : [];
// $paged = max(1, get_query_var('paged') ?: get_query_var('page'));
// $posts_per_page = 6;

// $args = [
//     'post_type' => 'assets',
//     's' => $search,
//     'posts_per_page' => $posts_per_page,
//     'paged' => $paged,
//     'tax_query' => [],
// ];

// if ( isset( $_GET['doc_type'] ) && ! empty( $_GET['doc_type'] ) ) {
//     $args['tax_query'][] = array(
//         'taxonomy' => 'doc_type',
//         'field'    => 'id',
//         'terms'    => $_GET['doc_type'],
//         'operator' => 'IN',
//     );
// }

// // Filter by Language
// if ( isset( $_GET['language'] ) && ! empty( $_GET['language'] ) ) {
//     $args['tax_query'][] = array(
//         'taxonomy' => 'language',
//         'field'    => 'id',
//         'terms'    => $_GET['language'],
//         'operator' => 'IN',
//     );
// }

// $query = new WP_Query($args);
?>

<div class="space-y-4" id="assets-page" data-layout="<?php echo esc_attr($layout); ?>">

    <!-- Controls -->
    <div class="flex flex-wrap justify-between items-center gap-4 mb-6">
        <input type="text"
            id="search-assets"
            placeholder="Search assets..."
            class="w-full sm:w-64 px-3 py-2 rounded border  "
            value="<?php echo esc_attr($search); ?>" />

        <div class="flex items-center gap-2">
            <button data-layout="grid"
                class="layout-toggle <?php echo $layout === 'grid' ? 'text-blue-500' : 'text-gray-500'; ?>"
                title="Grid View">
                <ion-icon name="grid-outline" class="text-2xl"></ion-icon>
            </button>
            <button data-layout="list"
                class="layout-toggle <?php echo $layout === 'list' ? 'text-blue-500' : 'text-gray-500'; ?>"
                title="List View">
                <ion-icon name="list-outline" class="text-2xl"></ion-icon>
            </button>
        </div>
    </div>


    <!-- Filters -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div>
            <h4 class="text-sm font-semibold text-gray-700 mb-2">Doc Type</h4>
            <div class="flex flex-wrap gap-2">
                <?php foreach (get_terms(['taxonomy' => 'doc_type']) as $term): ?>
                    <label class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium cursor-pointer transition-all
                                border <?php echo in_array($term->slug, (array)$doc_type) ? 
                                    'bg-blue-100 text-blue-800 border-blue-300 shadow-inner' : 
                                    'bg-white text-gray-700 border-gray-300 hover:bg-gray-50' ?>">
                        <input type="checkbox"
                            class="sr-only filter-doc"
                            value="<?php echo $term->slug; ?>"
                            <?php checked(in_array($term->slug, (array)$doc_type)); ?>>
                        <?php echo $term->name; ?>
                            <svg class="activeIcon hidden ml-1 h-3 w-3 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>
        <div id="language-filter">
            <h4 class="text-sm font-semibold text-gray-700 mb-3">Language</h4>
            <div class="flex flex-wrap gap-2">
                <?php foreach (get_terms(['taxonomy' => 'language', 'hide_empty' => false]) as $term): ?>
                    <label class="inline-flex items-center px-3 py-2 rounded-lg text-sm font-medium cursor-pointer transition-colors duration-150 border
                                <?php echo in_array($term->slug, (array)$language) ? 
                                    'bg-blue-50 text-blue-700 border-blue-200 shadow-inner' : 
                                    'bg-white text-gray-700 border-gray-200 hover:bg-gray-50' ?>">
                        <input type="checkbox"
                            class="absolute opacity-0 h-0 w-0 filter-lang"
                            value="<?php echo $term->slug; ?>"
                            <?php checked(in_array($term->slug, (array)$language)); ?>>
                        <span class="flex items-center">
                            <?php echo $term->name; ?>
                            <?php if(in_array($term->slug, (array)$language)): ?>
                                <svg class="activeIcon ml-1.5 h-3.5 w-3.5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            <?php endif; ?>
                        </span>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="flex items-center">
            <button id="reset-filters" class="inline-flex items-center gap-2 px-2 py-1 text-sm font-small text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-gray-900 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-200">
                <ion-icon name="refresh-outline" class="w-4 h-4"></ion-icon>
                Clear Filters
            </button>
        </div>
    </div>

    <!-- Loading State -->
    <div id="assets-loading" class="hidden">
        <div class="animate-pulse flex justify-center py-8">
            <div class="w-6 h-6 border-4 border-blue-500 rounded-full border-t-transparent animate-spin"></div>
        </div>
    </div>

    <!-- Placeholder for loading skeletons -->
    <div id="loadingElement" class="hidden">
    <div class="asset-results grid gap-4 grid-cols-2 md:grid-cols-3" id="skeletonContainer">
        <!-- Skeleton cards will be injected here -->
    </div>
    </div>

    <!-- Results -->
    <div id="assetList" class="asset-results grid gap-4 <?php echo $layout === 'list' ? 'grid-cols-1' : 'grid-cols-2 md:grid-cols-3'; ?>">
    </div>

    <!-- Pagination -->
    <div id="assets-pagination" class="mt-6 flex justify-center"></div>
</div>

<?php wp_reset_postdata(); ?>

<script>
    /**
     * Document Filter Checkbox Event Handler
     * 
     * This JavaScript code adds event listeners to document filter checkboxes that:
     * - Toggles visibility of an icon when checkbox state changes
     * - Updates label styling based on checkbox state:
     *   When checked:
     *   - Shows icon
     *   - Sets blue background/text colors
     *   - Updates border color to blue
     *   When unchecked:
     *   - Hides icon
     *   - Sets white background
     *   - Sets gray text and border colors
     * 
     * @selector .filter-doc
     * @event change
     * @affects
     * - Checkbox label background/text/border colors
     * - Icon visibility within label
     */
    document.querySelectorAll('.filter-doc').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const label = this.closest('label');
            const icon = this.closest('label').querySelector('.activeIcon');
            if (this.checked) {
                icon.classList.remove('hidden');
                label.classList.add('bg-blue-100', 'text-blue-800', 'border-blue-300');
                label.classList.remove('bg-white', 'text-gray-700', 'border-gray-300');
            } else {
                icon.classList.add('hidden');
                label.classList.remove('bg-blue-100', 'text-blue-800', 'border-blue-300');
                label.classList.add('bg-white', 'text-gray-700', 'border-gray-300');
            }
        });
    });
</script>

<script>
    const languageFilter = document.getElementById('language-filter');
    
    languageFilter.addEventListener('change', function(e) {
        if (e.target.classList.contains('filter-lang')) {
            const label = e.target.closest('label');
            const checkIcon = label.querySelector('svg');
            
            if (e.target.checked) {
                label.classList.add('bg-blue-50', 'text-blue-700', 'border-blue-200', 'shadow-inner');
                label.classList.remove('bg-white', 'text-gray-700', 'border-gray-200');
                if (!checkIcon) {
                    label.querySelector('span').innerHTML += `
                        <svg class="ml-1.5 h-3.5 w-3.5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>`;
                } else {
                    checkIcon.classList.remove('opacity-0');
                }
            } else {
                label.classList.remove('bg-blue-50', 'text-blue-700', 'border-blue-200', 'shadow-inner');
                label.classList.add('bg-white', 'text-gray-700', 'border-gray-200');
                if (checkIcon) checkIcon.remove();
            }
        }
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        let postsPerPage = 2; // Default items per page

        const assetPage = document.getElementById('assets-page');
        // const assetList = assetPage.querySelector('.asset-results');
        // const loadingElement = document.getElementById('assets-loading');
        const paginationElement = document.getElementById('assets-pagination');
        const searchInput = document.getElementById('search-assets');
        const layoutToggles = document.querySelectorAll('.layout-toggle');
        const filters = document.querySelectorAll('.filter-doc, .filter-lang');

        let currentPage = 1;
        let totalPages = 1;
        let debounceTimer;

        // This is for displaying loading skeletons
        const loadingElement = document.getElementById('loadingElement');
        const assetList = document.getElementById('assetList');
        const skeletonContainer = document.getElementById('skeletonContainer');

        function createSkeletonCard() {
            const card = document.createElement('div');
            card.className = "asset-card bg-white  rounded shadow p-4 relative animate-pulse";
            card.innerHTML = `
                <div class="h-5 bg-gray-300  rounded w-2/3 mb-2"></div>
                <div class="h-4 bg-gray-200  rounded w-1/3 mb-4"></div>

                <div class="mt-2 flex gap-2 flex-wrap">
                <div class="h-4 bg-gray-200  rounded w-1/4"></div>
                <div class="h-4 bg-gray-200  rounded w-1/4"></div>
                </div>

                <div class="mt-4 flex justify-end gap-2">
                <div class="h-6 w-6 bg-gray-300  rounded"></div>
                <div class="h-6 w-6 bg-gray-300  rounded"></div>
                </div>

                <div class="mt-3 text-sm space-y-2">
                <div class="h-4 bg-gray-200  rounded w-1/2"></div>
                <div class="h-4 bg-gray-200  rounded w-1/3"></div>
                </div>
            `;
            return card;
        }

        function showLoading() {
            // Clear any existing skeletons
            skeletonContainer.innerHTML = '';
            assetList.innerHTML = '';

            // Add 6 placeholder cards
            for (let i = 0; i < 6; i++) {
                skeletonContainer.appendChild(createSkeletonCard());
            }

            loadingElement.classList.remove('hidden');
            assetList.classList.add('opacity-50');
        }

        function hideLoading() {
            loadingElement.classList.add('hidden');
            assetList.classList.remove('opacity-50');
        }
        // End of placeholder loading state

        function collectFilters() {
            return {
                layout: assetPage.dataset.layout,
                search: searchInput.value,
                doc_type: [...document.querySelectorAll('.filter-doc:checked')]
                    .map(i => i.value)
                    .filter(Boolean), // Remove empty values
                language: [...document.querySelectorAll('.filter-lang:checked')]
                    .map(i => i.value)
                    .filter(Boolean),
                page: currentPage,
                posts_per_page: postsPerPage
            };
        }

        function updateURL(params) {
            const url = new URL(window.location.href);
            const preserveParams = ['tab']; // params to preserve
            const urlParams = new URLSearchParams(url.search);

            // Preserve specific parameters
            const preserved = {};
            preserveParams.forEach(param => {
                if (urlParams.has(param)) {
                    preserved[param] = urlParams.get(param);
                }
            });

            // Clear all existing parameters
            url.search = '';

            // Restore preserved parameters
            Object.entries(preserved).forEach(([key, value]) => {
                url.searchParams.set(key, value);
            });

            // Add new parameters
            Object.entries(params).forEach(([key, value]) => {
                if (Array.isArray(value) && value.length) {
                    // Handle arrays - join with commas instead of multiple parameters
                    url.searchParams.set(key, [...new Set(value)].join(','));
                } else if (value) {
                    url.searchParams.set(key, value);
                }
            });

            window.history.pushState({}, '', url);
        }

        function updateLayoutClasses(newLayout) {
            // Update layout classes in results container
            assetList.className = `asset-results grid gap-4 ${
                newLayout === 'list' ? 'grid-cols-1' : 'grid-cols-2 md:grid-cols-3'
            }`;
            
            // Update layout toggle buttons
            layoutToggles.forEach(btn => {
                btn.classList.toggle('text-blue-500', btn.dataset.layout === newLayout);
                btn.classList.toggle('text-gray-500', btn.dataset.layout !== newLayout);
            });
        }

        // utility function that makes it easy to add, update, or remove query parameters from the current URL without reloading the page
        function updateQueryParam(key, value) {
            const params = new URLSearchParams(window.location.search);

            if (value === null || value === undefined) {
                params.delete(key); // Remove the param if value is null/undefined
            } else {
                params.set(key, value); // Add or update the param
            }

            const newQuery = params.toString();
            const newUrl = `${window.location.pathname}${newQuery ? '?' + newQuery : ''}`;

            window.history.pushState({}, '', newUrl);
        }

        // Event Listeners
        layoutToggles.forEach(btn => {
            btn.addEventListener('click', () => {
                const currentLayout = assetPage.dataset.layout;
                const newLayout = btn.dataset.layout;
                if (currentLayout !== newLayout) {
                    assetPage.dataset.layout = newLayout;
                    updateLayoutClasses(newLayout);
                    loadAssets();
                }
            });
        });

        searchInput.addEventListener('input', () => {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                currentPage = 1;
                fetchAndRenderAssets()
                    .then(data  => {
                        if (data.pagination) {
                            renderPagination3(data.pagination);
                        }
                    })
                    .catch(error => {
                        showError('Error loading assets: ', error);
                    });
            }, 500);
        });

        filters.forEach(filter => {
            filter.addEventListener('change', () => {
                currentPage = 1;
                fetchAndRenderAssets()
                    .then(data  => {
                        if (data.pagination) {
                            renderPagination3(data.pagination);
                        }
                    })
                    .catch(error => {
                        showError('Error loading events: ', error);
                    });
            });
        });

        async function fetchAndRenderAssets(page = 1, perPage = postsPerPage, searchParams = {}) {
            showLoading();

            const urlParams = new URLSearchParams(window.location.search);
            const queryFromURL = Object.fromEntries(urlParams.entries());

            // currentPage = queryFromURL.page ? parseInt(queryFromURL.page, 10) : page;
            postsPerPage = queryFromURL.posts_per_page ? parseInt(queryFromURL.posts_per_page, 10) : perPage;
            
            const params = collectFilters();
            updateURL(params);

            // if (queryFromURL.page) {
            //     page = parseInt(params.page, 10);
            // } else {
            //     page = page;
            // }

            let assets = [];
            // Validate page number
            pageNumber = Math.max(1, Math.min(page, totalPages));
            currentPage = pageNumber;

            const assetList = document.getElementById('assetList');
            const tbody = assetList;
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

            // Build query method #2
            let queryParams = Object.entries(params)
                .filter(([_, value]) => value) // Remove empty values
                .map(([key, value]) => {
                    const paramValue = Array.isArray(value) ? value.join(',') : value;
                    return `${key}=${encodeURIComponent(paramValue)}`;
                })
                .join('&');

            showLoading('assets-table-body2', 5, 6); // Show loading state

            try {
                const url = `${wpApiSettings.root}prm/v1/tbyte_prm_assets?${queryParams}`;

                const response = await fetch(url, {
                    method: 'GET',
                    headers: {
                        // 'Content-Type': 'application/json',
                        'X-WP-Nonce': wpApiSettings.nonce
                    }
                });

                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }

                const data = await response.json();

                console.log('Assets Data:', data.items.length);

                // Clear existing rows
                tbody.innerHTML = '';

                if (data.items.length === 0) {
                    assetList.innerHTML = '<p class="text-gray-500 text-center col-span-2 md:col-span-3">' + data.message +'</p>';
                } else {
                    assets = data.items;

                    assets.forEach(asset => {
                        const row = document.createElement('div');
                        row.className = 'asset-card flex flex-col bg-white border border-gray-200 overflow-hidden p-4 rounded-xl shadow';
                        row.setAttribute('data-id', asset.id);
                        const { id, title, date, link, doc_type, content } = asset;

                        let docTypeThumb;
                        if (doc_type === "PDF") {
                            docTypeThumb = `<img src="${wpApiSettings.theme_path}/assets/images/pdf-icon.png" alt="PDF Icon" class="w-full h-full object-cover">`;
                        } else if (doc_type === "Image") {
                            // Get the image URL from the post content
                            docTypeThumb = `<img src="${content}" alt="Image Icon" class="w-full h-full object-cover">`;
                        } else if (doc_type === "URL") {
                            docTypeThumb = `<img src="${wpApiSettings.theme_path}/assets/images/link-icon.png" alt="Image Icon" class="w-full h-full object-cover">`;
                        } else {
                            docTypeThumb = `<div class="w-full h-full bg-gray-100 flex items-center justify-center">
                                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>`;
                        }

                        row.innerHTML = `
                            <?php if (has_post_thumbnail()): ?>
                                <img src="<?php the_post_thumbnail_url(); ?>" class="mb-3 rounded h-32 object-cover w-full" alt="">
                            <?php endif; ?>
                            <div class="w-full h-64 sm:h-auto">
                                ${docTypeThumb}
                            </div>
                            <div class="flex flex-col justify-between flex-grow">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-800 ">
                                        ${title}
                                    </h3>
                                    <small class="text-gray-500">${date}</small> – ${doc_type}
                                </div>
                                <div class="mt-4 flex justify-end gap-2">
                                    ${doc_type === "URL" ? `
                                        <a href="${link}" target="_blank" class="text-blue-600 hover:underline" onclick="copyAsset(event, this)">
                                            <ion-icon name="copy-outline"></ion-icon>
                                        </a>
                                    ` : doc_type === "Text" ? `
                                        <a href="${link}" target="_blank" class="text-blue-600 hover:underline" onclick="copyAsset(event, this)">
                                            <ion-icon name="copy-outline"></ion-icon>
                                        </a>
                                    ` : `
                                        <a href="${link}" download class="text-blue-600 hover:underline">
                                            <ion-icon name="download-outline"></ion-icon>
                                        </a>
                                    `}
                                    <a href="${link}" class="text-green-600 hover:underline">
                                        <ion-icon name="eye-outline"></ion-icon>
                                    </a>
                                </div>
                            </div>
                        `;
                        tbody.appendChild(row);
                    });
                }

                // Assuming the API returns an object with a 'data' property
                // const pagination = data.data.pagination;

                // Render the events
                // renderEvents(events);
                // renderPagination3(pagination);
                // });

                // Fade in the new content
                setTimeout(() => {
                    tbody.style.opacity = '1';
                }, 10);

                totalPages = data.pagination.total_pages;

                updateQueryParam('page', page);
                // return data;
                return data;
            } catch (error) {
                showError('Error loading assets. Please try again.');
                assetList.innerHTML = '<p class="text-red-500">' + error + '</p>';
                tbody.style.opacity = '1'; // Reset opacity on error
                return {
                    error: error.message
                }
            } finally {
                hideLoading();
            }
        }

        function renderPagination3(pagination) {
            const paginationContainer = document.getElementById('assets-pagination');
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
            if (pagination.total_pages <= 1) {
                const notFoundPageNumber = document.createElement('span');
                notFoundPageNumber.className = 'px-4 py-2 text-gray-500';
                notFoundPageNumber.textContent = 'N/A';
                paginationContainer.appendChild(notFoundPageNumber);
                // return;
            }
            if (pagination.total_pages > 5) {
                // Show only first 5 pages and last page
                const startPage = Math.max(1, pagination.current_page - 2);
                const endPage = Math.min(pagination.total_pages, pagination.current_page + 2);

                for (let i = startPage; i <= endPage; i++) {
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

                // Add ellipsis if there are more pages
                if (endPage < pagination.total_pages) {
                    const ellipsis = document.createElement('span');
                    ellipsis.className = 'px-4 py-2 text-gray-500';
                    ellipsis.textContent = '...';
                    paginationContainer.appendChild(ellipsis);

                    const lastPageLink = document.createElement('a');
                    lastPageLink.href = '#';
                    lastPageLink.dataset.page = pagination.total_pages;
                    lastPageLink.className = 'px-4 py-2 bg-white text-blue-600 hover:bg-blue-50 border border-blue-200 cursor-pointer rounded-md transition-all duration-300';
                    lastPageLink.textContent = pagination.total_pages;
                    paginationContainer.appendChild(lastPageLink);
                }
            } else {
                // Show all pages
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
            }

            // Next button (always visible but disabled when on last page)
            const nextLink = document.createElement('a');
            nextLink.href = '#';
            nextLink.dataset.page = pagination.current_page + 1;
            nextLink.className = `px-4 py-2 rounded-md transition-all duration-300 ${
                (pagination.current_page === pagination.total_pages || pagination.total_pages === 0) 
                    ? 'bg-gray-200 text-gray-500 cursor-not-allowed' 
                    : 'bg-white text-blue-600 hover:bg-blue-50 border border-blue-200 cursor-pointer'
            }`;
            nextLink.innerHTML = 'Next &rarr;';
            nextLink.addEventListener('click', (e) => {
                if (pagination.current_page === pagination.total_pages) e.preventDefault();
            });
            paginationContainer.appendChild(nextLink);

            // Add fade-in animation to the container
            paginationContainer.style.opacity = '0';
            setTimeout(() => {
                paginationContainer.style.opacity = '1';
                paginationContainer.style.transition = 'opacity 300ms ease-in-out';
            }, 10);
        }

        document.addEventListener('click', (e) => {
            if (e.target.matches('#assets-pagination a')) {
                e.preventDefault();
                // data-page attribute should be present on the clicked element
                if (!e.target.dataset.page) return;
                // Parse the page number from the data-page attribute
                const page = parseInt(e.target.dataset.page);
                if (!isNaN(page) && page > 0 && page <= totalPages) {
                    currentPage = page;
                    fetchAndRenderAssets(currentPage, postsPerPage)
                        .then(data  => {
                            if (data.pagination) {
                                renderPagination3(data.pagination);
                            }
                        })
                        .catch(error => {
                            showError(error);
                        });
                }
            }
        });

        // Reset filters button
        document.getElementById('reset-filters').addEventListener('click', () => {
            // Reset search input
            searchInput.value = '';
            // Reset checkboxes
            document.querySelectorAll('.filter-doc, .filter-lang').forEach(checkbox => {
                checkbox.checked = false;
                const label = checkbox.closest('label');
                const icon = label.querySelector('.activeIcon');
                if (icon) icon.classList.add('hidden');
                label.classList.remove('bg-blue-100', 'text-blue-800', 'border-blue-300');
                label.classList.add('bg-white', 'text-gray-700', 'border-gray-300');
            });
            // Reset layout
            assetPage.dataset.layout = 'grid';
            updateLayoutClasses('grid');
            // Reset current page
            currentPage = 1;

            // Fetch and render assets with reset filters
            fetchAndRenderAssets(currentPage, postsPerPage)
                .then(data  => {
                    if (data.pagination) {
                        renderPagination3(data.pagination);
                    }
                })
                .catch(error => {
                    showError(error);
                });
        });

        // Initial load
        fetchAndRenderAssets(currentPage, postsPerPage)
            .then(data  => {
                if (data.pagination) {
                    renderPagination3(data.pagination);
                }
            })
            .catch(error => {
                showError(error);
            });
    });
</script>