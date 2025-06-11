<?php
function wp_prm_enqueue_flatpickr() {
    wp_enqueue_script('flatpickr', 'https://cdn.jsdelivr.net/npm/flatpickr', [], null, true);
    wp_enqueue_style('flatpickr-style', 'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css');
}
add_action('wp_enqueue_scripts', 'wp_prm_enqueue_flatpickr');

function register_event_taxonomies() {
    register_taxonomy('event_type', 'tbyte_prm_events', [
        'label'        => 'Event Types',
        'public'       => true,
        'hierarchical' => true,
        'show_ui'      => true,
        'rewrite'      => ['slug' => 'event-type'],
        'show_in_rest' => true,
    ]);
}
add_action('init', 'register_event_taxonomies');

add_action('rest_api_init', function () {
    register_rest_route('prm/v1', '/tbyte_prm_events', [
        'methods'  => 'GET',
        'callback' => 'tbyte_prm_get_events',
        'permission_callback' => '__return_true',
    ]);

    register_rest_route('prm/v1', '/tbyte_prm_events/(?P<id>\d+)', [
        'methods'  => 'GET',
        'callback' => 'tbyte_prm_get_event',
        'permission_callback' => '__return_true',
    ]);

    register_rest_route('prm/v1', '/tbyte_prm_events', [
        'methods'  => 'POST',
        'callback' => 'tbyte_prm_create_event',
        'permission_callback' => 'tbyte_prm_can_manage_events',
    ]);

    register_rest_route('prm/v1', '/tbyte_prm_events/(?P<id>\d+)', [
        'methods'  => 'POST',
        'callback' => 'tbyte_prm_update_event',
        'permission_callback' => 'tbyte_prm_can_manage_events',
    ]);

    register_rest_route('prm/v1', '/tbyte_prm_events/(?P<id>\d+)', [
        'methods'  => 'DELETE',
        'callback' => 'tbyte_prm_delete_event',
        'permission_callback' => 'tbyte_prm_can_manage_events',
    ]);
});

// Check permissions
function tbyte_prm_can_manage_events() {
    return current_user_can('edit_posts');
}

// GET all events
function tbyte_prm_get_events($request) {
    $params = $request->get_params();

    $paged = (int) $request->get_param('page');
    $paged = $paged > 0 ? $paged : 1;

    $posts_per_page = (int) $request->get_param('posts_per_page');
    $posts_per_page = $posts_per_page > 0 ? $posts_per_page : 10;
    $posts_per_page = min($posts_per_page, 20);
    $posts_per_page = max($posts_per_page, 1);

    $search = $request->get_param('search');
    $search = !empty($search) ? sanitize_text_field($search) : '';

    $search = preg_replace('/[^a-zA-Z0-9\s]/', '', $search); // Sanitize search input

    $filters = !empty($params['filters']) ? json_decode($params['filters'], true) : [];
    $filters = is_array($filters) ? $filters : [];

    $start_date = !empty($params['start_date']) ? sanitize_text_field($params['start_date']) : '';
    $end_date = !empty($params['end_date']) ? sanitize_text_field($params['end_date']) : '';

    $events_query = [
        'post_type' => 'tbyte_prm_events',
        'posts_per_page' => $posts_per_page,
        'paged' => $paged,
        'post_status' => 'publish',
        's' => $search,
        'tax_query' => [
            'relation' => 'AND',
        ],
        'meta_query' => [],
    ];
 
    // Taxonomy filters
    if (!empty($params['filters']) && is_array($filters)) {
        $tax_queries = [];

        foreach ($filters as $filter) {
            if (!isset($filter['tax']) || !isset($filter['value'])) continue;

            $taxonomy = sanitize_text_field($filter['tax']);
            $term = sanitize_text_field($filter['value']);

            if (taxonomy_exists($taxonomy) && term_exists($term, $taxonomy)) {
                $tax_queries[$taxonomy][] = $term;
            }
        }

        foreach ($tax_queries as $tax => $terms) {
            $events_query['tax_query'][] = [
                'taxonomy' => $tax,
                'field' => 'slug',
                'terms' => $terms,
                'operator' => 'IN',
            ];
        }
    }
    
    // Date range filter
    if (!empty($start_date) && !empty($end_date)) {
        $start_date = date('Y-m-d', strtotime($start_date));
        $end_date = date('Y-m-d', strtotime($end_date));

        $events_query['meta_query'][] = [
            'key' => '_event_date',
            'value' => [$start_date, $end_date],
            'compare' => 'BETWEEN',
            'type' => 'DATE'
        ];
    }

    $query = new WP_Query($events_query);

    if (is_wp_error($events_query)) {
        return new WP_Error('query_failed', 'Failed to retrieve events: ' . $query->get_error_message(), ['status' => 500]);
    }

    $data = [];
    if ($query->have_posts()) {
        $data = [];

        while ($query->have_posts()) {
            $query->the_post();
            $data[] = [
                'id'    => get_the_ID(),
                'title' => get_the_title(),
                'link'  => get_permalink(),

                'start_date' => get_post_meta(get_the_ID(), '_event_start_date', true),
                'end_date' => get_post_meta(get_the_ID(), '_event_end_date', true),
                'start_time' => get_post_meta(get_the_ID(), '_event_start_time', true),
                'end_time' => get_post_meta(get_the_ID(), '_event_end_time', true),
                
                'venue' => get_post_meta(get_the_ID(), '_event_venue', true),
                'event_url' => get_post_meta(get_the_ID(), '_event_url', true),
                'event_status' => get_post_meta(get_the_ID(), '_event_status', true),
                'image' => get_post_meta(get_the_ID(), '_event_image', true),

                'cost' => get_post_meta(get_the_ID(), '_event_cost', true),
                'currency_symbol' => get_post_meta(get_the_ID(), '_event_currency_symbol', true),
                'currency_code' => get_post_meta(get_the_ID(), '_event_currency_code', true),
                'currency_position' => get_post_meta(get_the_ID(), '_event_currency_position', true),
                
                'is_featured' => get_post_meta(get_the_ID(), '_event_is_featured', true),
                'hide_from_listings' => get_post_meta(get_the_ID(), '_event_hide_from_listings', true),
                'show_map' => get_post_meta(get_the_ID(), '_event_show_map', true),
                'show_map_link' => get_post_meta(get_the_ID(), '_event_show_map_link', true),

                'tags' => wp_get_post_terms(get_the_ID(), 'post_tag', ['fields' => 'names']),
                'date'  => get_post_meta(get_the_ID(), '_event_date', true),
                'formatted_date' => date_i18n(get_option('date_format'), get_post_meta(get_the_ID(), '_event_date', true)),
                'location' => get_post_meta(get_the_ID(), '_event_location', true),
            ];
        }

        $response = [
            'items' => json_encode($data),
            'pagination' => [
                'total' => (int) $query->found_posts,
                'total_pages' => (int) $query->max_num_pages,
                'current_page' => (int) $paged,
                'per_page' => (int) $posts_per_page,
            ],
            'message' => 'Events retrieved successfully',
        ];
    } else {
        $response = [
            'items' => [],
            'message' => 'No events found matching your criteria',
            'pagination' => [
                'total' => 0,
                'total_pages' => 0,
                'current_page' => (int) $paged,
                'per_page' => (int) $posts_per_page,
            ],
        ];
    }
    
    // var_dump($events_query);
    
    wp_reset_postdata();
    return new WP_REST_Response($response, 200);
}

// GET a single event
function tbyte_prm_get_event($request) {
    $post_id = (int)$request['id'];
    $post = get_post($post_id);

    if (!$post || $post->post_type !== 'tbyte_prm_events') {
        return new WP_Error('not_found', 'Event not found', ['status' => 404]);
    }

    return [
        'id' => $post->ID,
        'title' => $post->post_title,
        'link' => get_permalink($post->ID),
        'content' => apply_filters('the_content', $post->post_content),
        'status' => $post->post_status,
        'post_date' => $post->post_date,

        'start_date' => get_post_meta($post->ID, '_event_start_date', true),
        'end_date' => get_post_meta($post->ID, '_event_end_date', true),
        'start_time' => get_post_meta($post->ID, '_event_start_time', true),
        'end_time' => get_post_meta($post->ID, '_event_end_time', true),

        'venue' => get_post_meta($post->ID, '_event_venue', true),
        'event_url' => get_post_meta($post->ID, '_event_url', true),
        'event_status' => get_post_meta($post->ID, '_event_status', true),
        'image' => get_post_meta($post->ID, '_event_image_url', true),
        'categories' => wp_list_pluck(get_post_meta($post->ID, '_event_categories')[0], 'name'),

        'cost' => get_post_meta($post->ID, '_event_cost', true),
        'currency_symbol' => get_post_meta($post->ID, '_event_currency_symbol', true),
        'currency_code' => get_post_meta($post->ID, '_event_currency_code', true),
        'currency_position' => get_post_meta($post->ID, '_event_currency_position', true),

        'is_featured' => get_post_meta($post->ID, '_event_is_featured', true),
        'hide_from_listings' => get_post_meta($post->ID, '_event_hide_from_listings', true),
        'show_map' => get_post_meta($post->ID, '_event_show_map', true),
        'show_map_link' => get_post_meta($post->ID, '_event_show_map_link', true),

        'tags' => wp_get_post_terms($post->ID, 'post_tag', ['fields' => 'names']),
        'date' => get_post_meta($post->ID, '_event_date', true),
        'formatted_date' => date_i18n(get_option('date_format'), get_post_meta($post->ID, '_event_date', true)),
        'location' => get_post_meta($post->ID, '_event_location', true),
        'metas' => get_post_meta($post->ID),
    ];
}

// CREATE an event
function tbyte_prm_create_event($request) {
    $params = $request->get_params();

    // Log request data
    $log_file = ABSPATH . 'wp-content/uploads/event-requests.log';
    $log_data = date('Y-m-d H:i:s') . " - " . json_encode($params) . "\n";
    file_put_contents($log_file, $log_data, FILE_APPEND);

    // return;

    $is_data_from_website = isset($params['is_data_from_website']) ? $params['is_data_from_website'] : false;

    // Validate and sanitize input
    $event_title = isset($params['event_title']) ? sanitize_text_field($params['event_title']) : ''; // Changed from event_title to event_name
    $event_type = isset($params['event_type']) ? intval($params['event_type']) : 0;
    $venue = isset($params['event_venue']) ? sanitize_text_field($params['event_venue']) : ''; // Changed from venue to event_venue
    $tags = isset($params['event_tags']) ? sanitize_text_field($params['event_tags']) : '';
    
    if ( $is_data_from_website == 1 ) {
        $event_image = isset($params['featured_image']) ? esc_url_raw($params['featured_image']) : '';
    } else {
        $event_image = isset($params['event_image']) ? esc_url_raw($params['event_image']) : '';
    }

    $event_start_date_utc = isset($params['event_start_date_utc']) ? sanitize_text_field($params['event_start_date_utc']) : '';
    $event_end_date_utc = isset($params['event_end_date_utc']) ? sanitize_text_field($params['event_end_date_utc']) : '';
    $start_time_utc = isset($params['event_start_time_utc']) ? sanitize_text_field($params['event_start_time_utc']) : '';
    $end_time_utc = isset($params['event_end_time_utc']) ? sanitize_text_field($params['event_end_time_utc']) : '';
    $event_timezone = isset($params['event_timezone']) ? sanitize_text_field($params['event_timezone']) : '';

    $event_cost = isset($params['event_cost']) ? sanitize_text_field($params['event_cost']) : '';
    $event_currency_symbol = isset($params['event_currency_symbol']) ? sanitize_text_field($params['event_currency_symbol']) : '';
    $event_currency_code = isset($params['event_currency_code']) ? sanitize_text_field($params['event_currency_code']) : '';
    $event_currency_position = isset($params['event_currency_position']) ? sanitize_text_field($params['event_currency_position']) : '';

    $event_is_featured = isset($params['is_featured']) ? (bool)$params['is_featured'] : false;
    $event_hide_from_listings = isset($params['hide_from_listings']) ? sanitize_text_field($params['hide_from_listings']) : 'no';
    $event_show_map = isset($params['show_map']) ? sanitize_text_field($params['show_map']) : '0';
    $event_show_map_link = isset($params['show_map_link']) ? sanitize_text_field($params['show_map_link']) : '0';
    
    $event_status = isset($params['event_status']) ? sanitize_text_field($params['event_status']) : 'publish';
    $event_url = isset($params['event_url']) ? esc_url_raw($params['event_url']) : '';



    $event_date = isset($params['event_date']) ? sanitize_text_field($params['event_date']) : '';
    $event_content = isset($params['event_content']) ? sanitize_textarea_field($params['event_content']) : '';

    // Validation
    if (empty($event_title)) {
        return new WP_Error('creation_failed', 'Event title is required.', ['status' => 400]);
    }

    if (empty($event_date)) {
        return new WP_Error('creation_failed', 'Event date is required.', ['status' => 400]);
    }

    // Prepare post data
    $post_data = [
        'post_title'   => $event_title,
        'post_content' => $event_content,
        'post_status'  => $event_status,
        'post_type'    => 'tbyte_prm_events',
        'post_date'    => current_time('mysql'), // Set current date as publish date
    ];

    // Create the post
    $post_id = wp_insert_post($post_data, true);

    if (is_wp_error($post_id)) {
        return new WP_Error('creation_failed', $post_id->get_error_message(), ['status' => 500]);
    }

    // Save meta fields
    update_post_meta($post_id, '_event_start_date', $event_start_date_utc);
    update_post_meta($post_id, '_event_end_date', $event_end_date_utc);
    update_post_meta($post_id, '_event_start_time', $start_time_utc);
    update_post_meta($post_id, '_event_end_time', $end_time_utc);
    update_post_meta($post_id, '_event_timezone', $event_timezone);

    update_post_meta($post_id, '_event_venue', $venue);
    update_post_meta($post_id, '_event_url', $event_url);
    update_post_meta($post_id, '_event_status', $event_status);
    update_post_meta($post_id, '_event_image_url', $event_image);
    update_post_meta($post_id, '_event_categories', isset($params['categories'] ) ? $params['categories'] : '');

    update_post_meta($post_id, '_event_cost', isset($event_cost) ? $event_cost : '');
    update_post_meta($post_id, '_event_currency_symbol', isset($event_currency_symbol) ? $event_currency_symbol : '');
    update_post_meta($post_id, '_event_currency_code', isset($event_currency_code) ? $event_currency_code : '');
    update_post_meta($post_id, '_event_currency_position', isset($event_currency_position) ? $event_currency_position : '');

    update_post_meta($post_id, '_event_is_featured', isset($event_is_featured) ? $event_is_featured : '');
    update_post_meta($post_id, '_event_hide_from_listings', isset($event_hide_from_listings) ? $event_hide_from_listings : '');
    update_post_meta($post_id, '_event_show_map', isset($event_show_map) ? $event_show_map : '');
    update_post_meta($post_id, '_event_show_map_link', isset($event_show_map_link) ? $event_show_map_link : '');
    
    // Set event type term if provided
    if ($event_type > 0) {
        wp_set_object_terms($post_id, [$event_type], 'event_type', false);
    }

    // Handle tags
    if (!empty($tags)) {
        $tags_array = array_map('trim', explode(',', $tags));
        wp_set_post_tags($post_id, $tags_array, false);
    }

    // Flush rewrite rules if needed (only do this once, not on every request)
    // flush_rewrite_rules(false);

    // Return success response
    return new WP_REST_Response([
        'success' => true,
        'message' => 'Event created successfully!',
        'post_id' => $post_id,
        'edit_link' => get_edit_post_link($post_id, '')
    ], 200);
}

// UPDATE an event
function tbyte_prm_update_event($request) {
    $post_id = (int)$request['id'];

    $post = get_post($post_id);
    if (!$post || $post->post_type !== 'tbyte_prm_events') {
        return new WP_Error('not_found', 'Event not found', ['status' => 404]);
    }

    wp_update_post([
        'ID'         => $post_id,
        'post_title' => sanitize_text_field($request['title']),
    ]);

    update_post_meta($post_id, 'event_date', sanitize_text_field($request['date']));
    update_post_meta($post_id, 'event_venue', sanitize_text_field($request['venue']));

    return ['message' => 'Event updated'];
}

// DELETE an event
function tbyte_prm_delete_event($request) {
    $post_id = (int)$request['id'];
    $deleted = wp_delete_post($post_id, true);

    if (!$deleted) {
        return new WP_Error('delete_failed', 'Failed to delete event', ['status' => 400]);
    }

    $response = [
        'success' => true,
        'message' => 'Event deleted',
    ];

    return new WP_REST_Response($response, 200);
}





















/* Backend Sync Handler */
add_action('admin_post_sync_event', 'handle_event_sync');
function handle_event_sync() {
    $sync_errors = [];
    $base_domains = [
        'sg' => 'https://www.terrabytegroup.com',
        'id' => 'https://id.terrabytegroup.com',
        // Add other regions...
    ];

    if (!empty($_POST['regions']) && !empty($_POST['event_title'])) {
        foreach ($_POST['regions'] as $region) {
            $api_url = $base_domains[$region] . '/wp-json/tribe/events/v1/events';
            
            $response = wp_remote_post($api_url, [
                'headers' => [
                    'Authorization' => 'Basic ' . base64_encode('username:application_password'),
                    'Content-Type' => 'application/json'
                ],
                'body' => json_encode([
                    'title' => sanitize_text_field($_POST['event_title']),
                    'start_date' => sanitize_text_field($_POST['start_date']),
                    'end_date' => sanitize_text_field($_POST['end_date']),
                    'status' => 'publish'
                ]),
                'timeout' => 15
            ]);

            if (is_wp_error($response)) {
                $sync_errors[] = "{$region}: " . $response->get_error_message();
            } elseif (wp_remote_retrieve_response_code($response) !== 201) {
                $body = json_decode(wp_remote_retrieve_body($response), true);
                $sync_errors[] = "{$region}: " . ($body['message'] ?? 'Unknown error');
            }
        }
    }

    if (!empty($sync_errors)) {
        set_transient('event_sync_errors', $sync_errors, 30);
    }
    
    wp_redirect(wp_get_referer());
    exit;
}

/* Webhook Listener for Incoming Events */
add_action('rest_api_init', function() {
    register_rest_route('dashboard/v1', '/sync-event', [
        'methods' => 'POST',
        'callback' => 'handle_incoming_event',
        'permission_callback' => '__return_true'
    ]);
});

function handle_incoming_event(WP_REST_Request $request) {
    $params = $request->get_params();
    $checksum = md5(serialize($params));
    
    // Conflict check
    $existing = get_posts([
        'post_type' => 'tribe_events',
        'meta_query' => [[
            'key' => 'event_checksum',
            'value' => $checksum
        ]]
    ]);

    if (!empty($existing)) {
        return new WP_Error('conflict', 'Event already exists', ['status' => 409]);
    }

    // $event_id = wp_insert_post([
    //     'post_title' => sanitize_text_field($params['title']),
    //     'post_type' => 'tribe_events',
    //     'post_status' => 'publish',
    //     'meta_input' => [
    //         '_EventStartDate' => $params['start_date'],
    //         '_EventEndDate' => $params['end_date'],
    //         'event_checksum' => $checksum,
    //         'source_region' => sanitize_text_field($params['region'])
    //     ]
    // ]);

    $event = tribe_events()
    ->set_args( [
        'title'      => sanitize_text_field($params['title']),
        'event_date' => '+2 days 15:00:00',
        'duration'   => HOUR_IN_SECONDS,
        'status'     => 'publish',
    ] )
    ->create();

    return $event ?: new WP_Error('creation_failed', 'Event creation failed');
}