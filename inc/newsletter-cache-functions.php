<?php
// Register REST API endpoints
add_action('rest_api_init', function() {
    register_rest_route('cache-control/v1', '/refresh', [
        'methods' => 'POST',
        'callback' => 'handle_cache_refresh',
        'permission_callback' => function() {
            return current_user_can('manage_options');
        }
    ]);

    register_rest_route('cache-control/v1', '/clear', [
        'methods' => 'POST',
        'callback' => 'handle_cache_clear',
        'permission_callback' => function() {
            return current_user_can('manage_options');
        }
    ]);

    register_rest_route('cache-control/v1', '/settings', [
        'methods' => 'POST',
        'callback' => 'handle_cache_settings',
        'permission_callback' => function() {
            return current_user_can('manage_options');
        }
    ]);
});

function handle_cache_refresh(WP_REST_Request $request) {
    $result = do_cache_refresh();
    if (is_wp_error($result)) {
        return new WP_REST_Response(['success' => false, 'message' => $result->get_error_message()], 400);
    }
    return new WP_REST_Response(['success' => true, 'message' => 'Cache refreshed', 'last_updated' => time()]);
}

function handle_cache_clear(WP_REST_Request $request) {
    $result = do_cache_clear();
    return new WP_REST_Response(['success' => (bool)$result, 'message' => $result ? 'Cache cleared' : 'Cache was already empty']);
}

function handle_cache_settings(WP_REST_Request $request) {
    $duration = $request->get_param('duration');
    $result = update_cache_duration($duration);
    if (is_wp_error($result)) {
        return new WP_REST_Response(['success' => false, 'message' => $result->get_error_message()], 400);
    }
    return new WP_REST_Response(['success' => true, 'message' => 'Settings updated']);
}

function do_cache_refresh() {
    $cache_key = 'terrabyte_newsletters_cache';
    $api_args = [
        'per_page' => 3,
        'categories' => '51,150',
        '_fields' => 'id,title,featured_media,link' // Only request needed fields
    ];
    
    $response = wp_remote_get('https://www.terrabytegroup.com/wp-json/wp/v2/posts?' . http_build_query($api_args));
    
    if (is_wp_error($response)) {
        return new WP_Error('api_error', 'Failed to fetch posts: ' . $response->get_error_message());
    }

    $status_code = wp_remote_retrieve_response_code($response);
    if ($status_code !== 200) {
        return new WP_Error('api_error', 'API returned status: ' . $status_code);
    }

    $data = json_decode(wp_remote_retrieve_body($response));
    if (empty($data)) {
        return new WP_Error('no_data', 'No posts returned from API');
    }
    
    // Process posts in parallel for better performance
    $posts_with_images = array_map(function($post) {
        if ($post->featured_media) {
            $media_response = wp_remote_get("https://www.terrabytegroup.com/wp-json/wp/v2/media/{$post->featured_media}?_fields=source_url");
            if (!is_wp_error($media_response)) {
                $media_data = json_decode(wp_remote_retrieve_body($media_response));
                $post->image_url = $media_data->source_url ?? null;
            }
        }
        return $post;
    }, $data);

    $cache_duration = get_option('terrabyte_cache_duration', 12 * HOUR_IN_SECONDS);
    set_transient($cache_key, $posts_with_images, $cache_duration);
    update_option('terrabyte_last_updated', time());

    return true;
}

function do_cache_clear() {
    $cache_key = 'terrabyte_newsletters_cache';
    $deleted = delete_transient($cache_key);
    
    // Also clear the last updated time
    delete_option('terrabyte_last_updated');
    
    return $deleted;
}

function update_cache_duration($duration) {
    $duration = floatval($duration);
    
    if ($duration <= 0) {
        return new WP_Error('invalid_duration', 'Duration must be a positive number');
    }

    $seconds = $duration * HOUR_IN_SECONDS;
    $updated = update_option('terrabyte_cache_duration', $seconds);
    
    // Update existing cache with new duration if it exists
    $cache_key = 'terrabyte_newsletters_cache';
    if ($data = get_transient($cache_key)) {
        set_transient($cache_key, $data, $seconds);
    }

    return $updated;
}