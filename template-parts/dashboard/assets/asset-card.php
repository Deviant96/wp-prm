<div class="asset-card bg-white  rounded shadow p-4 relative">
    <?php if (has_post_thumbnail()): ?>
        <img src="<?php the_post_thumbnail_url(); ?>" class="mb-3 rounded h-32 object-cover w-full" alt="">
    <?php endif; ?>
    <h3 class="font-bold"><?php the_title(); ?></h3>
    <p class="text-sm text-gray-500 ">
        <?php echo get_the_date(); ?> •
        <?php echo size_format(filesize(get_attached_file(get_post_thumbnail_id()))); ?>
    </p>
    <div class="mt-2 flex gap-2 flex-wrap">
        <?php the_terms(get_the_ID(), 'post_tag', '', '', ''); ?>
    </div>
    <div class="mt-4 flex justify-end gap-2">
        <a href="<?php echo esc_url(get_post_meta(get_the_ID(), 'asset_file', true)); ?>"
            download class="text-blue-600 hover:underline">
            <ion-icon name="download-outline"></ion-icon>
        </a>
        <a href="<?php the_permalink(); ?>" class="text-green-600 hover:underline">
            <ion-icon name="eye-outline"></ion-icon>
        </a>
    </div>
    <?php
    $doc_types = get_the_terms(get_the_ID(), 'doc_type');
    $languages = get_the_terms(get_the_ID(), 'language');
    if ($doc_types || $languages): ?>
        <div class="mt-3 text-sm">
            <?php if ($doc_types && !is_wp_error($doc_types)): ?>
                <div class="doc-types">Doc Type: <?php echo implode(', ', wp_list_pluck($doc_types, 'name')); ?></div>
            <?php endif; ?>
            <?php if ($languages && !is_wp_error($languages)): ?>
                <div class="languages">Languages: <?php echo implode(', ', wp_list_pluck($languages, 'name')); ?></div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>