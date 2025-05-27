<?php
// function wp_prm_enqueue_flatpickr() {
//     wp_enqueue_script('flatpickr', 'https://cdn.jsdelivr.net/npm/flatpickr', [], null, true);
//     wp_enqueue_style('flatpickr-style', 'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css');
// }
// add_action('wp_enqueue_scripts', 'wp_prm_enqueue_flatpickr');

add_action('rest_api_init', function () {
    register_rest_route('prm/v1', '/tbyte_prm_partner/(?P<id>\d+)', [
        'methods'  => 'GET',
        'callback' => 'tbyte_prm_get_partner',
        'permission_callback' => function() {
            return current_user_can('manage_options'); // Only allow admins
        }
    ]);

    register_rest_route('prm/v1', '/tbyte_prm_partner/soft_delete/(?P<id>\d+)', [
        'methods' => 'PUT',
        'callback' => 'tbyte_prm_soft_delete_partner',
        'permission_callback' => function() {
            return current_user_can('edit_posts');
        }
    ]);

    register_rest_route('prm/v1', '/tbyte_prm_partner/restore_soft_delete/(?P<id>\d+)', [
        'methods' => 'PUT',
        'callback' => 'tbyte_prm_restore_soft_delete_partner',
        'permission_callback' => function() {
            return current_user_can('edit_posts');
        }
    ]);
});

// Check permissions
function tbyte_prm_can_manage_partners() {
    return current_user_can('edit_posts');
}

// GET a single user/partner
function tbyte_prm_get_partner($request) {
    $user_id = (int) $request['id'];
    $user = get_user_by('ID', $user_id);

    if (!$user) {
        return new WP_Error('not_found', 'User not found', ['status' => 404]);
    }

    $partner_data = [
        'ID' => $user->ID,
        'email' => $user->user_email,
        'display_name' => $user->display_name,
        'first_name' => $user->first_name,
        'last_name' => $user->last_name,
        'company' => get_user_meta($user->ID, 'company', true),
        'phone' => get_user_meta($user->ID, 'phone', true),
        'address' => get_user_meta($user->ID, 'address', true),
        'city' => get_user_meta($user->ID, 'city', true),
        'state' => get_user_meta($user->ID, 'state', true),
        'zip' => get_user_meta($user->ID, 'zip', true),
        'country' => get_user_meta($user->ID, 'country', true),
    ];

    return new WP_REST_Response($partner_data, 200);
}

function tbyte_prm_soft_delete_partner($request) {
    $user_id = (int) $request['id'];
    $user = get_user_by('ID', $user_id);

    if (!$user) {
        return new WP_Error('not_found', 'User not found', ['status' => 404]);
    }

    update_user_meta($user_id, 'is_deleted', 1);

    return new WP_REST_Response([
        'success' => true,
        'message' => 'Partner soft deleted successfully.',
        'user_id' => $user_id
    ], 200);
}

function tbyte_prm_restore_soft_delete_partner($request) {
    $user_id = (int) $request['id'];
    $user = get_user_by('ID', $user_id);

    if (!$user) {
        return new WP_Error('not_found', 'User not found', ['status' => 404]);
    }

    update_user_meta($user_id, 'is_deleted', 0);

    return new WP_REST_Response([
        'success' => true,
        'message' => 'Partner restored successfully.',
        'user_id' => $user_id
    ], 200);
}