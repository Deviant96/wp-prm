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
add_action('admin_init', 'prm_redirect_partner_manager_from_admin');

/*
 * Notify user on role change to "partner" AKA their account got approved
 */
function notify_user_on_approval($user_id, $new_role) {
    if( $new_role === 'partner') {
        $user = get_userdata($user_id);
        $to = $user->user_email;

        $reset_key = get_password_reset_key($user);

        if (is_wp_error($reset_key)) {
            error_log('Password generation failed for user ID: ' . $user_id . ', Error: ' . $reset_key->get_error_message());
            return;
        }

        // This is to build reset URL
        $reset_url = network_site_url('wp-login.php?action=rp&key=' . $reset_key . '&login=' . rawurlencode($user->user_login), 'login');

        $full_name = $user->first_name . ' ' . $user->last_name;

        $subject = 'Your Partner Application has been Approved - Action Required';
        $message = "Hello {$full_name},\n\n";
        $message .= "Congratulations! Your application has been approved.\n\n";
        $message .= "To activate your account, please set your password by clicking the link below:\n\n";
        $message .= $reset_url . "\n\n";
        $message .= "This link will expire in 24 hours.\n\n";
        $message .= "If you did not request this, please ignore this email.\n\n";

        $message .= "You can log in here: " . wp_login_url();

        update_user_meta($user_id, 'partner_approval_date', current_time('mysql'));
        update_user_meta($user_id, 'partner_approval_email_sent', 1);

        wp_mail($to, $subject, $message);
    }
}
add_action('set_user_role', 'notify_user_on_approval', 10, 2);