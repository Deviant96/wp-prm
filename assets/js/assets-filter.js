document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('asset-search').addEventListener('input', debounce(fetchAssets, 300));
    document.querySelectorAll('.asset-filter').forEach(cb => cb.addEventListener('change', debounce(fetchAssets, 200)));
    document.getElementById('asset-search').addEventListener('keydown', (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            fetchAssets();
        }
    });
    document.getElementById('asset-search').addEventListener('blur', () => {
        if (document.getElementById('asset-search').value === '') {
            document.getElementById('asset-search').value = '';
        }
    });

    let currentPage = 1;
    let isLoading = false;

    async function fetchAssets(perPage = 10) {
        if (isLoading) return;
        isLoading = true;
        showAssetSearchLoadingIndicator();
        // const loadingSpinner = document.getElementById('loadingSpinner');
        // loadingSpinner.classList.remove('hidden');

        const search = document.getElementById('asset-search').value;

        const filters = [...document.querySelectorAll('.event-filter:checked')].map(cb => ({
            tax: cb.dataset.tax,
            value: cb.value
        }));

        const s = encodeURIComponent(search);
        try {
            const response = await fetch(`${wpApiSettings.root}prm/v1/tbyte_prm_assets?s=${s}&post_per_page=4`, {
                method: "GET",
                credentials: 'same-origin',
                mode: 'cors',
                cache: 'no-cache',
                redirect: 'follow',
                referrerPolicy: 'no-referrer',
                // headers: {
                //     'Content-Type': 'application/json',
                //     'X-WP-Nonce': wpApiSettings.nonce,
                // },
                headers: { 
                    'Content-Type': 'application/json',
                    'X-WP-Nonce': wpApiSettings.nonce,
                },
                // body: JSON.stringify({ 
                //     search,
                //     filters, 
                //     page: currentPage,
                //     post_per_page: perPage,
                // })
            });

            if (!response.ok) {
                throw new Error('Network response was not ok');
            }

            const data = await response.json();
            updateAssetList(data);
        } catch (error) {
            console.error('Error fetching assets:', error);
            showError('Error fetching assets. Please try again later.');
        } finally {
            isLoading = false;
            hideAssetSearchLoadingIndicator();
        }
    }

    function updateAssetList(data) {
        const assetList = document.getElementById('asset-list');

        if (data.items.length === 0) {
            assetList.innerHTML = `<div class="no-events col-span-4 md:col-span-4 text-[#9d9d9d] font-bold">${data.message}</div>`;
            return;
        }
        
        // Build HTML from the data (you might want to use a template)
        let html = '';
        data.items.forEach(asset => {
            const id = asset.id || '';
            const title = asset.title || 'No Title';
            const date = asset.date ? formatDate(asset.date) : '';
            const docType = asset.doc_type ? asset.doc_type : undefined;
            const content = asset.content || '';
            const language = asset.language || '';
            const link = asset.link || '#';
            // Generate thumbnail based on the doc type
            let docTypeThumb = '';
            if(docType === 'PDF') {
                docTypeThumb = `<img src="${wpApiSettings.theme_path}/assets/images/pdf-icon.png" alt="PDF Icon" class="w-full h-full object-cover">`;
            } else if (docType === 'Image') {
                docTypeThumb = `<img src="${content}" alt="Image Icon" class="w-full h-full object-cover">`;
            } else if (docType === 'URL') {
                docTypeThumb = `<img src="${wpApiSettings.theme_path}/assets/images/link-icon.png" alt="URL Icon" class="w-full h-full object-cover">`;
            } else if (docType === 'Text') {
                docTypeThumb = `<img src="${wpApiSettings.theme_path}/assets/images/text-icon.png" alt="Text Icon" class="w-full h-full object-cover">`;
            } else{
                docTypeThumb = `<div class="w-full h-full bg-gray-100  flex items-center justify-center">
                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>`;
            }
            let linkElement = '';
            if (docType === 'Text') {
                linkElement = `<a href="${link}" class="text-blue-600 hover:underline" onclick="copyAsset(event, this)">
                    <ion-icon name="copy-outline"></ion-icon>
                </a>`;
            } else if (docType === 'Image') {
                linkElement = `<a href="${link}" class="text-blue-600 hover:underline">
                    <ion-icon name="download-outline"></ion-icon>
                </a>`;
            } else if (docType === 'PDF') {
                linkElement = `<a href="${link}" class="text-blue-600 hover:underline">
                    <ion-icon name="download-outline"></ion-icon>
                </a>`;
            } else {
                linkElement = `<a href="${link}" class="text-blue-600 hover:underline" onclick="copyAsset(event, this)">
                    <ion-icon name="copy-outline"></ion-icon>
                </a>`;
            }

            html += `
                <div class="flex flex-col bg-white border border-gray-200 overflow-hidden p-4 rounded-xl shadow" data-id="${id}">
                    <div class="w-full bg-gray-200 h-64 sm:h-auto">
                        ${docTypeThumb}
                    </div>
                    <div class="flex flex-col justify-between flex-grow">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 ">
                                ${title}
                            </h3>
                            <div class="text-sm text-gray-500 mb-1">${content}</div>
                            <small class="text-gray-500">${date}</small> – ${docType}
                        </div>
                        <div class="mt-4 flex justify-end gap-2">
                            ${linkElement}
                            <a href="<?php the_permalink(); ?>" class="text-green-600 hover:underline">
                                <ion-icon name="eye-outline"></ion-icon>
                            </a>
                        </div>
                    </div>
                </div>
            `;
        });
        
        assetList.innerHTML = html;
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

    function showAssetSearchLoadingIndicator() {
        const assetList = document.getElementById('asset-list');
        assetList.innerHTML = ''; // Clear previous content

        console.log('showSearchLoadingIndicator');

        const skeletonCount = 4; // Or however many placeholders you want
        let html = '';

        for (let i = 0; i < skeletonCount; i++) {
            html += `
            <div class="bg-white border border-gray-200 p-4 rounded-xl shadow animate-pulse mb-4">
                <div class="h-40 bg-gray-300 rounded mb-2"></div>
                <div class="h-4 bg-gray-300 rounded w-3/4 mb-2"></div>
                <div class="h-4 bg-gray-300 rounded w-full mb-2"></div>
                <div class="h-4 bg-gray-300 rounded w-5/6"></div>
            </div>`;
        }

        assetList.innerHTML = html;
    }

    function hideAssetSearchLoadingIndicator() {
        const assetList = document.getElementById('asset-list');
        // assetList.innerHTML = '';
    }
});