<?php
function tbyte_prm_register_cpt()
{
    register_post_type('partner_application', [
        'label' => 'Partner Applications',
        'public' => false,
        'show_ui' => true,
        'supports' => ['title', 'editor', 'custom-fields'],
    ]);
    // Repeat for other CPTs
}
add_action('init', 'tbyte_prm_register_cpt');
function tbyte_prm_register_cpt_events()
{
    register_post_type('tbyte_prm_events', [
        'label' => 'Events',
        'public' => true,
        'has_archive' => true,
        'supports' => ['title', 'editor', 'thumbnail'],
        'rewrite' => ['slug' => 'events'],
    ]);
}
add_action('init', 'tbyte_prm_register_cpt_events');
function tbyte_prm_register_cpt_assets()
{
    register_post_type('tbyte_prm_assets', [
        'label' => 'Assets',
        'public' => true,
        'has_archive' => true,
        'supports' => ['title', 'editor', 'thumbnail'],
        'rewrite' => ['slug' => 'assets'],
        'capability_type' => 'post',
    ]);
}
add_action('init', 'tbyte_prm_register_cpt_assets');

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

function tbyte_prm_register_custom_post_types()
{
    register_post_type('prm_event', [
        'label' => 'Events',
        'public' => false,
        'show_ui' => true,
        'capability_type' => 'post',
        'capabilities' => [
            'edit_post' => 'edit_events',
            'edit_posts' => 'edit_events',
        ],
    ]);

    register_post_type('prm_asset', [
        'label' => 'Assets',
        'public' => false,
        'show_ui' => true,
        'capability_type' => 'post',
        'capabilities' => [
            'edit_post' => 'edit_assets',
            'edit_posts' => 'edit_assets',
        ],
    ]);
}
add_action('init', 'tbyte_prm_register_custom_post_types');

function tbyte_prm_register_event_fields()
{
    register_post_meta('tbyte_prm_events', 'event_date', [
        'type' => 'string',
        'show_in_rest' => true,
        'single' => true,
        'sanitize_callback' => 'sanitize_text_field',
    ]);

    register_post_meta('tbyte_prm_events', 'venue', [
        'type' => 'string',
        'show_in_rest' => true,
        'single' => true,
        'sanitize_callback' => 'sanitize_text_field',
    ]);
}
add_action('init', 'tbyte_prm_register_event_fields');

function prm_register_support_cpt()
{
    register_post_type('prm_support', [
        'labels' => [
            'name' => 'Support Requests',
            'singular_name' => 'Support Request',
        ],
        'public' => false,
        'show_ui' => true,
        'supports' => ['title'],
        'capability_type' => 'post',
        'menu_position' => 25,
        'menu_icon' => 'dashicons-sos',
    ]);
}
add_action('init', 'prm_register_support_cpt');

function prm_add_support_meta_boxes()
{
    add_meta_box('prm_support_details', 'Request Details', 'prm_render_support_meta', 'prm_support', 'normal', 'default');
}
add_action('add_meta_boxes', 'prm_add_support_meta_boxes');

function prm_render_support_meta($post)
{
    $name = get_post_meta($post->ID, 'support_name', true);
    $email = get_post_meta($post->ID, 'support_email', true);
    $message = get_post_meta($post->ID, 'support_message', true);

    echo "<p><strong>Name:</strong> " . esc_html($name) . "</p>";
    echo "<p><strong>Email:</strong> " . esc_html($email) . "</p>";
    echo "<p><strong>Message:</strong><br>" . nl2br(esc_html($message)) . "</p>";
}


function prm_register_event_fieldss()
{
    register_post_meta('events', 'event_date', [
        'type' => 'string',
        'show_in_rest' => true,
        'single' => true,
        'sanitize_callback' => 'sanitize_text_field',
    ]);

    register_post_meta('events', 'venue', [
        'type' => 'string',
        'show_in_rest' => true,
        'single' => true,
        'sanitize_callback' => 'sanitize_text_field',
    ]);
}
add_action('init', 'prm_register_event_fieldss');




function tbyte_prm_add_event_metabox()
{
    add_meta_box(
        'tbyte_prm_event_details',
        'Event Details',
        'tbyte_prm_render_event_metabox',
        'tbyte_prm_events', // your CPT slug
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'tbyte_prm_add_event_metabox');

function tbyte_prm_render_event_metabox($post)
{
    // Nonce for security
    wp_nonce_field('tbyte_prm_save_event_meta', 'tbyte_prm_event_meta_nonce');

    // Get existing values
    $event_date = get_post_meta($post->ID, '_event_date', true);
    $start_time = get_post_meta($post->ID, '_event_start_time', true);
    $end_time = get_post_meta($post->ID, '_event_end_time', true);
    $venue = get_post_meta($post->ID, '_event_venue', true);

?>
    <p>
        <label for="event_date"><strong>Date</strong></label><br>
        <input type="text" id="event_date" name="event_date" class="regular-text" value="<?= esc_attr($event_date); ?>" required />
    </p>

    <p>
        <label for="event_start_time"><strong>Start Time</strong></label><br>
        <input type="text" id="event_start_time" name="event_start_time" class="regular-text" value="<?= esc_attr($start_time); ?>" required />
    </p>

    <p>
        <label for="event_end_time"><strong>End Time</strong></label><br>
        <input type="text" id="event_end_time" name="event_end_time" class="regular-text" value="<?= esc_attr($end_time); ?>" required />
    </p>
    <p>
        <label for="event_venue"><strong>Venue:</strong></label><br>
        <input type="text" name="event_venue" id="event_venue" value="<?= esc_attr($venue); ?>" class="regular-text">
    </p>
<?php
}
function tbyte_prm_save_event_meta($post_id)
{
    // Check nonce
    if (!isset($_POST['tbyte_prm_event_meta_nonce']) || !wp_verify_nonce($_POST['tbyte_prm_event_meta_nonce'], 'tbyte_prm_save_event_meta')) {
        return;
    }

    // Autosave?
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Check user permission
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (isset($_POST['event_date']) && $_POST['event_date']) {
        $date = sanitize_text_field($_POST['event_date']);
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            update_post_meta($post_id, '_event_date', $date);
        }
    }

    if (isset($_POST['event_start_time'])) {
        $start = sanitize_text_field($_POST['event_start_time']);
        if (preg_match('/^\d{2}:\d{2}$/', $start)) {
            update_post_meta($post_id, '_event_start_time', $start);
        }
    }

    if (isset($_POST['event_end_time'])) {
        $end = sanitize_text_field($_POST['event_end_time']);
        if (preg_match('/^\d{2}:\d{2}$/', $end)) {
            update_post_meta($post_id, '_event_end_time', $end);
        }
    }

    if (isset($_POST['event_venue'])) {
        update_post_meta($post_id, '_event_venue', sanitize_text_field($_POST['event_venue']));
    }
}
add_action('save_post', 'tbyte_prm_save_event_meta');
