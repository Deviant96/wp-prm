jQuery(function($) {
    const $syncBtn = $('#manual-sync');
    const $progress = $('#sync-progress');
    const $progressBar = $progress.find('.sync-progress-bar');
    const $results = $('#sync-results');
    const $lastSync = $('.last-sync');
    const $status = $('.sync-status');

    $syncBtn.on('click', function(e) {
        e.preventDefault();
        
        $syncBtn.prop('disabled', true);
        $progress.show();
        $progressBar.css('width', '0');
        $results.empty();
        $status.text('').removeClass('error success');

        // Animate progress bar
        let width = 0;
        const interval = setInterval(() => {
            width += 5;
            $progressBar.css('width', `${width}%`);
            if (width >= 90) clearInterval(interval);
        }, 300);

        $.ajax({
            url: regionalSync.ajax_url,
            method: 'POST',
            data: {
                action: 'manual_sync_categories',
                nonce: regionalSync.nonce,
                force: true
            },
            success: (response) => {
                clearInterval(interval);
                $progressBar.css('width', '100%');
                
                if (response.success) {
                    $status.text('✓ Sync complete').addClass('success');
                    this.showResults(response.data);
                } else {
                    $status.text('✗ Sync failed').addClass('error');
                    $results.html(`
                        <div class="notice notice-error">
                            <p>${response.data}</p>
                        </div>
                    `);
                }
            },
            error: (xhr) => {
                clearInterval(interval);
                $progressBar.css('width', '100%');
                $status.text('✗ Sync failed').addClass('error');
                $results.html(`
                    <div class="notice notice-error">
                        <p>Error: ${xhr.responseText}</p>
                    </div>
                `);
            },
            complete: () => {
                $syncBtn.prop('disabled', false);
                setTimeout(() => $progress.hide(), 1000);
            }
        });
    });

    function showResults(data) {
        let html = '<div class="notice notice-success"><p>Categories synced successfully</p>';
        
        if (data.results) {
            html += '<ul>';
            for (const [region, result] of Object.entries(data.results)) {
                html += `
                    <li>
                        <strong>${region}:</strong>
                        ${result.added} added, 
                        ${result.updated} updated, 
                        ${result.skipped} skipped
                    </li>
                `;
            }
            html += '</ul>';
        }
        
        html += '</div>';
        $results.html(html);
        
        // Update last sync time
        if (data.last_sync && Object.values(data.last_sync).length) {
            const lastSync = Math.max(...Object.values(data.last_sync));
            $lastSync.text(`Last sync: ${new Date(lastSync * 1000).toLocaleString()}`);
        }
        
        // Refresh region cards
        $('.region-card').each(function() {
            const region = $(this).find('h3').text();
            const regionData = data.last_sync?.[region];
            const hasError = data.errors?.[region];
            
            $(this).toggleClass('has-error', !!hasError);
            
            if (regionData) {
                $(this).find('.last-sync').text(
                    new Date(regionData * 1000).toLocaleString()
                );
            }
            
            const $indicator = $(this).find('.status-indicator');
            $indicator.toggleClass('error', !!hasError);
            $indicator.toggleClass('success', !hasError);
            
            if (hasError) {
                $(this).find('.error-message').text(hasError.message);
            }
        });
    }
});