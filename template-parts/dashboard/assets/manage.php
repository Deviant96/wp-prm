<?php
$current_user = wp_get_current_user();

// Check if current user is Partner Manager
if (!in_array('partner_manager', $current_user->roles) && !in_array('administrator', $current_user->roles)) {
    echo '<p class="text-red-500">Access Denied.</p>';
    return;
}
?>

<div class="space-y-6">
    <!-- Header & Add Button -->
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold">Manage Assets</h2>
        <a href="<?php echo home_url('/?tab=assets-create'); ?>" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
            Add New Asset
        </a>
    </div>

    <!-- Add/Edit Form -->
    <div id="assetFormContainer" class="hidden bg-gray-50  p-4 rounded shadow">
        <form id="assetForm" class="space-y-4">
            <input type="hidden" name="asset_id" id="asset_id" value="">
            <div>
                <label class="block font-medium">Asset Name</label>
                <input type="text" name="asset_name" id="asset_name" class="w-full p-2 rounded border">
            </div>
            <div>
                <label class="block font-medium">Tags (comma separated)</label>
                <input type="text" name="asset_tags" id="asset_tags" class="w-full p-2 rounded border">
            </div>
            <div>
                <label class="block font-medium">Asset File</label>
                <input type="file" name="asset_file" id="asset_file" class="w-full">
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" id="cancelAssetBtn" class="text-gray-600 hover:underline">Cancel</button>
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Save</button>
            </div>
        </form>
    </div>

    <form id="asset-form" class="hidden bg-white  p-4 rounded shadow" onsubmit="submitAssetForm(asset)">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <input type="text" name="asset_name" placeholder="Asset Name" class="input" required />
            <input type="url" name="asset_url" placeholder="Asset URL" class="input" required />
            <input type="text" name="asset_tags" placeholder="Tags (comma-separated)" class="input" />
            <input type="file" name="asset_image" class="input" />
        </div>
        <button type="submit" class="mt-4 bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Save</button>
    </form>

    <!-- Assets Table -->
    <div id="assets-list" class="overflow-auto rounded shadow">
        <table class="w-full table-auto text-left text-sm">
            <thead class="bg-gray-100 ">
                <tr>
                    <th class="p-2">ID</th>
                    <th class="p-2">Name</th>
                    <th class="p-2">Doc Type</th>
                    <th class="p-2">Language</th>
                    <th class="p-2">Date</th>
                    <th class="p-2 text-right">Actions</th>
                </tr>
            </thead>
            <tbody id="assets-table-body2" class="bg-white  divide-y">
                <!-- Assets list -->
            </tbody>
            <tfoot class="bg-gray-100 ">
                <tr>
                    <td colspan="6" class="p-2 text-center text-gray-500">
                        <div id="asset-pagination" class="flex justify-center mt-4">
                        </div>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>


</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const formContainer = document.getElementById('assetFormContainer');
        const assetForm = document.getElementById('assetForm');

        document.querySelectorAll('.editAssetBtn').forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.dataset.id;
                // fetch data for that asset via AJAX (to be implemented)
                formContainer.classList.remove('hidden');
            });
        });

        document.querySelectorAll('.deleteAssetBtn').forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.dataset.id;
                if (confirm('Delete this asset?')) {
                    // delete via AJAX (to be implemented)
                }
            });
        });

        assetForm.addEventListener('submit', (e) => {
            e.preventDefault();
            // handle form submit via AJAX
        });
    });
</script>

<script>
    function submitAssetForm(e) {
        e.preventDefault();
        const form = e.target;
        const formData = new FormData(form);

        fetch(ajax_object.ajax_url + '?action=save_asset', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                alert(data.message);
                form.reset();
            });
    }

</script>

<script>
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
</script>

<script>
    async function deleteAsset(id) {
        if (confirm('Are you sure you want to delete this asset?')) {
            var assetId = id;

            try {
                const response = await fetch(`${wpApiSettings.root}prm/v1/tbyte_prm_assets/${assetId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-WP-Nonce': wpApiSettings.nonce,
                        'Content-Type': 'application/json'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    showSuccess('Document type deleted successfully!');
                    location.reload();
                } else {
                    showError('Error: ' + data.data);
                }
            } catch (error) {
                // TODO Add logging
                showError('An error occurred while deleting the asset. Please try again.');
            }
        }
    }
</script>

<script>
    let currentPage = 1;
    let totalPages = 1;
    const itemsPerPage = 5;

    document.addEventListener('DOMContentLoaded', () => {
        async function fetchAndRenderAssets(page = 1, perPage = 5, searchParams = {}) {
            let assets = [];
            // Validate page number
            pageNumber = Math.max(1, Math.min(page, totalPages));
            currentPage = pageNumber;

            const tbody = document.getElementById('assets-table-body2');
            const assetList = document.getElementById('assets-list');
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

            // Build query string
            const query = new URLSearchParams({
                page: page,
                posts_per_page: perPage,
                ...searchParams // Include any filters/search terms
            }).toString();

            showLoading('assets-table-body2', 5, 6); // Show loading state

            try {
                const url = `wp-json/prm/v1/tbyte_prm_assets?${query}`;

                const response = await fetch(url);

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
                    const row = document.createElement('tr');
                    row.appendChild(createTableCell(asset.id));
                    row.appendChild(createTableCell(asset.title));
                    row.appendChild(createTableCell(asset.doc_type));
                    row.appendChild(createTableCell(asset.language));
                    row.appendChild(createTableCell(asset.date));
                    const actionsCell = createTableCell('', 'text-right');
                    actionsCell.innerHTML = `
                        <button class="text-blue-600 hover:underline editAssetBtn" data-id="${asset.id}"><ion-icon name="create-outline"></ion-icon></button>
                        <button class="text-red-600 hover:underline ml-2 deleteAssetBtn" data-id="${asset.id}" onclick="deleteAsset(${asset.id})"><ion-icon name="trash-outline"></ion-icon></button>
                    `;
                    row.appendChild(actionsCell);
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
                tbody.style.opacity = '1'; // Reset opacity on error
                return {
                    error: error.message
                }
            }
        }

        // Call the function to load the first page
        // fetchAndRenderAssets();

        // Handle browser back/forward buttons
        window.addEventListener('popstate', () => {
            const params = new URLSearchParams(window.location.search);
            const page = parseInt(params.get('page')) || 1;
            // loadPage(page);
        });

        // Initial load
        // loadPage(currentPage, {
        //     per_page: itemsPerPage
        // });

        // Add event listeners for pagination buttons
        const pagination = document.getElementById('asset-pagination');
        pagination.addEventListener('click', (e) => {
            if (e.target.tagName === 'A' && !e.target.classList.contains('cursor-not-allowed')) {
                e.preventDefault();
                const page = parseInt(e.target.dataset.page, 10);

                // Add loading state
                let originalText = e.target.innerHTML;
                e.target.innerHTML = '<span class="animate-pulse">Loading...</span>';
                e.target.classList.add('cursor-not-allowed', 'opacity-50');

                fetchAndRenderAssets(page).then(data => {
                    e.target.innerHTML = originalText
                    e.target.classList.remove('cursor-not-allowed', 'opacity-50');
                    currentPage = page;
                    renderPagination3(data.pagination);
                });
            }
        });

        function showLoading(tableId, rowCount = 5, columnCount = 3) {
            const table = document.getElementById(tableId);

            // Clear existing table content
            table.innerHTML = '';

            for (let i = 0; i < rowCount; i++) {
                const row = document.createElement('tr');

                for (let j = 0; j < columnCount; j++) {
                    const cell = document.createElement('td');

                    // Tailwind-based shimmer placeholder
                    cell.innerHTML = `
                        <div class="h-4 bg-gray-300 rounded animate-pulse w-full"></div>
                    `;
                    cell.className = 'p-2'; // Padding for cell
                    row.appendChild(cell);
                }

                table.appendChild(row);
            }
        }

        function renderPagination2(pagination) {
            const paginationContainer = document.getElementById('asset-pagination');
            paginationContainer.innerHTML = '';
            // Previous button
            if (pagination.current_page > 1) {
                const prevLink = document.createElement('a');
                prevLink.href = '#';
                prevLink.dataset.page = pagination.current_page - 1;
                prevLink.className = 'px-3 py-1 mx-1 border rounded';
                prevLink.textContent = 'Previous';
                paginationContainer.appendChild(prevLink);
            }

            // Page numbers
            for (let i = 1; i <= pagination.total_pages; i++) {
                const pageLink = document.createElement('a');
                pageLink.href = '#';
                pageLink.dataset.page = i;
                pageLink.className = `px-3 py-1 mx-1 border rounded ${i === pagination.current_page ? 'bg-blue-500 text-white' : ''}`;
                pageLink.textContent = i;
                paginationContainer.appendChild(pageLink);
            }

            // Next button
            if (pagination.current_page < pagination.total_pages) {
                const nextLink = document.createElement('a');
                nextLink.href = '#';
                nextLink.dataset.page = pagination.current_page + 1;
                nextLink.className = 'px-3 py-1 mx-1 border rounded';
                nextLink.textContent = 'Next';
                paginationContainer.appendChild(nextLink);
            }
        }

        function renderPagination3(pagination) {
            const paginationContainer = document.getElementById('asset-pagination');
            paginationContainer.innerHTML = '';

            // Add smooth transition class to container
            paginationContainer.className = 'flex space-x-2 transition-all duration-300';

            // Previous button (always visible but disabled when on first page)
            const prevLink = document.createElement('a');
            prevLink.href = '#';
            prevLink.dataset.page = pagination.current_page - 1;
            prevLink.className = `px-4 py-2 rounded-md transition-all duration-300 ${
                pagination.current_page === 1 
                    ? 'bg-gray-200 text-gray-500 cursor-not-allowed' 
                    : 'bg-white text-blue-600 hover:bg-blue-50 border border-blue-200 cursor-pointer'
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
        // Call the function to load the first page
        fetchAndRenderAssets(1, 5)
            .then(assets => {
                console.error(assets);  
                if (assets.pagination) {
                    renderPagination3(assets.pagination);
                }
            })
            .catch(error => {
                showError('Error loading assets. Please try again.');
            });
    })
</script>