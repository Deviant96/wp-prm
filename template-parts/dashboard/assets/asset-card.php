<?php
    $title = get_the_title();
    $id = get_the_ID();
    $content = get_the_content();
    $date = get_the_date();
    $link = get_permalink();

    $doc_types = get_the_terms(get_the_ID(), 'doc_type');
    $languages = get_the_terms(get_the_ID(), 'language');

    $docTypeThumb = '';
    $docType = $doc_types ? $doc_types[0]->name : 'N/A';
    if($docType === "PDF") {
        $docTypeThumb = '<img src="' . get_template_directory_uri() . '/assets/images/pdf-icon.png" alt="PDF Icon" class="w-full h-full object-cover">';
    } else if ($docType === "Image") {
        // Get the image URL from the post content
        $docTypeThumb = '<img src="' . get_the_content() . '" alt="Image Icon" class="w-full h-full object-cover">';
    } else if ($docType === "URL") {
        // Get the image URL from the post content
        $docTypeThumb = '<img src="' . get_template_directory_uri() . '/assets/images/link-icon.png" alt="Image Icon" class="w-full h-full object-cover">';
    } else {
        $docTypeThumb = '<div class="w-full h-full bg-gray-100  flex items-center justify-center">
            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
        </div>';
    }
?>

<div class="asset-card flex flex-col bg-white border border-gray-200 overflow-hidden p-4 rounded-xl shadow" data-id="<?php echo $id ?>">
    <?php if (has_post_thumbnail()): ?>
        <img src="<?php the_post_thumbnail_url(); ?>" class="mb-3 rounded h-32 object-cover w-full" alt="">
    <?php endif; ?>
    <div class="w-full bg-gray-200 h-64 sm:h-auto">
        <?php echo $docTypeThumb; ?>
    </div>
    <div class="flex flex-col justify-between flex-grow">
        <div>
            <h3 class="text-lg font-semibold text-gray-800 ">
                <?php echo $title ?>
            </h3>
            <small class="text-gray-500"><?php echo $date; ?></small> – <?php echo $docType; ?>
        </div>
        <div class="mt-4 flex justify-end gap-2">
            <?php if ($docType === "URL") : ?>
                <a href="<?php echo esc_url(get_the_content(null, false)); ?>" target="_blank" class="text-blue-600 hover:underline" onclick="copyAsset(event, this)">
                    <ion-icon name="copy-outline"></ion-icon>
                </a>
            <?php elseif ($docType === "Text") : ?>
                <a href="<?php echo htmlspecialchars(get_the_content(null, false)); ?>" target="_blank" class="text-blue-600 hover:underline" onclick="copyAsset(event, this)">
                    <ion-icon name="copy-outline"></ion-icon>
                </a>
            <?php else : ?>
                <a href="<?php echo esc_url(get_the_content()); ?>"
                    download class="text-blue-600 hover:underline">
                    <ion-icon name="download-outline"></ion-icon>
                </a>
            <?php endif; ?>
            <a href="<?php echo $link ?>" class="text-green-600 hover:underline">
                <ion-icon name="eye-outline"></ion-icon>
            </a>
        </div>
    </div>
</div>