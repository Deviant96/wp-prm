<?php
/**
 * @param array $providers The collection of providers that will be used to scan the design payload
 * @return array
 */
function register_my_theme_provider(array $providers): array {
    $providers[] = [
        'id' => 'my_theme', // The id of this custom provider. It should be unique across all providers
        'name' => 'My Theme Scanner',
        'description' => 'Scans the current active theme and child theme',
        'callback' => 'scanner_cb_my_theme_provider', // The function that will be called to get the data. Please see the next step for the implementation
        'enabled' => \WindPress\WindPress\Utils\Config::get(sprintf(
            'integration.%s.enabled',
            'my_theme' // The id of this custom provider
        ), true),
    ];

    return $providers;
}
add_filter('f!windpress/core/cache:compile.providers', 'register_my_theme_provider');

function scanner_cb_my_theme_provider(): array {
    // The file with this extension will be scanned, you can add more extensions if needed
    $file_extensions = [
        'php',
        'js',
        'html',
    ];

    $contents = [];
    $finder = new \WindPressDeps\Symfony\Component\Finder\Finder();

    // The current active theme
    $wpTheme = wp_get_theme();
    $themeDir = $wpTheme->get_stylesheet_directory();

    // Check if the current theme is a child theme and get the parent theme directory
    $has_parent = $wpTheme->parent() ? true : false;
    $parentThemeDir = $has_parent ? $wpTheme->parent()->get_stylesheet_directory() : null;

    // Scan the theme directory according to the file extensions
    foreach ($file_extensions as $extension) {
        $finder->files()->in($themeDir)->name('*.' . $extension);
        if ($has_parent) {
            $finder->files()->in($parentThemeDir)->name('*.' . $extension);
        }
    }

    // Get the file contents and send to the compiler
    foreach ($finder as $file) {
        $contents[] = [
            'name' => $file->getRelativePathname(),
            'content' => $file->getContents(),
        ];
    }

    return $contents;
}





/**
 * Caching feature to fetch latest posts from terrabytegroup.com
 * and store them in a transient for desired time.
 * Which will be used to display latest posts in the email when a user registers as a partner.
 */
// Register Admin Menu
add_action('admin_menu', 'register_cache_control_dashboard');
function register_cache_control_dashboard() {
    add_menu_page(
        'API Cache Control',
        'Cache Control',
        'manage_options',
        'api-cache-control',
        'render_cache_control_page',
        'dashicons-database'
    );
}

// Dashboard UI
function render_cache_control_page() {
    // Handle form submissions first
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cache_settings'])) {
        check_admin_referer('cache_settings_nonce');
        
        if (isset($_POST['cache_duration'])) {
            $duration = floatval($_POST['cache_duration']) * HOUR_IN_SECONDS;
            update_option('terrabyte_cache_duration', $duration);
            echo '<div class="notice notice-success"><p>Cache duration updated!</p></div>';
        }
    }

    $cache_key = 'terrabyte_newsletters_cache';
    $cached_data = get_transient($cache_key);
    $cache_duration = get_option('terrabyte_cache_duration', HOUR_IN_SECONDS);  
    $last_updated = get_transient('terrabyte_last_updated');
    $current_duration = get_option('terrabyte_cache_duration', 24 * HOUR_IN_SECONDS);
    $hours = $current_duration / HOUR_IN_SECONDS;

    // var_dump($cache_duration); // Debugging line, can be removed later
    ?>
    <div class="wrap">
        <h1>API Cache Control</h1>

        <!-- Stats Card -->
        <div class="card" style="max-width: 600px;">
            <h2>Newsletter Cache Status</h2>
            <p>
                <strong>Last Updated:</strong> 
                <?php echo $last_updated ? date('Y-m-d H:i:s', $last_updated) : 'Never'; ?>
            </p>
            <p>
                <strong>Auto-Refresh:</strong> 
                Every <?php echo round($cache_duration / 3600, 1); ?> hours
            </p>
        </div>

        <div class="card">
            <h2>Latest Article Titles</h2>
            <?php 
                foreach ($cached_data as $posts) {
                    echo '<h3 style="font-size: 14px;">' . $posts->title->rendered . '</h3>';
                }
            ?>
        </div>

        <!-- Actions -->
        <form method="post" style="margin-top: 20px;">
            <?php wp_nonce_field('cache_control_action', 'cache_nonce'); ?>
            <button name="action" value="refresh" class="button button-primary" 
                    onclick="return confirm('Force fetch latest data?')">
                ↻ Refresh Now
            </button>
            <button name="action" value="clear" class="button button-secondary" 
                    onclick="return confirm('Delete all cached data?')">
                ✗ Clear Cache
            </button>
        </form>

        <!-- Settings -->
        <form method="post" style="margin-top: 30px;">
            <h3>Auto-Fetch Settings</h3>
            <label for="cache_duration">
                Refresh every: 
                <input type="number" name="cache_duration" value="<?php echo round($cache_duration / 3600, 1); ?>" min="0.1" step="0.1" style="width: 80px;">
                hours
            </label>
            <?php submit_button('Save', 'secondary'); ?>
        </form>
    </div>
    <?php
}

// Handle Actions
add_action('admin_init', 'handle_cache_actions');
function handle_cache_actions() {
    if (!current_user_can('manage_options') || !isset($_POST['cache_nonce']) || 
        !wp_verify_nonce($_POST['cache_nonce'], 'cache_control_action')) {
        return;
    }

    $cache_key = 'terrabyte_newsletters_cache';
    
    // Manual Refresh
    if ($_POST['action'] === 'refresh') {
        $data = fetch_latest_newsletters(); // Your API function
        if (is_wp_error($data)) {
            add_action('admin_notices', function() {
                echo '<div class="notice notice-error"><p>Error fetching data.</p></div>';
            });
            return;
        }
        set_transient($cache_key, $data, get_option('terrabyte_cache_duration', HOUR_IN_SECONDS));
        set_transient('terrabyte_last_updated', time(), DAY_IN_SECONDS);
        add_action('admin_notices', function() {
            echo '<div class="notice notice-success"><p>Cache refreshed!</p></div>';
        });
    }
    
    // Clear Cache
    elseif ($_POST['action'] === 'clear') {
        delete_transient($cache_key);
        add_action('admin_notices', function() {
            echo '<div class="notice notice-warning"><p>Cache cleared.</p></div>';
        });
    }
    
    // Update Duration
    elseif (isset($_POST['cache_duration'])) {
        if (!is_numeric($_POST['cache_duration']) || floatval($_POST['cache_duration']) <= 0) {
            add_action('admin_notices', function() {
                echo '<div class="notice notice-error"><p>Invalid cache duration. Please enter a positive number.</p></div>';
            });
            return;
        }
        $duration = floatval($_POST['cache_duration']) * 3600; // Convert hours → seconds
        update_option('terrabyte_cache_duration', $duration);
        add_action('admin_notices', function() {
            echo '<div class="notice notice-success"><p>Settings saved.</p></div>';
        });
    }
}


// Schedule Cron Job
register_activation_hook(__FILE__, 'activate_cache_auto_refresh');
function activate_cache_auto_refresh() {
    if (!wp_next_scheduled('terrabyte_auto_refresh')) {
        wp_schedule_event(time(), 'hourly', 'terrabyte_auto_refresh');
    }
}

// Hook to Fetch Data
add_action('terrabyte_auto_refresh', 'fetch_latest_newsletters');
function fetch_latest_newsletters() {
    $cache_key = 'terrabyte_newsletters_cache';
    $api_args = [
        'per_page' => 3,
        'categories' => '51,150'
    ];
    
    $response = wp_remote_get('https://www.terrabytegroup.com/wp-json/wp/v2/posts?' . http_build_query($api_args));
    
    if (!is_wp_error($response)) {
        $data = json_decode(wp_remote_retrieve_body($response));
        
        // Add featured image URL to each post
        foreach ($data as $news) {
            $media_url = 'https://www.terrabytegroup.com/wp-json/wp/v2/media/' . $news->featured_media;
            $media_response = wp_remote_get($media_url);
            if (!is_wp_error($media_response)) {
                $media_data = json_decode(wp_remote_retrieve_body($media_response));
                $news->image_url = $media_data->source_url;
            }
        }

        set_transient($cache_key, $data, get_option('terrabyte_cache_duration', HOUR_IN_SECONDS));
        set_transient('terrabyte_last_updated', time(), DAY_IN_SECONDS);
    }
}









// Verify the update_option call is executing by adding:
add_action('admin_notices', function() {
    echo '<div class="notice notice-info"><pre>'.print_r(get_option('terrabyte_cache_duration'), true).'</pre></div>';
});