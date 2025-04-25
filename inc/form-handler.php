<?php

function prm_handle_partner_registration() {
    if (isset($_POST['prm_register_partner'])) {
        $fullname = sanitize_text_field($_POST['prm_fullname']);
        $email = sanitize_email($_POST['prm_email']);
        $password = $_POST['prm_password'];

        if (!email_exists($email)) {
            $user_id = wp_create_user($email, $password, $email);
            wp_update_user([
                'ID' => $user_id,
                'display_name' => $fullname,
            ]);
            // Assign role as pending
            $user = new WP_User($user_id);
            $user->set_role('pending_partner');

            // Send confirmation to user
            wp_mail($email, 'Registration Received', 'Thanks! Your application is under review.');

            // Notify site admin about new registration
            $admin_email = get_option('admin_email');
            $subject = 'New Partner Registration Pending Approval';
            $message = "A new partner has registered and is pending approval:\n\n";
            $message .= "Name: $fullname\n";
            $message .= "Email: $email\n\n";
            $message .= "Approve the user here: " . admin_url('admin.php?page=pending-partners');

            wp_mail($admin_email, $subject, $message);

            wp_redirect(home_url('/thank-you'));
            exit;
        } else {
            wp_die('Email already exists.');
        }
    }
}
add_action('init', 'prm_handle_partner_registration');


function prm_handle_support_request() {
    if (isset($_POST['submit_support'])) {
        $name = sanitize_text_field($_POST['support_name']);
        $email = sanitize_email($_POST['support_email']);
        $message = sanitize_textarea_field($_POST['support_message']);

        if (!$name || !$email || !$message) {
            wp_redirect(add_query_arg('support_status', 'error', wp_get_referer()));
            exit;
        }

        // Save as CPT
        $post_id = wp_insert_post([
            'post_type' => 'prm_support',
            'post_title' => 'Support from ' . $name,
            'post_status' => 'publish',
            'meta_input' => [
                'support_name' => $name,
                'support_email' => $email,
                'support_message' => $message,
            ],
        ]);

        // Send email
        $to = get_option('admin_email');
        $subject = "Support Request from $name";
        $body = "Name: $name\nEmail: $email\n\n$message";
        wp_mail($to, $subject, $body);

        wp_redirect(add_query_arg('support_status', 'success', wp_get_referer()));
        exit;
    }
}
add_action('init', 'prm_handle_support_request');

