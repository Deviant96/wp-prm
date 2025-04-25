<?php
// Register 'doc_type' taxonomy for 'asset' CPT
function register_doc_type_taxonomy() {
    $labels = array(
        'name'              => 'Document Types',
        'singular_name'     => 'Document Type',
        'search_items'      => 'Search Document Types',
        'all_items'         => 'All Document Types',
        'parent_item'       => 'Parent Document Type',
        'parent_item_colon' => 'Parent Document Type:',
        'edit_item'         => 'Edit Document Type',
        'update_item'       => 'Update Document Type',
        'add_new_item'      => 'Add New Document Type',
        'new_item_name'     => 'New Document Type Name',
        'menu_name'         => 'Document Type',
    );

    $args = array(
        'hierarchical'      => true, // Makes it behave like categories
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'doc-type'),
    );

    register_taxonomy('doc_type', array('assets'), $args);
}
add_action('init', 'register_doc_type_taxonomy');

// Register 'language' taxonomy for 'asset' CPT
function register_language_taxonomy() {
    $labels = array(
        'name'              => 'Languages',
        'singular_name'     => 'Language',
        'search_items'      => 'Search Languages',
        'all_items'         => 'All Languages',
        'parent_item'       => 'Parent Language',
        'parent_item_colon' => 'Parent Language:',
        'edit_item'         => 'Edit Language',
        'update_item'       => 'Update Language',
        'add_new_item'      => 'Add New Language',
        'new_item_name'     => 'New Language Name',
        'menu_name'         => 'Language',
    );

    $args = array(
        'hierarchical'      => true, // Makes it behave like categories
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'language'),
    );

    register_taxonomy('language', array('assets'), $args);
}
add_action('init', 'register_language_taxonomy');



function prm_ajax_load_assets() {
    check_ajax_referer('prm_ajax_nonce', 'nonce');

    $layout = $_POST['layout'] ?? 'grid';
    $search = sanitize_text_field($_POST['search'] ?? '');
    $doc_type = $_POST['doc_type'] ?? [];
    $language = $_POST['language'] ?? [];
    $paged = max(1, intval($_POST['page'] ?? 1));
    $posts_per_page = 6;

    $tax_query = [];
    $args = [
        'post_type' => 'assets',
        's' => $search,
        'posts_per_page' => $posts_per_page,
        'paged' => $paged
    ];
    
    if (!empty($doc_type)) {
        $tax_query[] = [
            'taxonomy' => 'doc_type',
            'field'    => 'slug',
            'terms'    => is_array($doc_type) ? $doc_type : explode(',', $doc_type),
            'operator' => 'IN',
        ];
    }

    if (!empty($language)) {
        $tax_query[] = [
            'taxonomy' => 'language',
            'field'    => 'slug',
            'terms'    => is_array($language) ? $language : explode(',', $language),
            'operator' => 'IN',
        ];
    }

    if (!empty($tax_query)) {
        $args['tax_query'] = array_merge(
            ['relation' => 'OR'],
            $tax_query
        );
    }

    $query = new WP_Query($args);
    ob_start();

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            get_template_part('template-parts/dashboard/asset', 'card', [
                'layout' => $layout
            ]);
        }
    } else {
        // var_dump($args);
        echo '<p class="text-gray-500 dark:text-gray-300">No assets found.</p>';
    }

    $html = ob_get_clean();
    wp_reset_postdata();

    wp_send_json_success([
        'html' => $html,
        'max_pages' => $query->max_num_pages,
        'total_posts' => $query->found_posts,
    ]);
}
add_action('wp_ajax_prm_load_assets', 'prm_ajax_load_assets');
add_action('wp_ajax_nopriv_prm_load_assets', 'prm_ajax_load_assets');