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
<div id="assetFormContainer" class="bg-gray-50 dark:bg-gray-800 p-6 rounded-lg shadow">
    <h3 class="text-xl font-semibold mb-4 text-gray-800 dark:text-white">Add New Asset</h3>
    <form id="assetForm" class="space-y-4" enctype="multipart/form-data">
        <input type="hidden" name="asset_id" id="asset_id" value="">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Asset Name -->
            <div>
                <label for="asset_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Asset Name*</label>
                <input type="text" name="asset_name" id="asset_name" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            </div>

            <!-- Document Type -->
            <div>
                <label for="asset_doc_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Document Type*</label>
                <select name="asset_doc_type" id="asset_doc_type" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
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
                <label for="asset_language" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Language*</label>
                <select name="asset_language" id="asset_language" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
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
                <label for="asset_tags" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tags</label>
                <input type="text" name="asset_tags" id="asset_tags"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    placeholder="Comma separated tags">
            </div>
        </div>

        <!-- Dynamic Content Field (changes based on document type) -->
        <div id="asset-content-field">
            <div class="bg-gray-100 dark:bg-gray-700 p-4 rounded text-center text-gray-500 dark:text-gray-300">
                Please select a document type to see specific requirements
            </div>
        </div>

        <!-- Additional Fields -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Status -->
            <div>
                <label for="asset_status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                <select name="asset_status" id="asset_status"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="draft">Draft</option>
                    <option value="published" selected>Published</option>
                    <option value="archived">Archived</option>
                </select>
            </div>

            <!-- Publish Date -->
            <div>
                <label for="asset_publish_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Publish Date</label>
                <input type="date" name="asset_publish_date" id="asset_publish_date"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            </div>
        </div>

        <!-- Description -->
        <div>
            <label for="asset_description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
            <textarea name="asset_description" id="asset_description" rows="3"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
        </div>

        <input type="hidden" name="action" value="save_asset">
        <?php wp_nonce_field('save_asset_nonce', 'security'); ?>

        <div class="flex justify-end gap-2">
            <button type="button" id="cancelAssetBtn" class="px-4 py-2 text-gray-600 hover:text-gray-800 dark:text-gray-300 dark:hover:text-white">
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

    function submitAssetForm(e) {
        e.preventDefault();
        const form = e.target;
        const formData = new FormData(form);

        fetch(ajax_object.ajax_url + `?action=save_asset`, {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                alert(data.message);
                form.reset();
                toggleAssetForm();
                // loadAssets();
            });
    }

    // document.addEventListener('DOMContentLoaded', loadAssets);
</script>