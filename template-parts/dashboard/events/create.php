<?php
$current_user = wp_get_current_user();

if (!in_array('partner_manager', $current_user->roles) && !in_array('administrator', $current_user->roles)) {
    echo '<p class="text-red-500">Access Denied.</p>';
    return;
}
?>

<h1>Create New Event</h1>

<!-- Event Form -->
<div id="eventFormContainer" class="bg-gray-50 dark:bg-gray-800 p-6 rounded-lg shadow">
    <h3 class="text-xl font-semibold mb-4 text-gray-800 dark:text-white">Add New Event</h3>
    <form id="eventForm" class="space-y-4" enctype="multipart/form-data">
        <input type="hidden" name="event_id" id="event_id" value="">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Event Name -->
            <div>
                <label for="event_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Event Name*</label>
                <input type="text" name="event_name" id="event_name" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            </div>

            <!-- Event Type -->
            <div>
                <label for="event_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Event Type*</label>
                <select name="event_type" id="event_type" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="">Select Event Type</option>
                    <?php
                    $event_types = get_terms(array(
                        'taxonomy' => 'event_type',
                        'hide_empty' => false,
                    ));

                    foreach ($event_types as $type) {
                        echo '<option value="' . esc_attr($type->term_id) . '">' . esc_html($type->name) . '</option>';
                    }
                    ?>
                </select>
            </div>

            <!-- Event Location -->
            <div>
                <label for="event_location" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Location*</label>
                <input type="text" name="event_location" id="event_location" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            </div>

            <!-- Tags -->
            <div>
                <label for="event_tags" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tags</label>
                <input type="text" name="event_tags" id="event_tags"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    placeholder="Comma separated tags">
            </div>
        </div>

        <!-- Event Date -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="event_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Event Date*</label>
                <input type="date" name="event_date" id="event_date" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            </div>

            <!-- Event Status -->
            <div>
                <label for="event_status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                <select name="event_status" id="event_status"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="draft">Draft</option>
                    <option value="published" selected>Published</option>
                    <option value="archived">Archived</option>
                </select>
            </div>
        </div>

        <!-- Description -->
        <div>
            <label for="event_description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
            <textarea name="event_description" id="event_description" rows="3"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
        </div>

        <input type="hidden" name="action" value="save_event">
        <?php wp_nonce_field('save_event_nonce', 'security'); ?>

        <div class="flex justify-end gap-2">
            <button type="button" id="cancelEventBtn" class="px-4 py-2 text-gray-600 hover:text-gray-800 dark:text-gray-300 dark:hover:text-white">
                Cancel
            </button>
            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition">
                Save Event
            </button>
        </div>
    </form>
</div>

<script>
    async function submitEventForm(e) {
        e.preventDefault();

        const form = this;
        const formData = new FormData(form);

        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner"></span> Creating...';

        try {
            const response = await fetch(`${wpApiSettings.root}prm/v1/events/create`, {
                method: 'POST',
                headers: {
                    'X-WP-Nonce': wpApiSettings.nonce
                },
                body: formData,
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'Error saving event');
            }

            showSuccess('Event created successfully!');
            form.reset();
            // Optional: Redirect or refresh list
        } catch (error) {
            console.error('Error:', error);
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    }

    document.getElementById('eventForm').addEventListener('submit', submitEventForm);
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
                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Content*</label>
                    <textarea name="asset_content" required rows="5" 
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Enter the text content for this asset.</p>
                </div>
            `;
            break;
            
        case 'url':
            fieldHtml = `
                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">URL*</label>
                    <input type="url" name="asset_content" required 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                           placeholder="https://example.com">
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Enter the URL for this asset.</p>
                </div>
            `;
            break;
            
        case 'image':
            fieldHtml = `
                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Image File* (.jpg, .png, .gif)</label>
                    <input type="file" name="asset_content" accept=".jpg,.jpeg,.png,.gif" required 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-gray-600 dark:file:text-gray-100">
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Upload an image file (JPG, PNG, or GIF).</p>
                </div>
            `;
            break;
            
        case 'pdf':
            fieldHtml = `
                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">PDF File* (.pdf)</label>
                    <input type="file" name="asset_content" accept=".pdf" required 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-gray-600 dark:file:text-gray-100">
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Upload a PDF document.</p>
                </div>
            `;
            break;
            
        case 'document':
            fieldHtml = `
                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Document File* (.doc, .docx, .pdf)</label>
                    <input type="file" name="asset_content" accept=".doc,.docx,.pdf" required 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-gray-600 dark:file:text-gray-100">
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Upload a document (DOC, DOCX, or PDF).</p>
                </div>
            `;
            break;
            
        default:
            fieldHtml = '<div class="bg-gray-100 dark:bg-gray-700 p-4 rounded text-center text-gray-500 dark:text-gray-300">Please select a document type to see specific requirements</div>';
    }
    
    document.getElementById('asset-content-field').innerHTML = fieldHtml;
});
</script>