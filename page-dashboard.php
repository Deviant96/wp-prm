<?php
/* Template Name: Partner Dashboard */

if (!is_user_logged_in()) {
    wp_redirect(wp_login_url());
    exit;
}

$current_user = wp_get_current_user();
$user_roles = $current_user->roles;
set_query_var('current_user', $current_user);

wp_header();
?>

<script>
    var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
</script>

    <style>
        .vertical-menu {
            width: 250px;
            height: 100vh;
            background-color: #2c3e50;
            color: #ecf0f1;
            /* position: sticky; */
            top: 0;
            transition: width 0.3s ease;
            flex-shrink: 0;
        }

        .vertical-menu .menu-toggle {
            padding: 10px;
            cursor: pointer;
            background-color: #34495e;
            text-align: center;
        }

        .vertical-menu .menu {
            list-style-type: none;
            padding: 0;
        }

        .vertical-menu.collapsed {
            width: 60px;
        }

        .vertical-menu.collapsed .menu-text {
            display: none;
        }

        .vertical-menu.collapsed .submenu {
            display: none;
        }

        .vertical-menu .menu .menu-item.has-submenu {
            position: relative;
        }

        .vertical-menu .menu .menu-item.has-submenu .submenu {
            list-style-type: none;
            padding-left: 20px;
        }

        .vertical-menu .menu .menu-item a {
            display: flex;
            align-items: center;
            padding: 10px 20px;
            /* color: #ecf0f1; */
            text-decoration: none;
        }

        .vertical-menu .menu .menu-item.has-submenu .submenu li a {
            font-size: 0.9em;
            padding-left: 30px;
        }

        .vertical-menu.collapsed .menu-item a {
            justify-content: center;
        }

        .vertical-menu.collapsed .menu-item a ion-icon {
            margin-right: 0 !important;
        }

        .vertical-menu .menu .menu-item a ion-icon {
            font-size: 1.5em;
            margin-right: 10px;
        }
    </style>

    <style>
        .dashboard-container {
            display: flex;
            min-height: 80vh;
        }

        .dashboard-sidebar {
            width: 220px;
            background: #f5f5f5;
            padding: 20px;
            border-right: 1px solid #ddd;
        }

        .dashboard-sidebar a {
            display: block;
            margin-bottom: 10px;
            text-decoration: none;
            color: #333;
            font-weight: 500;
        }

        .dashboard-content {
            flex: 1;
            padding: 30px;
        }

        .dashboard-header {
            background: #fff;
            /* padding: 15px 30px; */
            border-bottom: 1px solid #ddd;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .dashboard-header h1 {
            margin: 0;
        }
    </style>

    <style>
        .events-section .events-banner {
            flex: 1;
            max-width: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            background-image: url("./../images/events.jpg");
            background-position: center;
            background-size: cover;
            position: relative;
        }
    </style>
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

                case 'support':
                    echo '<h2>Support</h2>';
                    echo '<p>Support info or form here.</p>';
                    get_template_part('template-parts/dashboard/dashboard', 'support');
                    break;

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
