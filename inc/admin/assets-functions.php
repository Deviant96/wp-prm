<?php
add_action('wp_ajax_save_asset', 'prm_save_asset');
function prm_save_asset() {
    // Validate and save asset (as custom post type)
    $post_id = wp_insert_post([
        'post_title' => sanitize_text_field($_POST['asset_name']),
        'post_type' => 'prm_asset',
        'post_status' => 'publish',
    ]);

    // Meta (simplified for now)
    if ($post_id && !is_wp_error($post_id)) {
        update_post_meta($post_id, 'asset_url', esc_url_raw($_POST['asset_url']));
        update_post_meta($post_id, 'asset_tags', sanitize_text_field($_POST['asset_tags']));
        // TODO: Handle file upload
        wp_send_json_success(['message' => 'Asset saved']);
    }

    wp_send_json_error(['message' => 'Failed to save asset']);
}

add_action('wp_ajax_get_assets', 'get_assets');
function get_assets() {
    $paged = isset($_GET['paged']) ? intval($_GET['paged']) : 1;
    if($paged < 1) {
        $paged = 1;
    }

    $posts_per_page = isset($_GET['posts_per_page']) ? intval($_GET['posts_per_page']) : 10;
    $posts_per_page = ($posts_per_page > 0 && $posts_per_page <= 50) ? $posts_per_page : 10;

    $query_args = [
        'post_type' => 'assets', 
        'posts_per_page' => $posts_per_page,
        'paged' => $paged,
        'post_status' => 'publish',
        // 's' => isset($_GET['search']) ? sanitize_text_field($_GET['search']) : '',
    ];

    $assets_query = new WP_Query($query_args);

    if (is_wp_error($assets_query)) {
        wp_send_json_error([
            'message' => 'Failed to retrieve assets.',
            'error' => $assets_query->get_error_message(),
        ], 500);
    }

    if (!$assets_query->have_posts()) {
        wp_send_json_error([
            'message' => 'No assets found',
            'assets' => [],
            'pagination' => [
                'total' => 0,
                'total_pages' => 0,
                'current_page' => $paged,
                'per_page' => $posts_per_page,
            ],
        ]);
    }

    $assets = [];

    while ($assets_query->have_posts()) {
        $assets_query->the_post();

        $assets[] = [
            'id' => get_the_ID(),
            'title' => wp_strip_all_tags(get_the_title()),
            'url' => esc_url(get_post_meta(get_the_ID(), 'asset_url', true)),
            // 'tags' => wp_get_post_terms(get_the_ID(), 'prm_asset_tag', ['fields' => 'names']),
            'doc_types' => wp_get_post_terms(get_the_ID(), 'doc_type', ['fields' => 'names']),
            'languages' => wp_get_post_terms(get_the_ID(), 'language', ['fields' => 'names']),
            'date' => get_the_date(),
        ];
    }
    wp_reset_postdata();

    $pagination = [
        'total' => (int) $assets_query->found_posts,
        'total_pages' => (int) $assets_query->max_num_pages,
        'current_page' => (int) $paged,
        'per_page' => (int) $posts_per_page,
    ];

    wp_send_json_success([
        'assets' => $assets,
        'pagination' => $pagination,
    ]);
}

add_action('wp_ajax_delete_asset', 'handle_delete_asset');
function handle_delete_asset() {
    // Check nonce for security (add a nonce field in the AJAX request)
    check_ajax_referer('delete_asset_nonce', 'security');

    // Check user capability (adjust capability as needed)
    if (!current_user_can('delete_posts')) {
        wp_send_json_error(['message' => 'Unauthorized']);
        return;
    }

    // Validate and sanitize input
    $post_id = isset($_POST['id']) ? absint($_POST['id']) : 0;
    if (!$post_id || get_post_type($post_id) !== 'assets') {
        wp_send_json_error(['message' => 'Invalid asset']);
        return;
    }

    // Attempt to delete post
    $deleted = wp_delete_post($post_id, true);
    if ($deleted) {
        wp_send_json_success(['message' => 'Asset deleted']);
    } else {
        wp_send_json_error(['message' => 'Failed to delete asset']);
    }
}