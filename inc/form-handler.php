<?php
function handle_partner_registration() {
    if (!isset($_POST['email']) || !is_email($_POST['email'])) {
        wp_redirect(add_query_arg('registration', 'error', wp_get_referer()));
        exit;
    }

    $email = sanitize_email($_POST['email']);
    $first_name = sanitize_text_field($_POST['first_name']);
    $last_name = sanitize_text_field($_POST['last_name']);
    $full_name = trim($first_name . ' ' . $last_name);
    $job_title = sanitize_text_field($_POST['job_title'] ?? '');
    $company = sanitize_text_field($_POST['company'] ?? '');
    $country = sanitize_text_field($_POST['country'] ?? '');
    $company_address = sanitize_text_field($_POST['company_address'] ?? '');
    $company_city = sanitize_text_field($_POST['company_city'] ?? '');
    $company_zip = sanitize_text_field($_POST['company_zip'] ?? '');
    $company_phone = sanitize_text_field($_POST['company_phone'] ?? '');
    $company_mobile = sanitize_text_field($_POST['company_mobile'] ?? '');

    if (isset($_POST['prm_register_partner'])) {
        if (email_exists($email)) {
            wp_redirect(add_query_arg('registration', 'error', wp_get_referer()));
            exit;
        }

        // Create user with no password — admin will generate it
        $password = wp_generate_password(12, true);
        $user_id = wp_create_user($email, $password, $email);

        if (is_wp_error($user_id)) {
            wp_redirect(add_query_arg('registration', 'error', wp_get_referer()));
            exit;
        }

        wp_update_user([
            'ID' => $user_id,
            'display_name' => $full_name,
            'first_name' => $first_name,
            'last_name' => $last_name,
        ]);

        // Assign role as pending
        $user = new WP_User($user_id);
        $user->set_role('pending_partner');
        
        // Store additional info as user meta
        update_user_meta($user_id, 'job_title', $job_title);
        update_user_meta($user_id, 'company', $company);
        update_user_meta($user_id, 'country', $country);
        update_user_meta($user_id, 'company_address', $company_address);
        update_user_meta($user_id, 'company_city', $company_city);
        update_user_meta($user_id, 'company_zip', $company_zip);
        update_user_meta($user_id, 'company_phone', $company_phone);
        update_user_meta($user_id, 'company_mobile', $company_mobile);

        // Notify user about registration
        $to = $email;
        $subject = 'Registration Received';
        $message = "Thanks, $full_name! Your application is under review.\n\n";
        $message .= "You will receive an email once your account is approved.\n\n";
        $message .= "If you have any questions, please contact us at marketing@terrabytegroup.com .\n\n";
        
        // Get latest 3 posts from external website's REST API
        $external_api_url = 'https://terrabytegroup.com/wp-json/wp/v2/posts';
        $api_args = array(
            'per_page' => 3,
            'orderby' => 'date',
            'order' => 'desc',
            'categories' => 123  // Replace with actual newsletter category ID
        );
        
        $response = wp_remote_get(add_query_arg($api_args, $external_api_url));
        
        if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) {
            $newsletters = json_decode(wp_remote_retrieve_body($response));
            
            $message .= "\n\nLatest Newsletters:\n";
            foreach ($newsletters as $news) {
            $message .= "- " . $news->title->rendered . "\n";
            $message .= "  " . $news->link . "\n\n";
            }
        }
        $headers = ['Content-Type: text/plain; charset=UTF-8'];
        wp_mail($to, $subject, $message, $headers);

        // --- Profile Picture Upload ---
        // TODO Remove this, because it is not used in the registration form
        if (isset($_FILES['profile_picture']) && !empty($_FILES['profile_picture']['name'])) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            require_once(ABSPATH . 'wp-admin/includes/media.php');

            // Validate file type
            $allowed_types = ['image/gif', 'image/png', 'image/jpg', 'image/jpeg', 'image/jfif'];
            $file_type = $_FILES['profile_picture']['type'];
            if (!in_array($file_type, $allowed_types)) {
                wp_redirect(add_query_arg('registration', 'filetype_error', wp_get_referer()));
                exit;
            }

            // Validate image dimensions
            $image_info = getimagesize($_FILES['profile_picture']['tmp_name']);
            if ($image_info[0] < 256 || $image_info[1] < 256) {
                wp_redirect(add_query_arg('registration', 'dimension_error', wp_get_referer()));
                exit;
            }

            // Upload file to media library
            $attachment_id = media_handle_upload('profile_picture', 0);

            if (is_wp_error($attachment_id)) {
                wp_redirect(add_query_arg('registration', 'upload_error', wp_get_referer()));
                exit;
            }

            // Save attachment ID as user meta
            update_user_meta($user_id, 'profile_picture', $attachment_id);
        }

        // Send confirmation to user
        wp_mail($email, 'Registration Received', 'Thanks! Your application is under review.');

        // Notify site admin about new registration
        $admin_email = get_option('admin_email');
        $subject = 'New Partner Registration Pending Approval';
        $message = "A new partner has registered and is pending approval:\n\n";
        $message .= "Name: $full_name\n";
        $message .= "Email: $email\n\n";
        $message .= "Approve the user here: " . admin_url('admin.php?page=pending-partners');

        wp_mail($admin_email, $subject, $message);

        wp_redirect(add_query_arg('registration', 'success', home_url('/partner-portal')));
        // wp_redirect(add_query_arg('registration', 'success', wp_get_referer()));
        exit;
    }
}
add_action('admin_post_nopriv_process_registration', 'handle_partner_registration');
add_action('admin_post_process_registration', 'handle_partner_registration');