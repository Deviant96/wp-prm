<?php
$current_user = get_query_var('current_user');
$user_roles = $current_user->roles;
?>
<div class="dashboard-header ml-[60px] md:ml-0 px-6 py-3 flex justify-between items-center bg-white shadow-md">
    <h1><a href="<?php echo home_url('/?tab=dashboard'); ?>" class="text-3xl font-bold bg-gradient-to-r from-[#086ad7] to-[#a01632] bg-clip-text text-transparent hover:from-[#a01632] hover:to-[#086ad7] transition-all duration-300">Terrabyte Group Partner Dashboard</a></h1>
    <div class="text-right">
        <div>Welcome, <?php echo esc_html($current_user->display_name); ?></div>
        <div class="text-sm text-gray-500"><?php echo esc_html(implode(', ', $user_roles)); ?></div>
        <div class="flex items-center space-x-2 mt-2 justify-end">
            <!-- Logout with icon -->
            <a href="<?php echo wp_logout_url(home_url()); ?>" class="text-white text-sm hover:text-gray-300 flex items-center space-x-1 py-1 px-3 rounded-md transition duration-200 bg-red-800 border-none" title="Logout" aria-label="Logout" role="button">
                <ion-icon name="log-out" class="text-xl"></ion-icon>
                <span class="hidden md:inline">Logout</span>
            </a>
        </div>
    </div>
</div>