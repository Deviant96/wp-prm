<div class="modal-container">
    <!-- Modal header with close button -->
    <header class="sticky top-0 z-10 bg-white border-b border-gray-200 p-4 flex justify-between items-center">
        <h1 class="text-xl font-bold text-gray-900 truncate max-w-[80%]"><?php the_title(); ?></h1>
        <button onclick="window.parent.closeAssetModal()" class="text-gray-500 hover:text-gray-700 focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </header>

    <!-- Main content area -->
    <div class="modal-content p-4">
        <article class="bg-white rounded-lg">
            <!-- Meta information -->
            <div class="flex flex-wrap gap-4 text-sm text-gray-600 mb-4">
                <?php if ($language) : ?>
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M7 2a1 1 0 011 1v1h3a1 1 0 110 2H9.578a18.87 18.87 0 01-1.724 4.78c.29.354.596.696.914 1.026a1 1 0 11-1.44 1.389c-.188-.196-.373-.396-.554-.6a19.098 19.098 0 01-3.107 3.567 1 1 0 01-1.334-1.49 17.087 17.087 0 003.13-3.733 18.992 18.992 0 01-1.487-2.494 1 1 0 111.79-.89c.234.47.489.928.764 1.372.417-.934.752-1.913.997-2.927H3a1 1 0 110-2h3V3a1 1 0 011-1zm6 6a1 1 0 01.894.553l2.991 5.982a.869.869 0 01.02.037l.99 1.98a1 1 0 11-1.79.895L15.383 16h-4.764l-.724 1.447a1 1 0 11-1.788-.894l.99-1.98.019-.038 2.99-5.982A1 1 0 0113 8z" clip-rule="evenodd"></path>
                        </svg>
                        <?php echo esc_html($language); ?>
                    </div>
                <?php endif; ?>

                <?php if ($tags = wp_get_post_tags(get_the_ID())) : ?>
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M17.707 9.293a1 1 0 010 1.414l-7 7a1 1 0 01-1.414 0l-7-7A.997.997 0 012 10V5a3 3 0 013-3h5c.256 0 .512.098.707.293l7 7zM5 6a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path>
                        </svg>
                        <div class="flex flex-wrap gap-1">
                            <?php foreach ($tags as $tag) : ?>
                                <span class="bg-gray-100 px-2 py-1 rounded text-xs"><?php echo esc_html($tag->name); ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($doc_types) : ?>
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                        </svg>
                        <?php echo esc_html($doc_types); ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Description section -->
            <section class="mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-2">Description</h2>
                <div class="prose max-w-none text-gray-700 text-sm">
                    <?php the_content(); ?>
                </div>
            </section>

            <!-- Content preview section -->
            <section>
                <h2 class="text-lg font-semibold text-gray-800 mb-3">Preview</h2>

                <?php if ($doc_types === 'Image') : ?>
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <div class="flex justify-center bg-gray-50 p-4">
                            <img src="<?php echo esc_url(get_the_content()); ?>" alt="Preview of <?php the_title_attribute(); ?>" class="max-h-[60vh] object-contain">
                        </div>
                        <div class="bg-gray-50 px-4 py-3 border-t border-gray-200 flex justify-end">
                            <a href="<?php echo esc_url(get_the_content()); ?>" download class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700">
                                <svg class="-ml-0.5 mr-1.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                                Download
                            </a>
                        </div>
                    </div>

                <?php elseif ($doc_types === 'PDF') : ?>
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <div class="h-[60vh] bg-gray-50">
                            <iframe src="<?php echo esc_url(get_the_content()); ?>#view=fitH" class="w-full h-full"></iframe>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 border-t border-gray-200 flex justify-end">
                            <a href="<?php echo esc_url(get_the_content()); ?>" download class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700">
                                <svg class="-ml-0.5 mr-1.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                                Download
                            </a>
                        </div>
                    </div>

                <?php elseif ($doc_types === 'URL') : ?>
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <div class="bg-gray-50 p-4">
                            <div class="flex items-center gap-2 text-gray-700 mb-2">
                                <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M12.586 4.586a2 2 0 112.828 2.828l-3 3a2 2 0 01-2.828 0 1 1 0 00-1.414 1.414 4 4 0 005.656 0l3-3a4 4 0 00-5.656-5.656l-1.5 1.5a1 1 0 101.414 1.414l1.5-1.5zm-5 5a2 2 0 012.828 0 1 1 0 101.414-1.414 4 4 0 00-5.656 0l-3 3a4 4 0 105.656 5.656l1.5-1.5a1 1 0 10-1.414-1.414l-1.5 1.5a2 2 0 11-2.828-2.828l3-3z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="truncate text-sm"><?php echo esc_url(get_the_content()); ?></span>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 border-t border-gray-200 flex justify-end">
                            <a href="<?php echo esc_url(get_the_content()); ?>" target="_blank" rel="noopener noreferrer" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700">
                                <svg class="-ml-0.5 mr-1.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" />
                                </svg>
                                Visit Link
                            </a>
                        </div>
                    </div>

                <?php elseif (get_the_content()) : ?>
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <div class="bg-gray-50 p-4 text-center text-gray-700 text-sm">
                            <p class="mb-3">Preview not available for this document type.</p>
                            <a href="<?php echo esc_url(get_the_content()); ?>" download class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700">
                                <svg class="-ml-0.5 mr-1.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                                Download
                            </a>
                        </div>
                    </div>

                <?php else : ?>
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <div class="bg-gray-50 p-4 text-center text-gray-700 text-sm">
                            <p>No preview available.</p>
                        </div>
                    </div>
                <?php endif; ?>
            </section>
        </article>
    </div>

    <!-- Footer with action buttons -->
    <footer class="sticky bottom-0 bg-white border-t border-gray-200 p-3 flex justify-between">
        <button onclick="window.parent.closeAssetModal()" class="px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50">
            Close
        </button>
        <div class="flex gap-2">
            <button id="shareBtn" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50">
                <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M15 8a3 3 0 10-2.977-2.63l-4.94 2.47a3 3 0 100 4.319l4.94 2.47a3 3 0 10.895-1.789l-4.94-2.47a3.027 3.027 0 000-.74l4.94-2.47C13.456 7.68 14.19 8 15 8z" />
                </svg>
                Share
            </button>
            <?php if (current_user_can('edit_post', get_the_ID())) : ?>
                <a href="<?php echo get_edit_post_link(); ?>" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700">
                    <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                    </svg>
                    Edit
                </a>
            <?php endif; ?>
        </div>
    </footer>
</div>