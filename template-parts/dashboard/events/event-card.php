<?php
$date = get_query_var('date');
$event_image = get_query_var('event_image');
$event_description = get_query_var('event_description');
$event_link = get_query_var('event_link');
$event_id = get_query_var('event_id');
$event_type = get_query_var('event_type');
?>

<div class="flex flex-col sm:flex-row bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden">
    <div class="sm:w-1/3 bg-gray-200 dark:bg-gray-700 h-32 sm:h-auto">
        <img src="<?php echo esc_url($event_image); ?>" alt="Event" class="w-full h-full object-cover">
    </div>
    <div class="p-4 flex flex-col justify-between flex-grow">
        <div>
            <div class="text-sm text-gray-500 dark:text-gray-400 mb-1"><?php echo esc_html($date); ?> – <?php echo esc_html($event_type); ?></div>
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100"><?php the_title(); ?></h3>
            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1"><?php echo esc_html($event_description); ?></p>
        </div>
        <div class="mt-3 flex justify-between items-center">
            <a href="<?php echo esc_url($event_link); ?>" class="text-blue-600 dark:text-blue-400 hover:underline text-sm">View Details</a>
            <ion-icon name="calendar-outline" class="text-gray-400 dark:text-gray-500 text-xl"></ion-icon>
        </div>
    </div>
    <p></p>
</div>