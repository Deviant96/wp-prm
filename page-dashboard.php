<?php
/* Template Name: Partner Dashboard */

if (!is_user_logged_in()) {
    wp_redirect( wp_login_url() );
    exit;
}

$current_user = wp_get_current_user();
set_query_var('current_user', $current_user);

get_header();
?>
</head>

<body class="m-0">

    <?php get_template_part('template-parts/dashboard/dashboard', 'header'); ?>

    <!-- <div class="dashboard-container"> -->
    <div class="flex min-h-screen bg-gray-100  text-gray-800 ">

        <!-- Sidebar -->
        <?php get_template_part('template-parts/dashboard/dashboard', 'sidebar'); ?>

        <!-- Main Content -->
        <!-- <div class="dashboard-content"> -->
        <main id="mainContent" class="flex-1 p-6 transition-all duration-300 ml-[60px] md:ml-0">
            <?php
            $tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'dashboard';
            switch ($tab) {
                case 'events':
                    get_template_part('template-parts/dashboard/dashboard', 'events');
                    break;
                
                case 'events-manage':
                    get_template_part('template-parts/dashboard/events/manage');
                    break;

                case 'events-create':
                    get_template_part('template-parts/dashboard/events/create');
                    break;

                case 'events-create-sync':
                    get_template_part('template-parts/dashboard/events/create', 'sync');
                    break;

                case 'assets':
                    echo '<h2>My Assets</h2>';
                    echo '<p>Download your assets here.</p>';
                    get_template_part('template-parts/dashboard/dashboard', 'assets');
                    // List downloadable assets
                    break;

                case 'assets-manage':
                    get_template_part('template-parts/dashboard/assets/manage');
                    break;

                case 'assets-create':
                    get_template_part('template-parts/dashboard/assets/create');
                    break;

                case 'assets-doc-types':
                    get_template_part('template-parts/dashboard/assets/document', 'type');
                    break;

                case 'assets-language':
                    get_template_part('template-parts/dashboard/assets/language');
                    break;

                case 'partners':
                    echo '<h2>Partners Overview</h2>';
                    echo '<p>Manage your partner relationships here.</p>';
                    get_template_part('template-parts/dashboard/dashboard', 'partners-manage');
                    break;

                case 'partners-all':
                    get_template_part('template-parts/dashboard/partners/manage');
                    break;

                case 'partners-deleted':
                    get_template_part('template-parts/dashboard/partners/deleted');
                    break;

                case 'portal-settings':
                    get_template_part('template-parts/dashboard/dashboard', 'settings');
                    break;

                case 'support':
                    echo '<h2>Support</h2>';
                    echo '<p>Support info or form here.</p>';
                    get_template_part('template-parts/dashboard/dashboard', 'support');
                    break;

                case 'asset-preview':
                    get_template_part('template-parts/asset','preview');

                default:
                    echo '<h2>Dashboard Overview</h2>';
                    echo '<p>Welcome to your partner dashboard! Use the menu to navigate.</p>';
                    get_template_part('template-parts/dashboard/dashboard', 'welcome');
                    break;
            }
            ?>
            <!-- </div> -->
        </main>
    </div>

    <?php get_template_part('template-parts/dashboard/dashboard', 'footer'); ?>

    <?php
    get_footer();
