<?php
// wp_enqueue_script('flatpickr', 'https://cdn.jsdelivr.net/npm/flatpickr', [], null, true);
// wp_enqueue_style('flatpickr-style', 'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css');


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

// add_action('rest_api_init', function () {
//     register_rest_route('prm/v1', '/events', [
//         'methods' => 'POST',
//         'callback' => 'prm_fetch_events',
//         'permission_callback' => '__return_true',
//     ]);
// });

function tbyte_prm_fetch_events($request) {
    $params = $request->get_json_params();
    
    $args = [
        'post_type' => 'tbyte_prm_events',
        'posts_per_page' => 9,
        's' => $params['search'] ?? '',
        'tax_query' => [],
        'meta_query' => [],
    ];

    // Taxonomy filter
    if (!empty($params['filters'])) {
        foreach ($params['filters'] as $filter) {
            $args['tax_query'][] = [
                'taxonomy' => sanitize_text_field($filter['tax']),
                'field' => 'slug',
                'terms' => sanitize_text_field($filter['value']),
            ];
        }
    }

    // Date range filter
    if (!empty($params['range'])) {
        $dates = explode(" to ", $params['range']);
        if (count($dates) === 2) {
            $args['meta_query'][] = [
                'key' => '_event_date',
                'value' => [strtotime($dates[0]), strtotime($dates[1])],
                'compare' => 'BETWEEN',
                'type' => 'NUMERIC'
            ];
        }
    }

    ob_start();
    $query = new WP_Query([
        'post_type' => 'tbyte_prm_events',
        // filters here
    ]);
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            get_template_part('template-parts/dashboard/events/event', 'card');
        }
    } else {
        echo '<p class="text-gray-500 dark:text-gray-400">No events found.</p>';
    }
    $html = ob_get_clean();

    return new WP_REST_Response($html, 200);
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
    $paged = (int) $request->get_param('paged');
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

    return ['message' => 'Event deleted'];
}
