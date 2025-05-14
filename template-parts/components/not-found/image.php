<?php
/**
 * Template part for displaying a placeholder image when no image is available.
 *
 * @package TerraPRM
 */
?>

<div class="w-32 h-32 bg-gray-100 text-gray-400 flex items-center justify-center rounded border border-gray-200 text-center">
    <!-- <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-200" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M12 2L2 7h20L12 2z"/>
        <path d="M2 7v10l10 5 10-5V7H2z"/>
    </svg> -->
    <div class="text-sm mt-2 pointer-events-none select-none">
        <?php esc_html_e('No Image Available', 'tbyte-prm'); ?>
    </div>
</div>