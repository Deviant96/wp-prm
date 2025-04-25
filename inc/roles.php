<?php

// Create Partner role
function prm_add_custom_roles() {
    add_role('partner', 'Partner', [
        'read' => true,
        'view_events' => true,
        'view_assets' => true,
        'edit_posts' => false,
        'delete_posts' => false,
    ]);

    add_role('pending_partner', 'Pending Partner', [
        'read' => false,
    ]);
}
add_action('init', 'prm_add_custom_roles');

// Create Partner Manager role
function prm_add_partner_manager_role() {
    add_role('partner_manager', 'Partner Manager', [
        'read' => true,
        'edit_posts' => true,
        'delete_posts' => false,
        'edit_others_posts' => false,
        'publish_posts' => false,
        'manage_partners' => true,
    ]);
}
add_action('init', 'prm_add_partner_manager_role');

// Redirect users from wp-admin if they are "Partner".
function prm_redirect_partner_from_admin() {
    if (current_user_can('partner') && is_admin() && !defined('DOING_AJAX')) {
        wp_redirect(site_url('/partner-dashboard'));
        exit;
    }
}
add_action('admin_init', 'prm_redirect_partner_from_admin');

function prm_redirect_partner_manager_from_admin() {
    if (current_user_can('partner_manager') && is_admin() && !defined('DOING_AJAX')) {
        wp_redirect(site_url('/partner-dashboard'));
        exit;
    }
}