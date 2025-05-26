<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="<?php echo get_template_directory_uri(); ?>/favicon.ico" sizes="32x32" />
    <?php wp_head(); ?>
    <style>
        * {
            box-sizing: border-box;
        }
        body, button, input, select, textarea {
            font-family: 'Urbanist', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }

        button {
            border: none;
            cursor: pointer;
            font-size: 1em;
            font-weight: 600;
        }

        a {
            text-decoration: none;
        }
        .dashboard-footer {
            text-align: center;
            padding: 20px;
            background: #f9f9f9;
            border-top: 1px solid #ddd;
        }
    </style>
    
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

        .dashboard-footer {
            text-align: center;
            padding: 20px;
            background: #f9f9f9;
            border-top: 1px solid #ddd;
        }
    </style>