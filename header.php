<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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