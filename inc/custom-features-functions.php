<?php
/**
 * @param array $providers The collection of providers that will be used to scan the design payload
 * @return array
 */
function register_my_theme_provider(array $providers): array {
    $providers[] = [
        'id' => 'my_theme', // The id of this custom provider. It should be unique across all providers
        'name' => 'My Theme Scanner',
        'description' => 'Scans the current active theme and child theme',
        'callback' => 'scanner_cb_my_theme_provider', // The function that will be called to get the data. Please see the next step for the implementation
        'enabled' => \WindPress\WindPress\Utils\Config::get(sprintf(
            'integration.%s.enabled',
            'my_theme' // The id of this custom provider
        ), true),
    ];

    return $providers;
}
add_filter('f!windpress/core/cache:compile.providers', 'register_my_theme_provider');

function scanner_cb_my_theme_provider(): array {
    // The file with this extension will be scanned, you can add more extensions if needed
    $file_extensions = [
        'php',
        'js',
        'html',
    ];

    $contents = [];
    $finder = new \WindPressDeps\Symfony\Component\Finder\Finder();

    // The current active theme
    $wpTheme = wp_get_theme();
    $themeDir = $wpTheme->get_stylesheet_directory();

    // Check if the current theme is a child theme and get the parent theme directory
    $has_parent = $wpTheme->parent() ? true : false;
    $parentThemeDir = $has_parent ? $wpTheme->parent()->get_stylesheet_directory() : null;

    // Scan the theme directory according to the file extensions
    foreach ($file_extensions as $extension) {
        $finder->files()->in($themeDir)->name('*.' . $extension);
        if ($has_parent) {
            $finder->files()->in($parentThemeDir)->name('*.' . $extension);
        }
    }

    // Get the file contents and send to the compiler
    foreach ($finder as $file) {
        $contents[] = [
            'name' => $file->getRelativePathname(),
            'content' => $file->getContents(),
        ];
    }

    return $contents;
}