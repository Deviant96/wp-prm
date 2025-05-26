<?php
// USED BY: dashboard-assets.php to load assets list
function prm_ajax_load_assets() {
    check_ajax_referer('prm_ajax_nonce', 'nonce');

    $layout = $_POST['layout'] ?? 'grid';
    $search = sanitize_text_field($_POST['search'] ?? '');
    $doc_type = $_POST['doc_type'] ?? [];
    $language = $_POST['language'] ?? [];
    $paged = max(1, intval($_POST['page'] ?? 1));
    $posts_per_page = 6;

    $tax_query = [];
    $args = [
        'post_type' => 'tbyte_prm_assets',
        's' => $search,
        'posts_per_page' => $posts_per_page,
        'paged' => $paged
    ];
    
    if (!empty($doc_type)) {
        $tax_query[] = [
            'taxonomy' => 'doc_type',
            'field'    => 'slug',
            'terms'    => is_array($doc_type) ? $doc_type : explode(',', $doc_type),
            'operator' => 'IN',
        ];
    }

    if (!empty($language)) {
        $tax_query[] = [
            'taxonomy' => 'language',
            'field'    => 'slug',
            'terms'    => is_array($language) ? $language : explode(',', $language),
            'operator' => 'IN',
        ];
    }

    if (!empty($tax_query)) {
        $args['tax_query'] = array_merge(
            ['relation' => 'OR'],
            $tax_query
        );
    }

    $query = new WP_Query($args);
    ob_start();

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            get_template_part('template-parts/dashboard/assets/asset', 'card', [
                'layout' => $layout
            ]);
        }
    } else {
        echo '<p class="text-gray-500 ">No assets found.</p>';
    }

    $html = ob_get_clean();
    wp_reset_postdata();

    wp_send_json_success([
        'html' => $html,
        'max_pages' => $query->max_num_pages,
        'total_posts' => $query->found_posts,
    ]);
}
add_action('wp_ajax_prm_load_assets', 'prm_ajax_load_assets');
add_action('wp_ajax_nopriv_prm_load_assets', 'prm_ajax_load_assets');


// Add custom field to taxonomy term
function add_doc_type_field_type() {
    ?>
    <div class="form-field">
        <label for="doc-type-field-type">Field Type</label>
        <select name="doc_type_field_type" id="doc-type-field-type">
            <option value="text">Text</option>
            <option value="url">URL</option>
            <option value="image">Image (.jpg, .png, .gif)</option>
            <option value="pdf">PDF (.pdf)</option>
            <option value="document">Document (.doc, .docx, .pdf)</option>
        </select>
        <p>The type of field required for this document type.</p>
    </div>
    <?php
}
add_action('doc_type_add_form_fields', 'add_doc_type_field_type');

// Edit custom field in taxonomy term
function edit_doc_type_field_type($term) {
    $field_type = get_term_meta($term->term_id, 'doc_type_field_type', true);
    ?>
    <tr class="form-field">
        <th scope="row"><label for="doc-type-field-type">Field Type</label></th>
        <td>
            <select name="doc_type_field_type" id="doc-type-field-type">
                <option value="text" <?php selected($field_type, 'text'); ?>>Text</option>
                <option value="url" <?php selected($field_type, 'url'); ?>>URL</option>
                <option value="image" <?php selected($field_type, 'image'); ?>>Image (.jpg, .png, .gif)</option>
                <option value="pdf" <?php selected($field_type, 'pdf'); ?>>PDF (.pdf)</option>
                <option value="document" <?php selected($field_type, 'document'); ?>>Document (.doc, .docx, .pdf)</option>
            </select>
            <p class="description">The type of field required for this document type.</p>
        </td>
    </tr>
    <?php
}
add_action('doc_type_edit_form_fields', 'edit_doc_type_field_type');

// Save custom field data
function save_doc_type_field_type($term_id) {
    if (isset($_POST['doc_type_field_type'])) {
        update_term_meta($term_id, 'doc_type_field_type', sanitize_text_field($_POST['doc_type_field_type']));
    }
}
add_action('created_doc_type', 'save_doc_type_field_type');
add_action('edited_doc_type', 'save_doc_type_field_type');

function document_upload_form() {
    ob_start();
    
    // Get all document types
    $doc_types = get_terms(array(
        'taxonomy' => 'doc_type',
        'hide_empty' => false,
    ));
    
    ?>
    <div class="space-y-6">
        <h2 class="text-2xl font-bold text-gray-800 ">Add New Document</h2>
        
        <form id="document-upload-form" class="space-y-4" enctype="multipart/form-data">
            <div>
                <label for="document-title" class="block text-sm font-medium text-gray-700 ">Title*</label>
                <input type="text" id="document-title" name="title" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500   ">
            </div>
            
            <div>
                <label for="document-type" class="block text-sm font-medium text-gray-700 ">Document Type*</label>
                <select id="document-type" name="document_type" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500   ">
                    <option value="">Select a document type</option>
                    <?php foreach ($doc_types as $type) : 
                        $field_type = get_term_meta($type->term_id, 'doc_type_field_type', true);
                    ?>
                        <option value="<?php echo $type->term_id; ?>" data-field-type="<?php echo $field_type; ?>">
                            <?php echo $type->name; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <!-- Dynamic field based on document type -->
            <div id="document-content-field">
                <!-- Field will be dynamically inserted here based on selection -->
            </div>
            
            <?php wp_nonce_field('upload_document_nonce', 'document_nonce'); ?>
            <input type="hidden" name="action" value="upload_document">
            
            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition">
                Save Document
            </button>
        </form>
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        $('#document-type').on('change', function() {
            var fieldType = $(this).find('option:selected').data('field-type');
            var fieldHtml = '';
            
            switch(fieldType) {
                case 'text':
                    fieldHtml = `
                        <label class="block text-sm font-medium text-gray-700 ">Content*</label>
                        <textarea name="content" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500   "></textarea>
                    `;
                    break;
                    
                case 'url':
                    fieldHtml = `
                        <label class="block text-sm font-medium text-gray-700 ">URL*</label>
                        <input type="url" name="content" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500   ">
                    `;
                    break;
                    
                case 'image':
                    fieldHtml = `
                        <label class="block text-sm font-medium text-gray-700 ">Image File* (JPG, PNG, GIF)</label>
                        <input type="file" name="content" accept=".jpg,.jpeg,.png,.gif" required class="mt-1 block w-full">
                    `;
                    break;
                    
                case 'pdf':
                    fieldHtml = `
                        <label class="block text-sm font-medium text-gray-700 ">PDF File* (.pdf)</label>
                        <input type="file" name="content" accept=".pdf" required class="mt-1 block w-full">
                    `;
                    break;
                    
                case 'document':
                    fieldHtml = `
                        <label class="block text-sm font-medium text-gray-700 ">Document File* (.doc, .docx, .pdf)</label>
                        <input type="file" name="content" accept=".doc,.docx,.pdf" required class="mt-1 block w-full">
                    `;
                    break;
                    
                default:
                    fieldHtml = '<p>Please select a document type</p>';
            }
            
            $('#document-content-field').html(fieldHtml);
        });
    });
    </script>
    <?php
    
    return ob_get_clean();
}
add_shortcode('document_upload_form', 'document_upload_form');


add_action('wp_ajax_upload_document', 'handle_upload_document');
add_action('wp_ajax_nopriv_upload_document', 'handle_upload_document');

function handle_upload_document() {
    check_ajax_referer('upload_document_nonce', 'document_nonce');
    
    if (!is_user_logged_in() || !current_user_can('edit_posts')) {
        wp_send_json_error('You do not have permission to perform this action.');
    }
    
    $title = isset($_POST['title']) ? sanitize_text_field($_POST['title']) : '';
    $doc_type_id = isset($_POST['document_type']) ? intval($_POST['document_type']) : 0;
    
    if (empty($title) || !$doc_type_id) {
        wp_send_json_error('Title and document type are required.');
    }

    $doc_type = get_term($doc_type_id, 'doc_type');
    if (is_wp_error($doc_type)) {
        wp_send_json_error('Invalid document type.');
    }
    
    $field_type = get_term_meta($doc_type_id, 'doc_type_field_type', true);
    $content = '';
    
    // Handle different field types
    switch ($field_type) {
        case 'text':
            $content = isset($_POST['content']) ? sanitize_textarea_field($_POST['content']) : '';
            if (empty($content)) {
                wp_send_json_error('Content is required.');
            }
            break;
            
        case 'url':
            $content = isset($_POST['content']) ? esc_url_raw($_POST['content']) : '';
            if (empty($content) || !filter_var($content, FILTER_VALIDATE_URL)) {
                wp_send_json_error('Please enter a valid URL.');
            }
            break;
            
        case 'image':
        case 'pdf':
        case 'document':
            if (empty($_FILES['content'])) {
                wp_send_json_error('Please upload a file.');
            }
            
            $file = $_FILES['content'];
            $allowed_types = [];
            
            switch ($field_type) {
                case 'image':
                    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
                    break;
                case 'pdf':
                    $allowed_types = ['application/pdf'];
                    break;
                case 'document':
                    $allowed_types = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
                    break;
            }
            
            // Check file type
            $filetype = wp_check_filetype($file['name']);
            if (!in_array($filetype['type'], $allowed_types)) {
                wp_send_json_error('Invalid file type. Please upload the correct file format.');
            }
            
            // Upload file
            $upload = wp_upload_bits($file['name'], null, file_get_contents($file['tmp_name']));
            if ($upload['error']) {
                wp_send_json_error('Error uploading file: ' . $upload['error']);
            }
            
            $content = $upload['url'];
            break;
    }
    
    // Create the post
    $post_id = wp_insert_post(array(
        'post_title' => $title,
        'post_type' => 'tbyte_prm_assets',
        'post_status' => 'publish',
        'post_content' => $content,
    ));
    
    if (is_wp_error($post_id)) {
        wp_send_json_error('Error creating document: ' . $post_id->get_error_message());
    }
    
    // Assign the document type
    wp_set_object_terms($post_id, $doc_type_id, 'doc_type');
    
    wp_send_json_success(array(
        'message' => 'Document created successfully!',
        'redirect' => get_permalink($post_id)
    ));
}


// Unused?
add_action('wp_ajax_get_asset_data', 'handle_get_asset_data');
function handle_get_asset_data() {
    check_ajax_referer('save_asset_nonce', 'security');

    if (!current_user_can('edit_posts')) {
        wp_send_json_error('You do not have permission to perform this action.');
    }

    $asset_id = isset($_POST['asset_id']) ? intval($_POST['asset_id']) : 0;
    
    if ($asset_id <= 0) {
        wp_send_json_error('Invalid asset ID.');
    }

    $asset = get_post($asset_id);
    if (!$asset || $asset->post_type !== 'tbyte_prm_assets') {
        wp_send_json_error('Asset not found.');
    }

    // Get document type
    $doc_types = wp_get_post_terms($asset_id, 'doc_type');
    $doc_type_id = !empty($doc_types) ? $doc_types[0]->term_id : 0;
    $field_type = $doc_type_id ? get_term_meta($doc_type_id, 'doc_type_field_type', true) : '';

    // Get meta fields
    $language = get_post_meta($asset_id, 'language', true);
    $description = get_post_meta($asset_id, 'description', true);
    $tags = wp_get_post_tags($asset_id, ['fields' => 'names']);
    $tags_str = implode(', ', $tags);

    wp_send_json_success([
        'asset_id' => $asset_id,
        'asset_name' => $asset->post_title,
        'asset_doc_type' => $doc_type_id,
        'asset_language' => $language,
        'asset_tags' => $tags_str,
        'asset_status' => $asset->post_status,
        'asset_publish_date' => substr($asset->post_date, 0, 10),
        'asset_description' => $description,
        'asset_content' => $asset->post_content,
        'field_type' => $field_type
    ]);
}

add_action('wp_ajax_delete_asset', 'handle_delete_asset');
function handle_delete_asset() {
    check_ajax_referer('save_asset_nonce', 'security');

    if (!current_user_can('delete_posts')) {
        wp_send_json_error('You do not have permission to perform this action.');
    }

    $asset_id = isset($_POST['asset_id']) ? intval($_POST['asset_id']) : 0;
    
    if ($asset_id <= 0) {
        wp_send_json_error('Invalid asset ID.');
    }

    // Check if asset exists
    $asset = get_post($asset_id);
    if (!$asset || $asset->post_type !== 'tbyte_prm_assets') {
        wp_send_json_error('Asset not found.');
    }

    // Delete associated file if it's a file-based asset
    $doc_types = wp_get_post_terms($asset_id, 'doc_type');
    if (!empty($doc_types)) {
        $doc_type_id = $doc_types[0]->term_id;
        $field_type = get_term_meta($doc_type_id, 'doc_type_field_type', true);
        
        // If content is a file URL, delete the file
        if (in_array($field_type, ['image', 'pdf', 'document'])) {
            $content = $asset->post_content;
            if (filter_var($content, FILTER_VALIDATE_URL)) {
                $upload_dir = wp_upload_dir();
                $file_path = str_replace($upload_dir['baseurl'], $upload_dir['basedir'], $content);
                if (file_exists($file_path)) {
                    unlink($file_path);
                }
            }
        }
    }

    // Delete the post
    $result = wp_delete_post($asset_id, true);
    
    if (!$result) {
        wp_send_json_error('Failed to delete asset.');
    }

    wp_send_json_success('Asset deleted successfully.');
}



add_action('rest_api_init', function() {
    register_rest_route('prm/v1', '/tbyte_prm_assets/create', [
        'methods' => 'POST',
        'callback' => 'create_asset_rest',
        'permission_callback' => function() {
            return current_user_can('edit_posts');
        }
    ]);

    register_rest_route('prm/v1', '/tbyte_prm_assets', [
        'methods' => 'GET',
        'callback' => 'get_assets_data_rest',
        'permission_callback' => '__return_true',
        'args' => [
            'page' => [
                'required' => false,
                'validate_callback' => function($param, $request, $key) {
                    return is_numeric($param);
                }
            ],
            'posts_per_page' => [
                'required' => false,
                'validate_callback' => function($param, $request, $key) {
                    return is_numeric($param);
                }
            ]
        ]
    ]);

    register_rest_route('prm/v1', '/tbyte_prm_assets/(?P<id>\d+)', [
        'methods' => 'DELETE',
        'callback' => 'delete_asset_rest',
        'permission_callback' => function() {
            return current_user_can('delete_posts');
        }
    ]);
});

/**
 * Fetch assets data for the REST API.
 *
 * @param WP_REST_Request $request The request object.
 * @return WP_REST_Response
 */
function get_assets_data_rest($request) {
    $params = $request->get_params();

    $posts_per_page = $request->get_param('posts_per_page') ? intval($request->get_param('posts_per_page')) : 10;

    $search = $request->get_param('search') ? sanitize_text_field($request->get_param('search')) : '';
    $doc_type = $params['doc_type'] ?? [];
    $language = $params['language'] ?? [];
    $paged = $request->get_param('page') ? intval($request->get_param('page')) : 1;
    $posts_per_page = $request->get_param('posts_per_page') ? intval($request->get_param('posts_per_page')) : 10;

    // var_dump($request->get_params());
    // var_dump($request->get_params('language'));


    if ($paged < 1) {
        $paged = 1;
    }

    if ($posts_per_page < 1 || $posts_per_page > 50) {
        $posts_per_page = 10;
    }

    $tax_query = [];
    $args = [
        'post_type' => 'tbyte_prm_assets',
        'posts_per_page' => $posts_per_page,
        'paged' => $paged,
        'post_status' => 'publish',
        's' => $search,
    ];

    // var_dump($doc_type);

    if (!empty($doc_type)) {
        $tax_query[] = [
            'taxonomy' => 'doc_type',
            'field'    => 'slug',
            'terms'    => is_array($doc_type) ? $doc_type : explode(',', $doc_type),
            'operator' => 'IN',
        ];
    }

    // var_dump($language);

    if (!empty($language)) {
        $args['meta_query'] = [
            [
                'key' => 'language', 
                'value' => is_array($language) ? $language : explode(',', $language),
                'compare' => 'IN'
            ]
        ];
    }

    if (!empty($tax_query)) {
        $args['tax_query'] = array_merge(
            ['relation' => 'OR'],
            $tax_query
        );
    }

    $query = new WP_Query($args);

    if (is_wp_error($query)) {
        return new WP_Error('query_failed', $query->get_error_message(), ['status' => 500]);
    }

    if (!$query->have_posts()) {
        return new WP_REST_Response([
            'message' => 'We could not find any assets matching your criteria.',
            'items' => [],
            'pagination' => [
                'total' => 0,
                'total_pages' => 0,
                'current_page' => $paged,
                'per_page' => $posts_per_page,
            ],
        ], 200);
    }

    $assets = [];
    while ($query->have_posts()) {
        $query->the_post();
        $doc_type = wp_get_post_terms(get_the_ID(), 'doc_type');
        $doc_type_name = !empty($doc_type) ? $doc_type[0]->name : 'N/A';
        $language = get_post_meta(get_the_ID(), 'language', true);

        $assets[] = [
            'id' => get_the_ID(),
            'title' => get_the_title(),
            'content' => get_the_content(),
            'date' => get_the_date(),
            'author' => get_the_author(),
            'link' => get_permalink(),
            'url' => esc_url(get_post_meta(get_the_ID(), 'asset_url', true)),
            // 'tags' => wp_get_post_terms(get_the_ID(), 'prm_asset_tag', ['fields' => 'names']),
            'doc_type' => $doc_type_name,
            'language' => $language,
            'date' => get_the_date(),
        ];
    }
    wp_reset_postdata();

    $pagination = [
        'total' => (int) $query->found_posts,
        'total_pages' => (int) $query->max_num_pages,
        'current_page' => (int) $paged,
        'per_page' => (int) $posts_per_page,
    ];

    return new WP_REST_Response([
        'items' => $assets,
        'pagination' => $pagination,
        'args' => $args,
    ], 200);
}

function create_asset_rest($request) {
    $params = $request->get_params();

    $name = isset($params['asset_name']) ? sanitize_text_field($params['asset_name']) : '';
    $doc_type_id = isset($params['asset_doc_type']) ? intval($params['asset_doc_type']) : 0;
    $language = isset($params['asset_language']) ? sanitize_text_field($params['asset_language']) : '';
    $tags = isset($params['asset_tags']) ? sanitize_text_field($params['asset_tags']) : '';
    // $status = isset($params['asset_status']) ? sanitize_text_field($params['asset_status']) : 'published';
    $publish_date = isset($params['asset_publish_date']) ? sanitize_text_field($params['asset_publish_date']) : '';
    $description = isset($params['asset_description']) ? sanitize_textarea_field($params['asset_description']) : '';

    if (empty($name)) {
        return new WP_Error('creation_failed', 'Name is required.', ['status' => 400]);
    }

    if (empty($doc_type_id)) {
        return new WP_Error('creation_failed', 'Document type is required.', ['status' => 400]);
    }

    if (empty($language)) {
        return new WP_Error('creation_failed', 'Language is required.', ['status' => 400]);
    }

    $doc_type = get_term($doc_type_id, 'doc_type');
    if (is_wp_error($doc_type) || !$doc_type) {
        return new WP_Error('creation_failed', 'Invalid.', ['status' => 400]);
    }

    $field_type = get_term_meta($doc_type_id, 'doc_type_field_type', true);

    // TODO Check if there is already a file with the same name
    // FIXME: Check if the file already exists in the uploads directory
    // TODO File must appear in media library
    // Handle content based on field type
    switch ($field_type) {
        case 'text':
            $content = isset($_POST['asset_content']) ? sanitize_textarea_field($_POST['asset_content']) : '';
            if (empty($content)) {
                wp_send_json_error('Content is required for this document type.');
            }
            break;

        case 'url':
            $content = isset($_POST['asset_content']) ? esc_url_raw($_POST['asset_content']) : '';
            if (empty($content) || !filter_var($content, FILTER_VALIDATE_URL)) {
                wp_send_json_error('Please enter a valid URL.');
            }
            break;

        case 'image':
        case 'pdf':
        case 'document':
            if (empty($_FILES['asset_content'])) {
                wp_send_json_error('Please upload a file.');
            }

            $file = $_FILES['asset_content'];
            $allowed_types = [];
            $max_size = 5 * 1024 * 1024; // 5MB

            switch ($field_type) {
                case 'image':
                    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
                    break;
                case 'pdf':
                    $allowed_types = ['application/pdf'];
                    break;
                case 'document':
                    $allowed_types = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
                    break;
            }

            // Check file type and size
            $filetype = wp_check_filetype($file['name']);
            if (!in_array($filetype['type'], $allowed_types)) {
                wp_send_json_error('Invalid file type. Please upload the correct file format.');
            }

            if ($file['size'] > $max_size) {
                wp_send_json_error('File size exceeds maximum limit of 5MB.');
            }

            // Upload file
            $upload = wp_upload_bits($file['name'], null, file_get_contents($file['tmp_name']));
            if ($upload['error']) {
                wp_send_json_error('Error uploading file: ' . $upload['error']);
            }

            $content = $upload['url'];
            break;

        default:
            wp_send_json_error('Invalid document type configuration.');
    }

    // Prepare post data
    $post_data = [
        'post_title' => $name,
        'post_content' => $content,
        'post_status' => 'publish',
        'post_type' => 'tbyte_prm_assets',
    ];

    // Set publish date if provided
    if (!empty($publish_date)) {
        $post_data['post_date'] = $publish_date . ' 00:00:00';
    }

    // Update or create post
    $post_id = wp_insert_post($post_data, true);

    if (is_wp_error($post_id)) {
        return new WP_Error('creation_failed', $post_id->get_error_message(), ['status' => 400]);
    }
    
    // Set document type taxonomy
    wp_set_object_terms($post_id, $doc_type_id, 'doc_type');

    // Save meta fields
    update_post_meta($post_id, 'language', $language);
    update_post_meta($post_id, 'description', $description);
    
    // Handle tags
    if (!empty($tags)) {
        $tags_array = array_map('trim', explode(',', $tags));
        wp_set_post_tags($post_id, $tags_array, false);
    }
    
    return [
        'success' => true,
        'message' => 'Asset saved successfully!',
        'post_id' => $post_id
    ];
}


add_action('rest_api_init', function () {
    register_rest_route('prm/v1', '/document_type/(?P<id>\d+)', array(
        'methods'  => 'DELETE',
        'callback' => 'delete_document_type_rest',
        'permission_callback' => function () {
            return current_user_can('manage_options'); // Only allow admins
        },
    ));
});

function delete_document_type_rest(WP_REST_Request $request) {
    $term_id = $request->get_param('id');

    if (empty($term_id)) {
        return new WP_REST_Response([
            'success' => false,
            'message' => 'Invalid term ID.',
        ], 400);
    }
    
    $deleted = wp_delete_term($term_id, 'doc_type');
    
    if (is_wp_error($deleted)) {
        return new WP_REST_Response([
            'success' => false,
            'message' => $deleted->get_error_message(),
        ], 400);
    }
    
    return new WP_REST_Response([
        'success' => true,
        'message' => 'Document type deleted successfully!',
    ], 200);
}


add_action('rest_api_init', function() {
    // GET endpoint for fetching term data
    register_rest_route('prm/v1', '/document_type/(?P<id>\d+)', [
        'methods' => 'GET',
        'callback' => 'get_document_type_data_rest',
        'permission_callback' => function() {
            return current_user_can('edit_posts');
        }
    ]);

    // POST/PUT endpoint for updating term data
    register_rest_route('prm/v1', '/document_type/(?P<id>\d+)', [
        'methods' => 'POST',
        'callback' => 'update_document_type_rest',
        'permission_callback' => function() {
            return current_user_can('edit_posts');
        }
    ]);

    register_rest_route('prm/v1', '/document_type', [
        'methods' => 'POST',
        'callback' => 'create_document_type_rest',
        'permission_callback' => function() {
            return current_user_can('edit_posts');
        }
    ]);

    register_rest_route('prm/v1', '/document_type/check-name', [
        'methods' => 'GET',
        'callback' => 'check_document_type_name',
        'permission_callback' => '__return_true',
        'args' => [
            'name' => [
                'required' => true,
                'sanitize_callback' => 'sanitize_text_field'
            ]
        ]
    ]);
});

function get_document_type_data_rest($request) {
    $term_id = $request['id'];
    $term = get_term($term_id, 'doc_type'); // Change to your taxonomy
    
    if (is_wp_error($term)) {
        return new WP_Error('term_not_found', 'Invalid term ID', ['status' => 404]);
    }
    
    return [
        'term_id' => $term->term_id,
        'name' => $term->name,
        'slug' => $term->slug,
        'parent' => $term->parent,
        'description' => $term->description
    ];
}

function update_document_type_rest($request) {
    $term_id = $request['id'];
    $params = $request->get_params();
    
    $args = [
        'name' => sanitize_text_field($params['name']),
        'slug' => sanitize_title($params['slug']),
        'parent' => intval($params['parent']),
        'description' => sanitize_textarea_field($params['description'])
    ];
    
    $updated = wp_update_term($term_id, 'doc_type', $args);
    
    if (is_wp_error($updated)) {
        return new WP_Error('update_failed', $updated->get_error_message(), ['status' => 400]);
    }
    
    return [
        'success' => true,
        'message' => 'Document type updated'
    ];
}

function create_document_type_rest($request) {
    $params = $request->get_params();

    $name = isset($params['name']) ? sanitize_text_field($params['name']) : '';

    if (empty($name)) {
        // wp_send_json_error('Name is required.');
        return new WP_Error('creation_failed', 'Name is required.', ['status' => 400]);
    }
    
    $args = [
        'name' => $name,
        'slug' => sanitize_title($params['slug']),
        'parent' => intval($params['parent']),
        'description' => sanitize_textarea_field($params['description'])
    ];
    
    $created = wp_insert_term($name, 'doc_type', $args);
    
    if (is_wp_error($created)) {
        return new WP_Error('creation_failed', $created->get_error_message(), ['status' => 400]);
    }

    $term_id = isset($created['term_id']) ? $created['term_id'] : 0;

    if ($term_id && isset($params['field_type'])) {
        update_term_meta($term_id, 'doc_type_field_type', sanitize_text_field($params['field_type']));
    }
    
    return [
        'success' => true,
        'message' => 'Document type created',
        'term_id' => $term_id,
    ];
}

/**
 * Delete an asset by the ID.
 *
 * @param WP_REST_Request $request The request object.
 * @return array
 */
function delete_asset_rest($request) {
    $asset_id = $request['id'];

    // Check permission
    if (!current_user_can('delete_posts')) {
        return new WP_Error('permission_denied', 'You do not have permission.', ['status' => 403]);
    }

    if (empty($asset_id)) {
        return new WP_Error('deletion_failed', 'Invalid asset ID.', ['status' => 400]);
    }

    $deleted = wp_delete_post($asset_id, true);

    if (!$deleted) {
        return new WP_Error('deletion_failed', 'Failed to delete asset.', ['status' => 400]);
    }

    return [
        'success' => true,
        'message' => 'Asset deleted successfully!'
    ];
}

function check_document_type_name($request) {
    $name = $request->get_param('name');
    $term = get_term_by('name', $name, 'doc_type');
    
    return [
        'exists' => !empty($term),
        'suggestions' => !empty($term) ? [
            'id' => $term->term_id,
            'slug' => $term->slug
        ] : null
    ];
}





add_action('rest_api_init', function() {
    // GET endpoint for fetching term data
    register_rest_route('prm/v1', '/tbyte_prm_asset_language/(?P<id>\d+)', [
        'methods' => 'GET',
        'callback' => 'get_language_data_rest',
        'permission_callback' => function() {
            return current_user_can('edit_posts');
        }
    ]);

    // POST/PUT endpoint for updating term data
    register_rest_route('prm/v1', '/tbyte_prm_asset_language/(?P<id>\d+)', [
        'methods' => 'PUT',
        'callback' => 'update_language_rest',
        'permission_callback' => function() {
            return current_user_can('edit_posts');
        }
    ]);

    register_rest_route('prm/v1', '/tbyte_prm_asset_language', [
        'methods' => 'POST',
        'callback' => 'create_language_rest',
        'permission_callback' => function() {
            return current_user_can('edit_posts');
        }
    ]);

    // register_rest_route('prm/v1', '/document_type/check-name', [
    //     'methods' => 'GET',
    //     'callback' => 'check_document_type_name',
    //     'permission_callback' => '__return_true',
    //     'args' => [
    //         'name' => [
    //             'required' => true,
    //             'sanitize_callback' => 'sanitize_text_field'
    //         ]
    //     ]
    // ]);

    register_rest_route('prm/v1', '/tbyte_prm_asset_language/check-name', [
        'methods' => 'GET',
        'callback' => function($request) {
            $name = $request->get_param('name');
            $term = get_term_by('name', $name, 'language');
            
            return [
                'exists' => !empty($term),
                'suggestions' => !empty($term) ? [
                    'id' => $term->term_id,
                    'slug' => $term->slug
                ] : null
            ];
        },
        'permission_callback' => '__return_true',
        'args' => [
            'name' => [
                'required' => true,
                'sanitize_callback' => 'sanitize_text_field'
            ]
        ]
    ]);

    register_rest_route('prm/v1', '/tbyte_prm_asset_language/delete/(?P<id>\d+)', [
        'methods' => 'DELETE',
        'callback' => 'delete_language_rest',
        'permission_callback' => function() {
            return current_user_can('manage_options'); // Only allow admins
        }
    ]);
});

function get_language_data_rest($request) {
    $term_id = $request['id'];
    $term = get_term($term_id, 'language'); // Change to your taxonomy
    
    if (is_wp_error($term)) {
        return new WP_Error('term_not_found', 'Invalid term ID', ['status' => 404]);
    }
    
    return [
        'term_id' => $term->term_id,
        'name' => $term->name
    ];
}

function update_language_rest($request) {
    $term_id = $request['id'];
    $params = $request->get_params();
    
    $name = isset($params['name']) ? sanitize_text_field($params['name']) : '';
    
    if (empty($name)) {
        return new WP_Error('update_failed', 'Name is required.', ['status' => 400]);
    }
    
    $args = [
        'name' => $name
    ];
    
    $updated = wp_update_term($term_id, 'language', $args);
    
    if (is_wp_error($updated)) {
        return new WP_Error('update_failed', $updated->get_error_message(), ['status' => 400]);
    }
    
    return [
        'success' => true,
        'message' => 'Language updated successfully',
        'term_id' => $term_id,
    ];
}

function create_language_rest($request) {
    $params = $request->get_params();

    $name = isset($params['name']) ? sanitize_text_field($params['name']) : '';

    if (empty($name)) {
        // wp_send_json_error('Name is required.');
        return new WP_Error('creation_failed', 'Name is required.', ['status' => 400]);
    }
    
    $created = wp_insert_term($name, 'language', [
        'slug' => sanitize_title($name),
        'description' => $params['description'] ?? ''
    ]);
    
    if (is_wp_error($created)) {
        return new WP_Error('creation_failed', $created->get_error_message(), ['status' => 400]);
    }

    clean_term_cache($created['term_id'], 'language');

    $term_id = isset($created['term_id']) ? $created['term_id'] : 0;
    
    return [
        'success' => true,
        'message' => 'Document type created',
        'term_id' => $term_id,
    ];
}

function delete_language_rest($request) {
    $term_id = $request['id'];

    if (empty($term_id)) {
        return new WP_Error('deletion_failed', 'Invalid term ID.', ['status' => 400]);
    }

    $deleted = wp_delete_term($term_id, 'language');

    if (is_wp_error($deleted)) {
        return new WP_Error('deletion_failed', $deleted->get_error_message(), ['status' => 400]);
    }

    return [
        'success' => true,
        'message' => 'Language deleted successfully!'
    ];
}