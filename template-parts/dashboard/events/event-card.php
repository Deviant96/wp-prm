<?php
$date = get_query_var('date');
$event_image = get_query_var('event_image');
$event_content = get_query_var('event_content');
$event_link = get_query_var('event_link');
$event_id = get_query_var('event_id');
$event_type = get_query_var('event_type');
$event_tags = get_query_var('event_tags');
?>

<div class="flex flex-col sm:flex-row bg-white  border border-gray-200  rounded-xl overflow-hidden">
    <div class="sm:w-1/3 bg-gray-200  h-32 sm:h-auto">
        <?php if (empty($event_image)) : ?>
            <div class="w-full h-full bg-gray-100  flex items-center justify-center">
                <?php get_template_part('/template-parts/components/not-found/image'); ?>
            </div>
        <?php else : ?>
            <div class="w-full h-full bg-gray-100  flex items-center justify-center">
                <img src="<?php echo esc_url($event_image); ?>" alt="Event" class="w-full h-full object-cover">
            </div>
        <?php endif; ?>
    </div>
    <div class="p-4 flex flex-col justify-between flex-grow">
        <div>
            <div class="text-sm text-gray-500  mb-1"><?php echo esc_html($date); ?> – <?php echo esc_html($event_type); ?></div>
            <h3 class="text-lg font-semibold text-gray-800 "><?php the_title(); ?></h3>
            <?php if ($event_tags) : ?>
                <div class="mt-3 flex flex-wrap gap-2">
                    <?php
                        array_map(function ($el) {
                            echo '<span class="text-xs px-2 py-1 bg-gray-100 rounded-full">' . $el->name . '</span>';
                        }, $event_tags);
                    ?>
                </div>
            <?php endif; ?>
        </div>
        <div class="mt-3 flex justify-between items-center">
            <a href="<?php echo esc_url($event_link); ?>" class="text-blue-600  hover:underline text-sm">View Details</a>
            <ion-icon name="calendar-outline" class="text-gray-400  text-xl"></ion-icon>
        </div>
    </div>
    <p></p>
</div>