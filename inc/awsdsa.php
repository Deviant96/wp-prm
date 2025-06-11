<?php
class Regional_Sync_UI {
    private $regions;
    private $last_sync;
    private $sync_errors;

    public function __construct() {
        $this->regions = get_option('tec_sync_regions', []);
        $this->last_sync = get_option('tec_last_sync', []);
        $this->sync_errors = get_option('tec_sync_errors', []);

        add_action('admin_menu', [$this, 'add_admin_page']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_assets']);
        add_action('wp_ajax_manual_sync_categories', [$this, 'handle_manual_sync']);
        add_action('created_tribe_events_cat', [$this, 'handle_category_change']);
        add_action('edited_tribe_events_cat', [$this, 'handle_category_change']);
    }

    public function add_admin_page() {
        add_menu_page(
            'Regional Sync',
            'Regional Sync',
            'manage_options',
            'regional-sync',
            [$this, 'render_ui'],
            'dashicons-update',
            80
        );
    }

    public function enqueue_assets($hook) {
        if ('toplevel_page_regional-sync' !== $hook) return;

        wp_enqueue_style(
            'regional-sync-css',
            get_template_directory_uri() . '/assets/css/regional-sync.css',
            [],
            filemtime(get_template_directory() . '/assets/css/regional-sync.css')
        );

        wp_enqueue_script(
            'regional-sync-js',
            get_template_directory_uri() . '/assets/js/regional-sync.js',
            ['jquery'],
            filemtime(get_template_directory() . '/assets/js/regional-sync.js'),
            true
        );

        wp_localize_script('regional-sync-js', 'regionalSync', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('regional_sync_nonce')
        ]);
    }

    public function render_ui() {
        ?>
        <div class="wrap regional-sync-ui">
            <h1>Regional Category Sync</h1>
            
            <div class="sync-card">
                <div class="sync-controls">
                    <button id="manual-sync" class="button button-primary">
                        <span class="dashicons dashicons-update"></span> Sync Now
                    </button>
                    <div class="sync-meta">
                        <span class="last-sync">
                            Last sync: <?php echo $this->get_last_sync_time(); ?>
                        </span>
                        <span class="sync-status"></span>
                    </div>
                </div>

                <div id="sync-progress" style="display:none;">
                    <div class="sync-progress-bar"></div>
                    <span class="sync-message">Syncing categories...</span>
                </div>

                <div id="sync-results"></div>
            </div>

            <div class="sync-card">
                <h2>Region Status</h2>
                <div class="region-grid">
                    <?php foreach ($this->regions as $region => $config) : 
                        $last_sync = $this->last_sync[$region] ?? 0;
                        $error = $this->sync_errors[$region] ?? false;
                    ?>
                    <div class="region-card <?php echo $error ? 'has-error' : ''; ?>">
                        <h3><?php echo esc_html($region); ?></h3>
                        <div class="region-meta">
                            <span class="last-sync">
                                <?php echo $last_sync ? date('M j, Y g:i a', $last_sync) : 'Never synced'; ?>
                            </span>
                            <span class="status-indicator <?php echo $error ? 'error' : 'success'; ?>"></span>
                        </div>
                        <?php if ($error) : ?>
                        <div class="error-message">
                            <?php echo esc_html($error['message']); ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php
    }

    private function get_last_sync_time() {
        if (empty($this->last_sync)) return 'Never';
        return date('M j, Y g:i a', max($this->last_sync));
    }

    public function handle_manual_sync() {
        check_ajax_referer('regional_sync_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized', 403);
        }

        $results = $this->sync_categories(true);
        
        wp_send_json_success([
            'results' => $results,
            'last_sync' => $this->last_sync,
            'errors' => $this->sync_errors
        ]);
    }

    public function handle_category_change($term_id) {
        $this->sync_categories();
    }

    private function sync_categories($force = false) {
        $current_time = time();
        $results = [];
        
        foreach ($this->regions as $region => $config) {
            if (!$force && isset($this->last_sync[$region]) {
                if ($current_time - $this->last_sync[$region] < 300) continue;
            }
            
            try {
                $response = wp_remote_get($config['url'] . '/wp-json/wp/v2/tribe_events_cat?per_page=100', [
                    'headers' => ['Authorization' => 'Basic ' . base64_encode($config['username'] . ':' . $config['password'])],
                    'timeout' => 15
                ]);
                
                if (is_wp_error($response)) throw new Exception($response->get_error_message());
                if (wp_remote_retrieve_response_code($response) !== 200) throw new Exception("API error");
                
                $categories = json_decode(wp_remote_retrieve_body($response), true);
                $results[$region] = $this->process_categories($region, $categories);
                
                $this->last_sync[$region] = $current_time;
                unset($this->sync_errors[$region]);
                
            } catch (Exception $e) {
                $this->sync_errors[$region] = [
                    'time' => $current_time,
                    'message' => $e->getMessage()
                ];
                error_log("Regional Sync Error ($region): " . $e->getMessage());
            }
        }
        
        update_option('tec_last_sync', $this->last_sync);
        update_option('tec_sync_errors', $this->sync_errors);
        
        return $results;
    }

    private function process_categories($region, $categories) {
        $counts = ['added' => 0, 'updated' => 0, 'skipped' => 0];
        
        foreach ($categories as $category) {
            $existing = $this->find_existing_term($region, $category);
            $slug = sanitize_title($region . '-' . $category['slug']);
            
            $args = [
                'description' => $category['description'],
                'slug' => $slug,
                'parent' => $this->get_region_parent_id($region)
            ];
            
            if ($existing) {
                if ($this->needs_update($existing, $category, $region)) {
                    wp_update_term($existing->term_id, 'category', $args);
                    $counts['updated']++;
                } else {
                    $counts['skipped']++;
                }
            } else {
                $args['name'] = $category['name'];
                $result = wp_insert_term($category['name'], 'category', $args);
                
                if (!is_wp_error($result)) {
                    add_term_meta($result['term_id'], 'imported_from_region', $region);
                    add_term_meta($result['term_id'], 'original_event_cat_id', $category['id']);
                    $counts['added']++;
                }
            }
        }
        
        return $counts;
    }

    // ... (include the same helper methods from previous implementation)
}

// Initialize if we're in admin
if (is_admin()) {
    new Regional_Sync_UI();
}


















.regional-sync-ui {
    max-width: 1200px;
}

.sync-card {
    background: #fff;
    border: 1px solid #ccd0d4;
    box-shadow: 0 1px 1px rgba(0,0,0,0.04);
    padding: 20px;
    margin-bottom: 20px;
}

.sync-controls {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 20px;
}

.sync-meta {
    display: flex;
    align-items: center;
    gap: 10px;
    color: #646970;
}

#sync-progress {
    margin: 15px 0;
    display: none;
}

.sync-progress-bar {
    height: 4px;
    background: #2271b1;
    width: 0;
    transition: width 0.3s ease;
}

.region-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 15px;
}

.region-card {
    padding: 15px;
    border: 1px solid #dcdcde;
    border-radius: 4px;
    position: relative;
}

.region-card.has-error {
    border-left: 4px solid #d63638;
}

.region-meta {
    display: flex;
    justify-content: space-between;
    margin-top: 10px;
    font-size: 0.9em;
    color: #646970;
}

.status-indicator {
    display: inline-block;
    width: 12px;
    height: 12px;
    border-radius: 50%;
}

.status-indicator.success {
    background: #00a32a;
}

.status-indicator.error {
    background: #d63638;
}

.error-message {
    margin-top: 10px;
    padding-top: 10px;
    border-top: 1px dashed #dcdcde;
    color: #d63638;
    font-size: 0.85em;
}

#sync-results .notice {
    margin: 15px 0 0;
}


























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












$regions = [
    'north' => [
        'url' => 'https://north.example.com',
        'username' => 'api_user',
        'password' => 'api_password'
    ],
    // Add other regions
];
update_option('tec_sync_regions', $regions);




















Add this to your theme's functions.php:
// Include the regional sync UI
require_once get_template_directory() . '/inc/regional-sync-ui.php';