<?php
// Theme setup
function wp_prm_theme_setup()
{
    // Add support for title tag
    add_theme_support('title-tag');

    // Add support for post thumbnails
    add_theme_support('post-thumbnails');

    // Register navigation menus
    // register_nav_menus(array(
    //     'primary' => __('Primary Menu', 'wp-prm'),
    //     'footer' => __('Footer Menu', 'wp-prm'),
    // ));

    load_theme_textdomain('prm', get_template_directory() . '/languages');
}
add_action('after_setup_theme', 'wp_prm_theme_setup');

// Include files
require_once get_template_directory() . '/inc/font-styles.php';
require get_template_directory() . '/inc/roles.php';
require get_template_directory() . '/inc/custom-post-types.php';
require get_template_directory() . '/inc/shortcodes.php';
// require get_template_directory() . '/inc/form-handler.php';
require_once get_template_directory() . '/inc/form-handler.php';
require_once get_template_directory() . '/inc/custom-features-functions.php';
require get_template_directory() . '/inc/admin/assets-functions.php';
require get_template_directory() . '/inc/dashboard-functions.php';
require get_template_directory() . '/inc/assets-functions.php';
require get_template_directory() . '/inc/event-functions.php';
require get_template_directory() . '/inc/user-approval-functions.php';

// Enqueue styles and scripts
function wp_prm_enqueue_scripts()
{
    wp_enqueue_style('prm-dashboard', get_template_directory_uri() . '/assets/css/dashboard.css');
    wp_enqueue_script('prm-dashboard', get_template_directory_uri() . '/assets/js/dashboard.js', ['jquery'], null, true);
    wp_localize_script('prm-dashboard', 'ajax_object', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('prm_nonce'),
    ));
}
add_action('wp_enqueue_scripts', 'wp_prm_enqueue_scripts');

// Register widget areas
function wp_prm_widgets_init()
{
    register_sidebar(array(
        'name' => __('Sidebar', 'wp-prm'),
        'id' => 'sidebar-1',
        'description' => __('Add widgets here.', 'wp-prm'),
        'before_widget' => '<div class="widget">',
        'after_widget' => '</div>',
        'before_title' => '<h2 class="widget-title">',
        'after_title' => '</h2>',
    ));
}
add_action('widgets_init', 'wp_prm_widgets_init');

// Redirect Login to Partner Dashboard
function prm_global_login_redirect($redirect_to, $request, $user)
{
    // If login failed or user not set, skip
    if (!isset($user->roles) || empty($user->roles)) {
        return $redirect_to;
    }

    // Redirect by role
    if (in_array('partner', $user->roles)) {
        return home_url('/dashboard'); // Partner dashboard
    }

    if (in_array('partner_manager', $user->roles)) {
        return home_url('/dashboard'); // Same or different dashboard
    }

    if (in_array('administrator', $user->roles)) {
        return home_url('/dashboard'); // Or admin area if preferred
    }

    // Default redirect (other roles)
    return home_url('/dashboard');
}
// add_filter('login_redirect', 'prm_global_login_redirect', 10, 3);


// Auto Redirect Logged-in Users from Partner Portal
function prm_redirect_logged_in_from_portal()
{
    if (is_page_template('page-partner-portal.php') && is_user_logged_in()) {
        $user = wp_get_current_user();

        if (in_array('partner', (array) $user->roles) || in_array('partner_manager', (array) $user->roles)) {
            wp_redirect(home_url('/dashboard'));
            exit;
        }

        // Optionally redirect admins or other roles
        // wp_redirect(admin_url()); exit;
    }
}
// add_action('template_redirect', 'prm_redirect_logged_in_from_portal');

// FIXME Double redirect, it's erasing warning/error messages on the page
// Redirect Users Trying to Manually Access /wp-login.php
function prm_redirect_wp_login()
{
    $is_login_page = $GLOBALS['pagenow'] === 'wp-login.php';

    // Only redirect GET requests to default login page
    if (
        $is_login_page &&
        $_SERVER['REQUEST_METHOD'] === 'GET' &&
        !is_user_logged_in() &&
        (!isset($_GET['action']) || $_GET['action'] === 'login')
    ) {
        wp_redirect(home_url('/partner-portal?tab=login'));
        exit;
    }
}
add_action('init', 'prm_redirect_wp_login');


// Add a Simple REST API Endpoint to Serve Tabs
add_action('rest_api_init', function () {
    register_rest_route('prm/v1', '/tab', [
        'methods' => 'GET',
        'callback' => 'prm_load_tab_content',
        'permission_callback' => '__return_true',
    ]);
});

function prm_load_tab_content($request)
{
    ob_start();
    $tab = sanitize_text_field($request['tab'] ?? 'login');

    // Pass extra query vars to template
    // if (isset($request['support_status'])) {
    //     set_query_var('support_status', sanitize_text_field($request['support_status']));
    // }

    if (in_array($tab, ['login', 'register', 'support'])) {
        get_template_part('template-parts/portal/tab', $tab);
    } else {
        echo '<p>Invalid tab.</p>';
    }

    $html = ob_get_clean();

    return new WP_REST_Response($html, 200);
}


function prm_redirect_on_failed_login($username)
{
    $referrer = wp_get_referer();

    if (!empty($referrer) && strpos($referrer, 'partner-portal') !== false) {
        wp_redirect(add_query_arg('login', 'failed', $referrer));
        // var_dump($referrer);
        exit;
    }
}
add_action('wp_login_failed', 'prm_redirect_on_failed_login');




function prm_enqueue_flatpickr_assets($hook)
{
    global $post;
    if ($hook === 'post-new.php' || $hook === 'post.php') {
        if ($post->post_type === 'events') {
            wp_enqueue_style('flatpickr-css', 'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css');
            wp_enqueue_script('flatpickr-js', 'https://cdn.jsdelivr.net/npm/flatpickr', [], null, true);
            wp_enqueue_script('flatpickr-custom', get_template_directory_uri() . '/assets/js/flatpickr-events.js', ['flatpickr-js'], null, true);
        }
    }
}
add_action('admin_enqueue_scripts', 'prm_enqueue_flatpickr_assets');



// Disable WordPress Admin Bar for Non-Admins
function disable_admin_bar_for_non_admins()
{
    if (!current_user_can('administrator')) {
        show_admin_bar(false);
    }
}
add_action('after_setup_theme', 'disable_admin_bar_for_non_admins');




function my_enqueue_scripts()
{
    // Pass ajaxurl to the script
    wp_localize_script('my-custom-js', 'ajax_object', array(
        'ajaxurl' => admin_url('admin-ajax.php')
    ));
}
add_action('wp_enqueue_scripts', 'my_enqueue_scripts');
