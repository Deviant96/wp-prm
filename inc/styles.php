<?php
function enqueue_document_types_admin_assets() { 
    wp_enqueue_style('tbyte-prm-style', get_template_directory_uri() . '/style.css', '', '1.0', 'all');

    if (isset($_GET['tab']) && $_GET['tab'] === 'assets-doc-types') {
        // wp_enqueue_style('document-types-admin', get_stylesheet_directory_uri() . '/assets/css/document-types-admin.css');
        wp_enqueue_script('document-types-admin', get_stylesheet_directory_uri() . '/assets/js/document-types-admin.js', array('jquery'), null, true);
        
        wp_localize_script('document-types-admin', 'documentTypesAdmin', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
        ));
    }

    wp_enqueue_style('tbyte_prm_toast', get_stylesheet_directory_uri() . '/assets/css/toast.css', array(), null, 'all');
}
add_action('wp_enqueue_scripts', 'enqueue_document_types_admin_assets');