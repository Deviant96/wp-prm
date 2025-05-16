<?php
function wp_prm_enqueue_flatpickr()
{
    wp_enqueue_script('flatpickr', 'https://cdn.jsdelivr.net/npm/flatpickr', [], null, true);
    wp_enqueue_style('flatpickr-style', 'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css');
}
add_action('wp_enqueue_scripts', 'wp_prm_enqueue_flatpickr');
// add_action( 'rest_api_init',  'hfm_register_custom_rest_route');

// function hfm_register_custom_rest_route() {
//     register_rest_route( 'prm/v1', '/events', array(
//      'methods'          => 'GET',
//      'callback'         => 'prm_get_events',
//      'permission_callback' => '__return_true',
//     ) );
// }

// function hfm_benchmark_rest_request() {
//     return array( 'time' => time() );
// }

add_action('rest_api_init', function() {
    register_rest_route('prm/v1', '/tbyte_prm_events/create', [
        'methods' => 'POST',
        'callback' => 'create_event_rest',
        'permission_callback' => function() {
            return current_user_can('edit_posts');
        }
    ]);
});

function create_event_rest($request) {
    $params = $request->get_params();

    // Validate and sanitize input
    $event_title = isset($params['event_title']) ? sanitize_text_field($params['event_title']) : ''; // Changed from event_title to event_name
    $event_type = isset($params['event_type']) ? intval($params['event_type']) : 0;
    $venue = isset($params['event_venue']) ? sanitize_text_field($params['event_venue']) : ''; // Changed from venue to event_venue
    $tags = isset($params['event_tags']) ? sanitize_text_field($params['event_tags']) : '';
    $event_date = isset($params['event_date']) ? sanitize_text_field($params['event_date']) : '';
    $start_time = isset($params['start_time']) ? sanitize_text_field($params['start_time']) : '';
    $end_time = isset($params['end_time']) ? sanitize_text_field($params['end_time']) : '';
    $event_status = isset($params['event_status']) ? sanitize_text_field($params['event_status']) : 'publish';
    $event_url = isset($params['event_url']) ? esc_url_raw($params['event_url']) : '';
    $event_description = isset($params['event_description']) ? sanitize_textarea_field($params['event_description']) : '';

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
        'post_content' => $event_description,
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
    update_post_meta($post_id, '_event_date', $event_date); // Added underscore prefix for consistency
    update_post_meta($post_id, '_event_venue', $venue);
    update_post_meta($post_id, '_event_start_time', $start_time);
    update_post_meta($post_id, '_event_end_time', $end_time);
    update_post_meta($post_id, '_event_url', $event_url);
    update_post_meta($post_id, '_event_status', $event_status); // Consider using post_status instead
    
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

/*
 * GET Events
 * This function fetches events based on the provided parameters.
 * It uses WP_Query to retrieve the events and returns the HTML for the events list.
 */
// add_action('rest_api_init', function () {
//     register_rest_route('prm/v1', '/events', [
//         'methods' => 'GET',
//         'callback' => 'prm_get_eventss',
//         'permission_callback' => '__return_true',
//     ]);
// });
// function prm_get_events($args = []) {
//     $defaults = [
//         'post_type' => 'events',
//         'posts_per_page' => 9,
//         'paged' => 1,
//     ];
//     $args = wp_parse_args($args, $defaults);
//     $query = new WP_Query($args);

//     ob_start();
//     if ($query->have_posts()) {
//         while ($query->have_posts()) {
//             $query->the_post();
//             get_template_part('template-parts/dashboard/event', 'card');
//         }
//     } else {
//         echo '<p class="text-gray-500 dark:text-gray-400">No events found.</p>';
//     }
//     wp_reset_postdata();
//     return ob_get_clean();
// }

add_action('rest_api_init', function () {
    register_rest_route('prm/v1', '/tbyte_prm_events', [
        'methods' => 'POST',
        'callback' => 'tbyte_prm_fetch_events',
        'permission_callback' => '__return_true',
    ]);
});

function tbyte_prm_fetch_events($request) {
    $params = $request->get_json_params();

    // Validate and sanitize input
    $search = isset($params['search']) ? sanitize_text_field($params['search']) : '';
    $page = isset($params['page']) ? absint($params['page']) : 1;
    $per_page = isset($params['posts_per_page']) ? absint($params['posts_per_page']) : 10;
    
    $args = [
        'post_type' => 'tbyte_prm_events',
        'posts_per_page' => $per_page,
        'paged' => $page,
        's' => $search,
        'tax_query' => ['relation' => 'AND'],
        'meta_query' => [],
    ];

    // Taxonomy filters
    if (!empty($params['filters']) && is_array($params['filters'])) {
        $tax_queries = [];
        
        foreach ($params['filters'] as $filter) {
            if (!isset($filter['tax']) || !isset($filter['value'])) continue;
            
            $taxonomy = sanitize_text_field($filter['tax']);
            $term = sanitize_text_field($filter['value']);
            
            if (taxonomy_exists($taxonomy) && term_exists($term, $taxonomy)) {
                $tax_queries[$taxonomy][] = $term;
            }
        }
        
        foreach ($tax_queries as $tax => $terms) {
            $args['tax_query'][] = [
                'taxonomy' => $tax,
                'field' => 'slug',
                'terms' => $terms,
                'operator' => 'IN',
            ];
        }
    }

    // Date range filter
    if (!empty($params['range'])) {
        $dates = $params['range'];
        
        if (count($dates) === 2) {
            $start_date = $dates[0];
            $end_date = $dates[1];
            
            if ($start_date && $end_date) {
                $args['meta_query'][] = [
                    'key' => '_event_date',
                    'value' => [$start_date, $end_date],
                    'compare' => 'BETWEEN',
                    'type' => 'DATE'
                ];
                
                // Optional: Order by event date
                $args['meta_key'] = '_event_date';
                $args['orderby'] = 'meta_value_num';
                $args['order'] = 'ASC';
            }
        }
    }

    $query = new WP_Query($args);

    if (is_wp_error($query)) {
        return new WP_Error('query_failed', 'Failed to retrieve events: ' . $query->get_error_message(), ['status' => 500]);
    }

    if ($query->have_posts()) {
        $data = [];
        
        while ($query->have_posts()) {
            $query->the_post();
            $post_id = get_the_ID();
            
            $data[] = [
                'id' => $post_id,
                'title' => get_the_title(),
                'link' => get_permalink(),
                'excerpt' => get_the_excerpt(),
                'thumbnail' => get_the_post_thumbnail_url($post_id, 'medium'),
                'type' => wp_get_post_terms($post_id, 'event_type', ['fields' => 'names']),
                'tags' => wp_get_post_terms($post_id, 'post_tag', ['fields' => 'names']),
                'date' => get_post_meta($post_id, '_event_date', true),
                'formatted_date' => date_i18n(get_option('date_format'), get_post_meta($post_id, '_event_date', true)),
                'venue' => get_post_meta($post_id, '_event_venue', true),
                'location' => get_post_meta($post_id, '_event_location', true),
            ];
        }
        
        $response = [
            'items' => $data,
            'pagination' => [
                'total' => (int) $query->found_posts,
                'total_pages' => (int) $query->max_num_pages,
                'current_page' => $page,
                'per_page' => $per_page,
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
                'current_page' => $page,
                'per_page' => $per_page,
            ],
        ];
    }

    wp_reset_postdata();
    return new WP_REST_Response($response, 200);
}


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
    // Get query parameters from the REST request
    $paged = (int) $request->get_param('page');
    $paged = $paged > 0 ? $paged : 1;

    $posts_per_page = (int) $request->get_param('posts_per_page');
    $posts_per_page = ($posts_per_page > 0 && $posts_per_page <= 50) ? $posts_per_page : 10;
    // var_dump($posts_per_page);
    $events_query = new WP_Query([
        'post_type' => 'tbyte_prm_events',
        'posts_per_page' => $posts_per_page,
        'paged' => $paged,
        'post_status' => 'publish',
    ]);

    if (is_wp_error($events_query)) {
        wp_send_json_error([
            'message' => 'Failed to retrieve assets.',
            'error' => $events_query->get_error_message(),
        ], 500);
    }

    $data = [];
    while ($events_query->have_posts()) {
        $events_query->the_post();
        $data[] = [
            'id'    => get_the_ID(),
            'title' => get_the_title(),
            'link'  => get_permalink(),
            'type' => wp_get_post_terms(get_the_ID(), 'event_type', ['fields' => 'names']),
            'tags' => wp_get_post_terms(get_the_ID(), 'post_tag', ['fields' => 'names']),
            'date'  => get_post_meta(get_the_ID(), '_event_date', true),
            'venue' => get_post_meta(get_the_ID(), '_event_venue', true),
        ];
    }
    wp_reset_postdata();

    if (empty($data)) {
        return rest_ensure_response([
            'items' => [], 
            'message' => 'No events found',
            'pagination' => [
                'total' => 0,
                'total_pages' => 0,
                'current_page' => $paged,
                'per_page' => $posts_per_page,
            ],
        ]);
    }

    $pagination = [
        'total' => (int) $events_query->found_posts,
        'total_pages' => (int) $events_query->max_num_pages,
        'current_page' => (int) $paged,
        'per_page' => (int) $posts_per_page,
    ];

    return rest_ensure_response([
        'items' => $data,
        'pagination' => $pagination,
    ]);
}

// GET a single event
function tbyte_prm_get_event($request) {
    $post_id = (int)$request['id'];
    $post = get_post($post_id);

    if (!$post || $post->post_type !== 'tbyte_prm_events') {
        return new WP_Error('not_found', 'Event not found', ['status' => 404]);
    }

    return [
        'id'    => $post->ID,
        'title' => $post->post_title,
        'date'  => get_post_meta($post->ID, 'event_date', true),
        'venue' => get_post_meta($post->ID, 'event_venue', true),
    ];
}

// CREATE an event
function tbyte_prm_create_event($request) {
    $post_id = wp_insert_post([
        'post_type'   => 'tbyte_prm_events',
        'post_title'  => sanitize_text_field($request['title']),
        'post_status' => 'publish',
    ]);

    if (is_wp_error($post_id)) {
        return $post_id;
    }

    update_post_meta($post_id, 'event_date', sanitize_text_field($request['date']));
    update_post_meta($post_id, 'event_venue', sanitize_text_field($request['venue']));

    return ['message' => 'Event created', 'id' => $post_id];
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

    return [
        'success' => true,
        'message' => 'Event deleted',
    ];
}
