<?php
$layout = $_GET['layout'] ?? 'grid';
$search = sanitize_text_field($_GET['s'] ?? '');
$doc_type = isset($_GET['doc_type']) ? explode(',', sanitize_text_field($_GET['doc_type'])) : [];
// var_dump($doc_type);
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
        <div>
            <h4 class="font-semibold mb-2">Doc Type</h4>
            <?php foreach (get_terms(['taxonomy' => 'doc_type']) as $term): ?>
                <label class="block">
                    <input type="checkbox"
                        class="filter-doc mr-2"
                        value="<?php echo $term->slug; ?>"
                        <?php checked(in_array($term->slug, (array)$doc_type)); ?>>
                    <?php echo $term->name; ?>
                </label>
            <?php endforeach; ?>
        </div>
        <div>
            <h4 class="font-semibold mb-2">Language</h4>
            <?php foreach (get_terms(['taxonomy' => 'language']) as $term): ?>
                <label class="block">
                    <input type="checkbox"
                        class="filter-lang mr-2"
                        value="<?php echo $term->slug; ?>"
                        <?php checked(in_array($term->slug, (array)$language)); ?>>
                    <?php echo $term->name; ?>
                </label>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Tag Filter Chips -->
    <!-- <div class="mb-6 flex flex-wrap gap-2">
        <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full cursor-pointer hover:bg-blue-200">#New</span>
        <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full cursor-pointer hover:bg-green-200">#Brochure</span>
        <span class="bg-purple-100 text-purple-700 px-3 py-1 rounded-full cursor-pointer hover:bg-purple-200">#2025</span>
    </div> -->

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
    <div id="resultsContainer" class="asset-results grid gap-4 <?php echo $layout === 'list' ? 'grid-cols-1' : 'grid-cols-2 md:grid-cols-3'; ?>">
    </div>

    <!-- Pagination -->
    <div class="mt-6 flex justify-center" id="assets-pagination"></div>
</div>

<?php wp_reset_postdata(); ?>


<script>
    document.addEventListener('DOMContentLoaded', () => {
        const assetPage = document.getElementById('assets-page');
        // const resultsContainer = assetPage.querySelector('.asset-results');
        // const loadingElement = document.getElementById('assets-loading');
        const paginationElement = document.getElementById('assets-pagination');
        const searchInput = document.getElementById('search-assets');
        const layoutToggles = document.querySelectorAll('.layout-toggle');
        const filters = document.querySelectorAll('.filter-doc, .filter-lang');

        let currentPage = 1;
        let debounceTimer;

        // This is for displaying loading skeletons
        const loadingElement = document.getElementById('loadingElement');
        const resultsContainer = document.getElementById('resultsContainer');
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
            resultsContainer.innerHTML = '';

            // Add 6 placeholder cards
            for (let i = 0; i < 6; i++) {
                skeletonContainer.appendChild(createSkeletonCard());
            }

            loadingElement.classList.remove('hidden');
            resultsContainer.classList.add('opacity-50');
        }

        function hideLoading() {
            loadingElement.classList.add('hidden');
            resultsContainer.classList.remove('opacity-50');
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

        async function loadAssets() {
            // console.log("hahaha")
            showLoading();
            const params = collectFilters();
            // console.log("params: ", params);
            updateURL(params);

            try {
                const formData = new FormData();
                formData.append('action', 'prm_load_assets');
                formData.append('nonce', '<?php echo wp_create_nonce('prm_ajax_nonce'); ?>');
                Object.entries(params).forEach(([key, value]) => {
                    formData.append(key, Array.isArray(value) ? value.join(',') : value);
                });

                const response = await fetch(ajax_object.ajax_url, {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();


                if (data.success) {
                    resultsContainer.innerHTML = data.data.html;
                    renderPagination(data.data.max_pages);
                }
            } catch (error) {
                console.error('Error loading assets:', error);
                resultsContainer.innerHTML = '<p class="text-red-500">Error loading assets. Please try again.</p>';
            } finally {
                hideLoading();
            }
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
            resultsContainer.className = `asset-results grid gap-4 ${
                newLayout === 'list' ? 'grid-cols-1' : 'grid-cols-2 md:grid-cols-3'
            }`;
            
            // Update layout toggle buttons
            layoutToggles.forEach(btn => {
                btn.classList.toggle('text-blue-500', btn.dataset.layout === newLayout);
                btn.classList.toggle('text-gray-500', btn.dataset.layout !== newLayout);
            });
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
                loadAssets();
            });
        });

        // Initial load
        loadAssets();
    });
</script>