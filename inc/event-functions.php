<?php
wp_enqueue_script('flatpickr', 'https://cdn.jsdelivr.net/npm/flatpickr', [], null, true);
wp_enqueue_style('flatpickr-style', 'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css');


add_action('rest_api_init', function () {
    register_rest_route('prm/v1', '/events', [
        'methods' => 'POST',
        'callback' => 'prm_fetch_events',
        'permission_callback' => '__return_true',
    ]);
});

function prm_fetch_events($request) {
    $params = $request->get_json_params();
    var_dump($params); // Debugging line to check the incoming parameters
    $args = [
        'post_type' => 'events',
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
        'post_type' => 'events',
        // filters here
    ]);
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            get_template_part('template-parts/dashboard/event', 'card');
        }
    } else {
        echo '<p class="text-gray-500 dark:text-gray-400">No events found.</p>';
    }
    $html = ob_get_clean();

    return new WP_REST_Response($html, 200);
}


function register_event_taxonomies() {
    register_taxonomy('event_type', 'events', [
        'label'        => 'Event Types',
        'public'       => true,
        'hierarchical' => true,
        'show_ui'      => true,
        'rewrite'      => ['slug' => 'event-type'],
        'show_in_rest' => true,
    ]);
}
add_action('init', 'register_event_taxonomies');
