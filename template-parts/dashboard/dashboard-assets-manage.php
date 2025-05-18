<script>
jQuery(document).ready(function($) {
    // Toggle asset form
    jQuery('#show-asset-form').on('click', function() {
        $('#assetFormContainer').removeClass('hidden');
        $(this).addClass('hidden');
    });
    
    jQuery('#cancelAssetBtn').on('click', function() {
        $('#assetFormContainer').addClass('hidden');
        $('#show-asset-form').removeClass('hidden');
        $('#assetForm')[0].reset();
        $('#asset-content-field').html('<div class="bg-gray-100  p-4 rounded text-center text-gray-500 ">Please select a document type to see specific requirements</div>');
    });
    
    // Handle form submission
    jQuery('#assetForm').on('submit', function(e) {
        console.error('Form submitting...');
        e.preventDefault();
        
        var formData = new FormData(this);
        
        console.error('Form data:', formData);
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    alert('Asset saved successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + response.data);
                }
            }
        });
    });
    
    // Delete asset
    jQuery('.delete-asset').on('click', function() {
        if (!confirm('Are you sure you want to delete this asset?')) {
            return;
        }
        
        var assetId = $(this).data('asset-id');
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'delete_asset',
                asset_id: assetId,
                security: $('#asset_nonce').val()
            },
            success: function(response) {
                if (response.success) {
                    alert('Asset deleted successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + response.data);
                }
            }
        });
    });
});
</script>

<script>
    // Load asset data for editing
    jQuery('.edit-asset').on('click', function() {
        var assetId = $(this).data('asset-id');
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'get_asset_data',
                asset_id: assetId,
                security: $('#asset_nonce').val()
            },
            success: function(response) {
                if (response.success) {
                    var asset = response.data;
                    
                    // Populate form fields
                    $('#asset_id').val(asset.asset_id);
                    $('#asset_name').val(asset.asset_name);
                    $('#asset_doc_type').val(asset.asset_doc_type).trigger('change');
                    $('#asset_language').val(asset.asset_language);
                    $('#asset_tags').val(asset.asset_tags);
                    $('#asset_status').val(asset.asset_status);
                    $('#asset_publish_date').val(asset.asset_publish_date);
                    $('#asset_description').val(asset.asset_description);
                    
                    // Set content field based on type (after a small delay to ensure field is created)
                    setTimeout(function() {
                        if (asset.field_type === 'text' || asset.field_type === 'url') {
                            $('[name="asset_content"]').val(asset.asset_content);
                        }
                        // For file types, we can't pre-populate the file input due to browser security
                    }, 100);
                    
                    // Show form
                    $('#assetFormContainer').removeClass('hidden');
                    $('#show-asset-form').addClass('hidden');
                } else {
                    alert('Error: ' + response.data);
                }
            }
        });
    });

    // Initialize date picker to today if empty
    if (!jQuery('#asset_publish_date').val()) {
        jQuery('#asset_publish_date').val(new Date().toISOString().substr(0, 10));
    }
</script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const addBtn = document.getElementById('addAssetBtn');
        const cancelBtn = document.getElementById('cancelAssetBtn');
        const formContainer = document.getElementById('assetFormContainer');
        const assetForm = document.getElementById('assetForm');

        addBtn.addEventListener('click', () => {
            assetForm.reset();
            formContainer.classList.remove('hidden');
        });

        cancelBtn.addEventListener('click', () => {
            formContainer.classList.add('hidden');
        });

        document.querySelectorAll('.editAssetBtn').forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.dataset.id;
                // fetch data for that asset via AJAX (to be implemented)
                console.log('Edit', id);
                formContainer.classList.remove('hidden');
            });
        });

        document.querySelectorAll('.deleteAssetBtn').forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.dataset.id;
                if (confirm('Delete this asset?')) {
                    // delete via AJAX (to be implemented)
                    console.log('Delete', id);
                }
            });
        });

        assetForm.addEventListener('submit', (e) => {
            e.preventDefault();
            // handle form submit via AJAX
            console.log('Submitting form...');
        });
    });
</script>

<script>
    function toggleAssetForm() {
        document.getElementById('asset-form').classList.toggle('hidden');
    }

    function submitAssetForm(e) {
        e.preventDefault();
        const form = e.target;
        const formData = new FormData(form);

        fetch(ajax_object.ajax_url + `?action=save_asset`, {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                alert(data.message);
                form.reset();
                toggleAssetForm();
                // loadAssets();
            });
    }

    // document.addEventListener('DOMContentLoaded', loadAssets);
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
    function deleteAsset(id) {
        if (confirm('Are you sure you want to delete this asset?')) {
            fetch(ajax_object.ajax_url + `?action=delete_asset&id=${id}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-WP-Nonce': '<?php echo wp_create_nonce('delete_asset_nonce'); ?>'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        alert('Asset deleted successfully!');
                        // Optionally, refresh the asset list
                        // loadAssets();
                    } else {
                        alert('Error deleting asset: ' + data.message);
                    }
                });
        }
    }
</script>

<script>
    let currentPage = 1;
    let totalPages = 1;
    const itemsPerPage = 5;

    document.addEventListener('DOMContentLoaded', () => {
        async function fetchAndRenderAssets(page = 1, perPage = 5, searchParams = {}) {
            // Validate page number
            pageNumber = Math.max(1, Math.min(page, totalPages));
            currentPage = pageNumber;

            const tbody = document.getElementById('assets-table-body2');
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
                page: currentPage,
                per_page: itemsPerPage,
                ...searchParams // Include any filters/search terms
            }).toString();

            showLoading('assets-table-body2', 5, 6); // Show loading state

            try {
                const response = await fetch(ajax_object.ajax_url + `?action=get_assets&paged=${page}&posts_per_page=${perPage}`);
                const data = await response.json();

                if (!data.success) {
                    console.error('API error:', data.data.message);
                    return;
                }

                const assets = data.data.assets;

                // Clear existing rows
                tbody.innerHTML = '';

                // Render each asset as a table row
                assets.forEach(asset => {
                    const tr = document.createElement('tr');

                    // Define the cells you want to display and their optional classes
                    const cells = [{
                            content: asset.id
                        },
                        {
                            content: asset.title,
                            className: 'p-2 font-medium'
                        },
                        {
                            content: asset.doc_types
                        },
                        {
                            content: asset.languages
                        },
                        {
                            content: asset.date
                        }
                    ];

                    // Create and append all cells
                    cells.forEach(cell => {
                        tr.appendChild(createTableCell(cell.content, cell.className));
                    });

                    // Add action buttons cell if needed
                    const actionsCell = document.createElement('td');
                    actionsCell.className = 'p-2 text-right space-x-2';
                    actionsCell.innerHTML = `
                        <button class="editAssetBtn text-blue-600" data-id="<?php echo get_the_ID(); ?>">
                            <ion-icon name="create-outline"></ion-icon>
                        </button>
                        <button class="deleteAssetBtn text-red-600" data-id="<?php echo get_the_ID(); ?>" onclick="deleteAsset(<?php echo get_the_ID(); ?>)">
                            <ion-icon name="trash-outline"></ion-icon>
                        </button>
                    `;
                    tr.appendChild(actionsCell);

                    tbody.appendChild(tr);
                });

                // Fade in the new content
                setTimeout(() => {
                    tbody.style.opacity = '1';
                }, 10);

                updateQueryParam('page', page);

                return data;
            } catch (error) {
                console.error('Fetch error:', error);
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
                e.target.innerHTML = '<span class="animate-pulse">Loading...</span>';
                e.target.classList.add('cursor-not-allowed', 'opacity-50');

                fetchAndRenderAssets(page).then(data => {
                    console.log('Pagination data:', data);
                    if (data && data.data && data.data.pagination) {
                        renderPagination3(data.data.pagination);
                    }
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
            console.error("pagina: ", pagination.current_page)
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
        fetchAndRenderAssets(1, 5).then(data => {
            console.error('data', data.data.pagination);
            if (data && data.data && data.data.pagination) {
                renderPagination3(data.data.pagination);
            }
        });
    })
</script>