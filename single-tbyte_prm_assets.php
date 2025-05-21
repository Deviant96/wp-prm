<?php
/**
 * Single Asset Post View
 * Template for displaying single asset posts with Tailwind CSS
 */

$doc_types = get_the_terms(get_the_ID(), 'doc_type');
$doc_types = $doc_types ? $doc_types[0]->name : 'N/A';
$language = get_post_meta(get_the_ID(), 'language', true);
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
                    <!-- Header section -->
                    <header class="p-6 border-b border-gray-100">
                        <h1 class="text-3xl font-bold text-gray-900 mb-4"><?php the_title(); ?></h1>
                        
                        <div class="flex flex-wrap gap-4 text-sm text-gray-600">
                            <?php if ($language) : ?>
                            <div class="flex items-center gap-2 text-sm text-gray-600">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M7 2a1 1 0 011 1v1h3a1 1 0 110 2H9.578a18.87 18.87 0 01-1.724 4.78c.29.354.596.696.914 1.026a1 1 0 11-1.44 1.389c-.188-.196-.373-.396-.554-.6a19.098 19.098 0 01-3.107 3.567 1 1 0 01-1.334-1.49 17.087 17.087 0 003.13-3.733 18.992 18.992 0 01-1.487-2.494 1 1 0 111.79-.89c.234.47.489.928.764 1.372.417-.934.752-1.913.997-2.927H3a1 1 0 110-2h3V3a1 1 0 011-1zm6 6a1 1 0 01.894.553l2.991 5.982a.869.869 0 01.02.037l.99 1.98a1 1 0 11-1.79.895L15.383 16h-4.764l-.724 1.447a1 1 0 11-1.788-.894l.99-1.98.019-.038 2.99-5.982A1 1 0 0113 8z" clip-rule="evenodd"></path>
                                </svg>
                                <a href="<?php echo esc_url(
                                            home_url('/?tab=assets&language=' . strtolower($language))
                                ); ?>" class="text-sm text-gray-600 underline">
                                    <?php echo esc_html($language); ?>
                                </a>
                            </div>
                            <?php endif; ?>
                            
                            <?php if ($tags = wp_get_post_tags(get_the_ID())) : ?>
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M17.707 9.293a1 1 0 010 1.414l-7 7a1 1 0 01-1.414 0l-7-7A.997.997 0 012 10V5a3 3 0 013-3h5c.256 0 .512.098.707.293l7 7zM5 6a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path>
                                </svg>
                                <div class="flex flex-wrap gap-1">
                                <?php foreach ($tags as $tag) : ?>
                                    <span class="bg-gray-100 px-2 py-1 rounded"><?php echo esc_html($tag->name); ?></span>
                                <?php endforeach; ?>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <?php if ($status = get_post_status(get_the_ID())) : ?>
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full 
                                <?php echo strtolower($status) === 'publish' ? 'bg-green-500' : ''; ?>
                                <?php echo strtolower($status) === 'draft' ? 'bg-yellow-500' : ''; ?>
                                <?php echo strtolower($status) === 'archived' ? 'bg-gray-500' : ''; ?>">
                                </span>
                                <span><?php echo esc_html($status); ?></span>
                            </div>
                            <?php endif; ?>
                            
                            <div class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                            </svg>
                            <span><?php echo get_the_date(); ?></span>
                            </div>
                            
                            <?php if ($doc_types) : ?>
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                                </svg>
                                <a href="<?php echo esc_url(
                                            home_url('/?tab=assets&doc_type=' . strtolower($doc_types))
                                ); ?>" class="text-sm text-gray-600 underline">
                                    <?php echo esc_html($doc_types); ?>
                                </a>
                            </div>
                            <?php endif; ?>
                        </div>
                    </header>
                    
                    <!-- Description section -->
                    <section class="p-6 border-b border-gray-100">
                    <h2 class="text-xl font-semibold text-gray-800 mb-3">Description</h2>
                    <div class="prose max-w-none text-gray-700">
                        <?php the_content(); ?>
                    </div>
                    </section>
                    
                    <!-- Content preview section -->
                    <section class="p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Preview</h2>
                    
                    <?php      
                    if ($doc_types === 'Image') : ?>
                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <div class="flex justify-center bg-gray-50 p-4">
                            <img src="<?php echo esc_url(get_the_content()); ?>" alt="Preview of <?php the_title_attribute(); ?>" class="max-h-96 object-contain">
                        </div>
                        <div class="bg-gray-50 px-4 py-3 border-t border-gray-200 flex justify-end">
                            <a href="<?php echo esc_url(get_the_content()); ?>" download class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                            Download Image
                            </a>
                        </div>
                        </div>
                        
                    <?php elseif ($doc_types === 'PDF') : ?>
                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <div class="h-96 bg-gray-50">
                            <iframe src="<?php echo esc_url(get_the_content()); ?>#view=fitH" class="w-full h-full"></iframe>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 border-t border-gray-200 flex justify-end">
                            <a href="<?php echo esc_url(get_the_content()); ?>" download class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                            Download PDF
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
                            <span class="truncate"><?php echo esc_url(get_the_content()); ?></span>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 border-t border-gray-200 flex justify-end">
                            <a href="<?php echo esc_url(get_the_content()); ?>" target="_blank" rel="noopener noreferrer" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" />
                            </svg>
                            Visit Link
                            </a>
                        </div>
                        </div>
                        
                    <?php elseif (get_the_content()) : ?>
                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <div class="bg-gray-50 p-4 text-center text-gray-700">
                            <p class="mb-4">Preview not available for this document type.</p>
                            <a href="<?php echo esc_url(get_the_content()); ?>" download class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                            Download File
                            </a>
                        </div>
                        </div>
                        
                    <?php else : ?>
                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <div class="bg-gray-50 p-4 text-center text-gray-700">
                            <p>No preview available.</p>
                        </div>
                        </div>
                    <?php endif; ?>
                    </section>
                    
                    <!-- Action buttons -->
                    <footer class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-between">
                    <div class="flex gap-2">
                        <button id="shareBtn" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M15 8a3 3 0 10-2.977-2.63l-4.94 2.47a3 3 0 100 4.319l4.94 2.47a3 3 0 10.895-1.789l-4.94-2.47a3.027 3.027 0 000-.74l4.94-2.47C13.456 7.68 14.19 8 15 8z" />
                        </svg>
                        Share
                        </button>
                    </div>
                    
                    <?php if (current_user_can('edit_post', get_the_ID())) : ?>
                        <a href="<?php echo get_edit_post_link(); ?>" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                        </svg>
                        Edit
                        </a>
                    <?php endif; ?>
                    </footer>
                </article>
            </main>
        </div>
        <script src="https://unpkg.com/ionicons@5.5.2/dist/ionicons.js"></script>
        <script>
            document.getElementById('shareBtn').addEventListener('click', async function() {
                if (navigator.share) {
                    try {
                        await navigator.share({
                            title: '<?php the_title(); ?>',
                            url: '<?php echo esc_url(get_permalink()); ?>'
                        });
                        showSuccess('Shared successfully!');
                    } catch (error) {
                        showError('Error sharing: ' + error);
                    }
                    navigator.share({
                        title: '<?php the_title(); ?>',
                        url: '<?php echo esc_url(get_permalink()); ?>'
                    })
                } else {
                    showError('Sharing is not supported in this browser.');
                }
            })
        </script>

<?php get_footer(); ?>