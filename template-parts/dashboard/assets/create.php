<?php
$current_user = wp_get_current_user();

// Check if current user is Partner Manager
if (!in_array('partner_manager', $current_user->roles) && !in_array('administrator', $current_user->roles)) {
    echo '<p class="text-red-500">Access Denied.</p>';
    return;
}
?>

<h1>Create New Asset</h1>

<!-- Add/Edit Form -->
<div id="assetFormContainer" class="bg-gray-50  p-6 rounded-lg shadow">
    <h3 class="text-xl font-semibold mb-4 text-gray-800 ">Add New Asset</h3>
    <form id="assetForm" class="space-y-4" enctype="multipart/form-data">
        <input type="hidden" name="asset_id" id="asset_id" value="">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Asset Name -->
            <div>
                <label for="asset_name" class="block text-sm font-medium text-gray-700 ">Asset Name*</label>
                <input type="text" name="asset_name" id="asset_name" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500   ">
            </div>

            <!-- Document Type -->
            <div>
                <label for="asset_doc_type" class="block text-sm font-medium text-gray-700 ">Document Type*</label>
                <select name="asset_doc_type" id="asset_doc_type" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500   ">
                    <option value="">Select Document Type</option>
                    <?php
                    $doc_types = get_terms(array(
                        'taxonomy' => 'doc_type',
                        'hide_empty' => false,
                    ));

                    foreach ($doc_types as $type) {
                        echo '<option value="' . $type->term_id . '" data-field-type="' . get_term_meta($type->term_id, 'doc_type_field_type', true) . '">' . $type->name . '</option>';
                    }
                    ?>
                </select>
            </div>

            <!-- Language -->
            <div>
                <label for="asset_language" class="block text-sm font-medium text-gray-700 ">Language*</label>
                <select name="asset_language" id="asset_language" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500   ">
                    <option value="">Select Language</option>
                    <?php
                    $languages = get_terms([
                        'taxonomy' => 'language',
                        'hide_empty' => false,
                    ]);

                    foreach ($languages as $language) {
                        $language_code = get_post_meta($language->ID, 'language_code', true);
                        echo '<option value="' . esc_attr($language_code ?: $language->name) . '">' . esc_html($language->name) . '</option>';
                    }
                    ?>
                </select>
            </div>

            <!-- Tags -->
            <div>
                <label for="asset_tags" class="block text-sm font-medium text-gray-700 ">Tags</label>
                <input type="text" name="asset_tags" id="asset_tags"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500   "
                    placeholder="Comma separated tags">
            </div>
        </div>

        <!-- Dynamic Content Field (changes based on document type) -->
        <div id="asset-content-field">
            <div class="bg-gray-100  p-4 rounded text-center text-gray-500 ">
                Please select a document type to see specific requirements
            </div>
        </div>

        <!-- Additional Fields -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Status -->
            <div>
                <label for="asset_status" class="block text-sm font-medium text-gray-700 ">Status</label>
                <select name="asset_status" id="asset_status"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500   ">
                    <option value="draft">Draft</option>
                    <option value="published" selected>Published</option>
                    <option value="archived">Archived</option>
                </select>
            </div>

            <!-- Publish Date -->
            <div>
                <label for="asset_publish_date" class="block text-sm font-medium text-gray-700 ">Publish Date</label>
                <input type="date" name="asset_publish_date" id="asset_publish_date"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500   ">
            </div>
        </div>

        <!-- Description -->
        <div>
            <label for="asset_description" class="block text-sm font-medium text-gray-700 ">Description</label>
            <textarea name="asset_description" id="asset_description" rows="3"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500   "></textarea>
        </div>

        <input type="hidden" name="action" value="save_asset">
        <?php wp_nonce_field('save_asset_nonce', 'security'); ?>

        <div class="flex justify-end gap-2">
            <button type="button" id="cancelAssetBtn" class="px-4 py-2 text-gray-600 hover:text-gray-800  ">
                Cancel
            </button>
            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition">
                Save Asset
            </button>
        </div>
    </form>
</div>

<script>
    function toggleAssetForm() {
        document.getElementById('asset-form').classList.toggle('hidden');
    }

    async function submitAssetForm(e) {
        e.preventDefault();

        const form = this;
        const formData = new FormData(form);

        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner"></span> Creating...';

        try {
            const response = await fetch(`${wpApiSettings.root}prm/v1/tbyte_prm_assets/create`, {
                method: 'POST',
                headers: {
                    'X-WP-Nonce': wpApiSettings.nonce
                },
                body: formData,
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'Error saving asset');
            }
            
            showSuccess('Document type created successfully!');
            form.reset();

            // TODO Redirect to the asset list page or show the new asset
        } catch (error) {
            showError('Failed to create asset. Please try again.');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    }

    document.getElementById('assetForm').addEventListener('submit', submitAssetForm);
</script>

<script>
    // Dynamic field based on document type
    document.getElementById('asset_doc_type').addEventListener('change', function() {
    var selectedOption = this.options[this.selectedIndex];
    var fieldType = selectedOption.dataset.fieldType;
    var fieldHtml = '';
    
    switch(fieldType) {
        case 'text':
            fieldHtml = `
                <div class="bg-gray-50  p-4 rounded">
                    <label class="block text-sm font-medium text-gray-700 ">Content*</label>
                    <textarea name="asset_content" required rows="5" 
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500   "></textarea>
                    <p class="mt-1 text-sm text-gray-500 ">Enter the text content for this asset.</p>
                </div>
            `;
            break;
            
        case 'url':
            fieldHtml = `
                <div class="bg-gray-50  p-4 rounded">
                    <label class="block text-sm font-medium text-gray-700 ">URL*</label>
                    <input type="url" name="asset_content" required 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500   "
                           placeholder="https://example.com">
                    <p class="mt-1 text-sm text-gray-500 ">Enter the URL for this asset.</p>
                </div>
            `;
            break;
            
        case 'image':
            fieldHtml = `
                <div class="bg-gray-50  p-4 rounded">
                    <label class="block text-sm font-medium text-gray-700 ">Image File* (.jpg, .png, .gif)</label>
                    <input type="file" name="asset_content" accept=".jpg,.jpeg,.png,.gif" required 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100  ">
                    <p class="mt-1 text-sm text-gray-500 ">Upload an image file (JPG, PNG, or GIF).</p>
                </div>
            `;
            break;
            
        case 'pdf':
            fieldHtml = `
                <div class="bg-gray-50  p-4 rounded">
                    <label class="block text-sm font-medium text-gray-700 ">PDF File* (.pdf)</label>
                    <input type="file" name="asset_content" accept=".pdf" required 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100  ">
                    <p class="mt-1 text-sm text-gray-500 ">Upload a PDF document.</p>
                </div>
            `;
            break;
            
        case 'document':
            fieldHtml = `
                <div class="bg-gray-50  p-4 rounded">
                    <label class="block text-sm font-medium text-gray-700 ">Document File* (.doc, .docx, .pdf)</label>
                    <input type="file" name="asset_content" accept=".doc,.docx,.pdf" required 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100  ">
                    <p class="mt-1 text-sm text-gray-500 ">Upload a document (DOC, DOCX, or PDF).</p>
                </div>
            `;
            break;
            
        default:
            fieldHtml = '<div class="bg-gray-100  p-4 rounded text-center text-gray-500 ">Please select a document type to see specific requirements</div>';
    }
    
    document.getElementById('asset-content-field').innerHTML = fieldHtml;
});
</script>