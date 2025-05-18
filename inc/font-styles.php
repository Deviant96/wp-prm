<?php
function prm_enqueue_ionicons() {
    wp_enqueue_script(
        'ionicons',
        'https://unpkg.com/ionicons@7.2.1/dist/ionicons/ionicons.esm.js',
        [],
        null,
        true
    );

    wp_enqueue_script(
        'ionicons-nomodule',
        'https://unpkg.com/ionicons@7.2.1/dist/ionicons/ionicons.js',
        [],
        null,
        true
    );
}
add_action('wp_enqueue_scripts', 'prm_enqueue_ionicons');
