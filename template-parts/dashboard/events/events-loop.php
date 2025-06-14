<?php $events = get_query_var('events'); ?>
<?php if (!$events) : ?>
    <?php return; ?>
<?php endif; ?>

<?php if ($events->have_posts()) : ?>
    <?php while ($events->have_posts()) : $events->the_post(); ?>

        <?php
        $date_string = get_post_meta(get_the_ID(), '_event_date', true);
        $date = null;

        if ( ! empty( $date_string ) ) {
            $date = DateTime::createFromFormat( 'Y-m-d', $date_string );
            if ( $date ) {
                $date = esc_html( $date->format( 'F j, Y' ) ); // e.g., April 24, 2025
            }
        }
        ?>
        <?php $start_time = get_post_meta(get_the_ID(), '_event_start_time', true); ?>
        <?php $end_time   = get_post_meta(get_the_ID(), '_event_end_time', true); ?>
        <?php $venue      = get_post_meta(get_the_ID(), '_event_venue', true); ?>
        <?php $event_type = wp_get_post_terms(get_the_ID(), 'event_type', ['fields' => 'names']); ?>
        <?php $event_type = !empty($event_type) ? implode(', ', $event_type) : ''; ?>
        <?php $event_link = get_permalink(); ?>
        <?php $event_title = get_the_title(); ?>
        <?php $event_image = get_the_post_thumbnail_url(get_the_ID(), 'medium'); ?>
        <?php $event_content = get_the_excerpt(); ?>
        <?php $event_tags = wp_get_post_tags(get_the_ID()); ?>

        <?php 
            set_query_var('date', $date);
            set_query_var('start_time', $start_time);
            set_query_var('end_time', $end_time);
            set_query_var('venue', $venue);
            set_query_var('event_link', $event_link);
            set_query_var('event_title', $event_title);
            set_query_var('event_image', $event_image);
            set_query_var('event_type', $event_type);
            set_query_var('event_content', $event_content); 
            set_query_var('event_tags', $event_tags); 
        ?>

        <?php get_template_part('template-parts/dashboard/events/event', 'card'); ?>
        
    <?php endwhile; ?>
    <?php wp_reset_postdata(); ?>
<?php else : ?>
    <p>No events found.</p>
<?php endif; ?>
