<?php
/**
 * Terrabyte Group PRM System - No Public Access
 */

// Redirect all unauthorized access to portal
if (!is_user_logged_in()) {
    wp_redirect(home_url('/partner-portal'));
    exit;
}

$current_user = wp_get_current_user();
if (!in_array('partner', $current_user->roles) && !in_array('administrator', $current_user->roles) && !in_array('partner_manager', $current_user->roles)) {
    wp_die(__('You do not have permission to access this system.', 'tbyte-prm-theme'));
}

// Otherwise redirect to dashboard
wp_redirect(home_url());
exit;