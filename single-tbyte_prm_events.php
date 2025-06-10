<?php
/**
 * Single Asset Post View
 * Template for displaying single asset posts with Tailwind CSS
 */

$doc_types = get_the_terms(get_the_ID(), 'doc_type');
$doc_types = $doc_types ? $doc_types[0]->name : 'N/A';
$language = get_post_meta(get_the_ID(), 'language', true);

if (have_posts()) :
    while (have_posts()) : the_post();

        $post_id = get_the_ID();
        $event = [
            'id' => $post_id,
            'title' => get_the_title(),
            'link' => get_permalink(),

            'start_date' => get_post_meta($post_id, '_event_start_date', true),
            'end_date' => get_post_meta($post_id, '_event_end_date', true),
            'start_time' => get_post_meta($post_id, '_event_start_time', true),
            'end_time' => get_post_meta($post_id, '_event_end_time', true),

            'venue' => get_post_meta($post_id, '_event_venue', true),
            'event_url' => get_post_meta($post_id, '_event_url', true),
            'event_status' => get_post_meta($post_id, '_event_status', true),
            'image' => get_post_meta($post_id, '_event_image', true),

            'cost' => get_post_meta($post_id, '_event_cost', true),
            'currency_symbol' => get_post_meta($post_id, '_event_currency_symbol', true),
            'currency_code' => get_post_meta($post_id, '_event_currency_code', true),
            'currency_position' => get_post_meta($post_id, '_event_currency_position', true),

            'is_featured' => get_post_meta($post_id, '_event_is_featured', true),
            'hide_from_listings' => get_post_meta($post_id, '_event_hide_from_listings', true),
            'show_map' => get_post_meta($post_id, '_event_show_map', true),
            'show_map_link' => get_post_meta($post_id, '_event_show_map_link', true),

            'tags' => wp_get_post_terms($post_id, 'post_tag', ['fields' => 'names']),
            'date' => get_post_meta($post_id, '_event_date', true),
            'formatted_date' => date_i18n(get_option('date_format'), get_post_meta($post_id, '_event_date', true)),
            'location' => get_post_meta($post_id, '_event_location', true),
        ];
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> class="">
    <head>
        <meta charset="<?php bloginfo('charset'); ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?php wp_head(); ?>

        <style>
            ol {
                list-style: none;
                padding-left: 0;
            }
            li {
                list-style: none;
            }
        </style>
    </head>
    <body <?php body_class(); ?>>
        <div class="flex flex-col min-h-screen">
            <main class="container mx-auto px-4 py-8 max-w-4xl">
                <!-- Breadcrumb Navigation -->
                <nav class="flex p-6" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-2">
                        <li class="inline-flex items-center">
                            <a href="<?php echo home_url(); ?>" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                            <svg class="w-3 h-3 mr-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                <path d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z"/>
                            </svg>
                            Home
                            </a>
                        </li>
                        <li>
                            <div class="flex items-center">
                            <svg class="w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                            </svg>
                            <a href="<?php echo home_url('/?tab=assets') ?>" class="ml-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ml-2">Assets</a>
                            </div>
                        </li>
                        <li aria-current="page">
                            <div class="flex items-center">
                            <svg class="w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                            </svg>
                            <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2"><?php the_title(); ?></span>
                            </div>
                        </li>
                    </ol>
                </nav>
                <article class="bg-white rounded-xl shadow-md overflow-hidden">
                    <div class="max-w-6xl mx-auto p-6">
                        <div class="bg-white rounded-xl shadow-md p-8">
                            <div class="flex items-center justify-between mb-6">
                                <h1 class="text-3xl font-bold text-gray-800"><?php echo esc_html($event['title']); ?></h1>
                                <?php if ($event['is_featured']) : ?>
                                    <span class="bg-yellow-200 text-yellow-800 text-xs px-3 py-1 rounded-full font-semibold uppercase">Featured</span>
                                <?php endif; ?>
                            </div>

                            <?php if (!empty($event['image'])) : ?>
                                <div class="mb-6">
                                    <img src="<?php echo esc_url($event['image']); ?>" alt="<?php echo esc_attr($event['title']); ?>" class="rounded-md w-full object-cover max-h-96">
                                </div>
                            <?php endif; ?>

                            <div class="grid md:grid-cols-2 gap-6 mb-6">
                                <div>
                                    <p class="text-sm text-gray-500">Date</p>
                                    <p class="text-lg font-medium"><?php echo esc_html($event['formatted_date']); ?></p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Time</p>
                                    <p class="text-lg font-medium">
                                        <?php echo esc_html($event['start_time']); ?> - <?php echo esc_html($event['end_time']); ?>
                                    </p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Venue</p>
                                    <p class="text-lg font-medium"><?php echo esc_html($event['venue']); ?></p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Location</p>
                                    <p class="text-lg font-medium"><?php echo esc_html($event['location']); ?></p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Cost</p>
                                    <p class="text-lg font-medium">
                                        <?php echo esc_html($event['currency_symbol'] . $event['cost']); ?>
                                    </p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Status</p>
                                    <p class="text-lg font-medium"><?php echo esc_html(ucfirst($event['event_status'])); ?></p>
                                </div>
                            </div>

                            <?php if (!empty($event['event_url'])) : ?>
                                <div class="mb-6">
                                    <a href="<?php echo esc_url($event['event_url']); ?>" class="text-blue-600 underline">Visit Event Website</a>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($event['tags'])) : ?>
                                <div class="mb-6">
                                    <p class="text-sm text-gray-500 mb-2">Tags</p>
                                    <div class="flex flex-wrap gap-2">
                                        <?php foreach ($event['tags'] as $tag) : ?>
                                            <span class="bg-gray-100 text-gray-800 px-3 py-1 rounded-full text-sm"><?php echo esc_html($tag); ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if ($event['show_map_link']) : ?>
                                <div>
                                    <a href="https://www.google.com/maps/search/?q=<?php echo urlencode($event['location']); ?>" target="_blank"
                                    class="inline-flex items-center text-sm text-blue-600 hover:underline">
                                        📍 View on Google Maps
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php
                        endwhile;
                    endif; ?>
                </article>
            </main>
        </div>
        <script src="https://unpkg.com/ionicons@5.5.2/dist/ionicons.js"></script>

<?php get_footer(); ?>