<div class="space-y-8">

    <!-- Greeting Card -->
    <div
        class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white p-6 rounded-2xl shadow flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-semibold">Welcome back, <?php echo wp_get_current_user()->display_name; ?> 👋</h2>
            <p class="text-sm text-blue-100 mt-1">Here's what's happening with your assets and events.</p>
        </div>
        <img src="<?php echo get_template_directory_uri(); ?>/assets/hello.svg" alt="Welcome"
            class="w-24 hidden md:block">
    </div>

    <!-- Latest Assets -->
    <div>
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-semibold">Latest Marketing Assets</h3>
            <input type="text" placeholder="Search assets..."
                class="border border-gray-300 dark:border-gray-700 rounded px-3 py-1.5 text-sm focus:ring-2 focus:ring-blue-500">
        </div>
        <div class="grid sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            <?php
            $assets = new WP_Query([
                'post_type' => 'tbyte_prm_assets',
                'posts_per_page' => 4,
            ]);
            if ($assets->have_posts()):
                while ($assets->have_posts()):
                    $assets->the_post(); ?>
                    <div
                        class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-4 rounded-xl shadow hover:shadow-md transition">
                        <h4 class="font-semibold text-lg mb-1"><?php the_title(); ?></h4>
                        <p class="text-sm text-gray-600 dark:text-gray-300"><?php echo wp_trim_words(get_the_excerpt(), 15); ?>
                        </p>
                        <a href="<?php the_permalink(); ?>" class="text-blue-600 text-sm mt-2 inline-block">View Asset</a>
                    </div>
                <?php endwhile;
                wp_reset_postdata();
            else: ?>
                <p class="text-gray-500 dark:text-gray-400">No assets found.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Upcoming Events -->
    <div class="grid md:grid-cols-3 gap-6 items-stretch">
        <!-- Left Side: Feature Card -->
        <div class="col-span-1 bg-cover bg-center rounded-2xl overflow-hidden shadow relative flex items-end justify-start"
            style="background-image: url('<?php echo get_template_directory_uri(); ?>/assets/images/events-bg.jpg');">
            <div class="bg-black/50 w-full p-5">
                <h3 class="text-xl font-semibold text-white mb-2">Upcoming Events</h3>
                <a href="/partner-portal?tab=events" class="text-white underline text-sm">See all events</a>
            </div>
        </div>

        <!-- Right Side: List of Events -->
        <div class="col-span-2 space-y-4">
            <?php
            $args = [
                'post_type' => 'tbyte_prm_events',
                'posts_per_page' => 4,
                'meta_key' => '_event_date',
                'orderby' => 'meta_value',
                'order' => 'ASC',
                'meta_query' => [
                    [
                        'key' => '_event_date',
                        'value' => date('Y-m-d'),
                        'compare' => '>=',
                        'type' => 'DATE'
                    ]
                ]
            ];
            $events = new WP_Query($args);

            if ($events->have_posts()) :
                echo '<div class="grid grid-cols-1 md:grid-cols-2 gap-6">';
                while ($events->have_posts()) : $events->the_post();

                    $date       = get_post_meta(get_the_ID(), '_event_date', true);
                    $start_time = get_post_meta(get_the_ID(), '_event_start_time', true);
                    $end_time   = get_post_meta(get_the_ID(), '_event_end_time', true);
                    $venue      = get_post_meta(get_the_ID(), '_event_venue', true);
            ?>

                    <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-xl shadow bg-white dark:bg-gray-800 shadow hover:shadow-md transition">
                        <h2 class="text-xl font-semibold mb-1"><?php the_title(); ?></h2>
                        <p class="text-gray-600 dark:text-gray-300 mb-2">
                            📅 <?= date('F j, Y', strtotime($date)); ?>,
                            🕒 <?= esc_html($start_time); ?> - <?= esc_html($end_time); ?>
                        </p>
                        <p class="text-gray-500 dark:text-gray-400 mb-4">📍 <?= esc_html($venue); ?></p>
                        <a href="<?= get_permalink(); ?>" class="text-blue-600 hover:underline">View Details →</a>
                    </div>

            <?php endwhile;
                echo '</div>';
                wp_reset_postdata();
            else :
                echo '<p>No events found.</p>';
            endif;
            ?>
        </div>
    </div>

    <!-- (Optional) Quick Actions -->
    <div class="grid md:grid-cols-2 gap-4 mt-6">
        <div
            class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-4 rounded-xl shadow text-center hover:bg-gray-50 dark:hover:bg-gray-700 transition">
            <p class="font-semibold">Need help?</p>
            <a href="/partner-portal?tab=support" class="text-blue-600 text-sm mt-1 inline-block">Contact Support</a>
        </div>
        <div
            class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-4 rounded-xl shadow text-center hover:bg-gray-50 dark:hover:bg-gray-700 transition">
            <p class="font-semibold">Want to add new content?</p>
            <a href="/partner-portal?tab=assets&action=add" class="text-blue-600 text-sm mt-1 inline-block">Add New
                Asset</a>
        </div>
    </div>

</div>