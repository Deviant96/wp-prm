<?php $current_user = get_query_var('current_user'); ?>
<div class="dashboard-header ml-[60px] md:ml-0 p-6">
    <h1>Partner Dashboard</h1>
    <div>Welcome, <?php echo esc_html($current_user->display_name); ?></div>
    <a href="<?php echo wp_logout_url(home_url()); ?>">Logout</a>
</div>