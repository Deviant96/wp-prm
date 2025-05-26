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
    <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-4 mb-6 text-sm">
        <div class="mb-4">
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
        <div class="mb-4" id="language-filter">
            <h4 class="text-sm font-semibold text-gray-700 mb-3">Language</h4>
            <div class="flex flex-wrap gap-2">
                <?php foreach (get_terms(['taxonomy' => 'language']) as $term): ?>
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
                                <svg class="ml-1.5 h-3.5 w-3.5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            <?php endif; ?>
                        </span>
                    </label>
                <?php endforeach; ?>
            </div>
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
    <div class="mt-6 flex justify-center" id="assets-pagination"></div>
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
                page: currentPage
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

        function renderPagination(maxPages) {
            if (maxPages <= 1) {
                paginationElement.innerHTML = '';
                return;
            }

            let html = '';
            for (let i = 1; i <= maxPages; i++) {
                html += `<button class="pagination-button px-3 py-1 mx-1 rounded ${currentPage === i ? 'bg-blue-500 text-white' : 'bg-gray-200'}"
                            data-page="${i}">${i}</button>`;
            }
            paginationElement.innerHTML = html;

            paginationElement.querySelectorAll('.pagination-button').forEach(button => {
                button.addEventListener('click', () => {
                    currentPage = parseInt(button.dataset.page);
                    loadAssets();
                });
            });
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
                loadAssets();
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

        async function fetchAndRenderAssets(page = 1, perPage = 5, searchParams = {}) {
            showLoading();
            const params = collectFilters();
            updateURL(params);

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

            // Build query method #1
            const query = new URLSearchParams({
                page: page,
                posts_per_page: perPage,
                ...searchParams // Include any filters/search terms
            }).toString();

            // Build query method #2
            const queryParams = Object.entries(params)
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

                // Assuming the API returns an object with a 'data' property
                assets = data.items;
                // const pagination = data.data.pagination;

                // Render the events
                // renderEvents(events);
                // renderPagination3(pagination);
                // });


                // Clear existing rows
                tbody.innerHTML = '';

                // Render each asset
                assets.forEach(asset => {
                    console.error(asset)
                    const row = document.createElement('div');
                    row.className = 'asset-card flex flex-col bg-white border border-gray-200 overflow-hidden p-4 rounded-xl shadow';
                    row.setAttribute('data-id', asset.id);
                    const { id, title, date, link, doc_type, docTypeThumb } = asset;
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

                // Fade in the new content
                setTimeout(() => {
                    tbody.style.opacity = '1';
                }, 10);

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

        // Initial load
        fetchAndRenderAssets()
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