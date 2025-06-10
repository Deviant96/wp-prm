<?php
$current_user = wp_get_current_user();

if (!in_array('partner_manager', $current_user->roles) && !in_array('administrator', $current_user->roles)) {
    echo '<p class="text-red-500">Access Denied.</p>';
    return;
}
?>

<h1 class="text-3xl font-bold text-gray-800  mb-6">Create New Event</h1>

<!-- Event Form -->
<div id="eventFormContainer" class="bg-white  p-8 rounded-lg shadow-lg border border-gray-200 ">
    <h3 class="text-2xl font-semibold mb-6 text-gray-800  border-b pb-4 border-gray-200 ">Event Details</h3>
    <form id="eventForm" class="space-y-6" enctype="multipart/form-data">
        <input type="hidden" name="event_id" id="event_id" value="">

        <!-- Title Row -->
        <div>
            <label for="event_title" class="block text-sm font-medium text-gray-700  mb-2">Event Title*</label>
            <input type="text" name="event_title" id="event_title" required
                class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500       transition duration-200"
                placeholder="Enter event title">
        </div>

        <!-- Grid Row 1: Event Type, Venue, Status -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Event Type -->
            <div>
                <label for="event_type" class="block text-sm font-medium text-gray-700  mb-2">Event Type*</label>
                <select name="event_type" id="event_type" required
                    class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500       transition duration-200">
                    <option value="" selected disabled>Select Event Type</option>
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

            <!-- Venue -->
            <div>
                <label for="event_venue" class="block text-sm font-medium text-gray-700  mb-2">Venue*</label>
                <input type="text" name="event_venue" id="event_venue" required
                    class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500       transition duration-200"
                    placeholder="Enter venue">
            </div>

            <!-- Event Status -->
            <div>
                <label for="event_status" class="block text-sm font-medium text-gray-700  mb-2">Status*</label>
                <select name="event_status" id="event_status" required
                    class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500       transition duration-200">
                    <option value="draft">Draft</option>
                    <option value="publish" selected>Publish</option>
                    <option value="archived">Archived</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>
        </div>

        <!-- Grid Row 2: Date, Start Time, End Time -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Event Date -->
            <div>
                <label for="event_date" class="block text-sm font-medium text-gray-700  mb-2">Event Date*</label>
                <input type="date" name="event_date" id="event_date" required
                    class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500       transition duration-200">
            </div>

            <!-- Start Time -->
            <div>
                <label for="start_time" class="block text-sm font-medium text-gray-700  mb-2">Start Time*</label>
                <input type="time" name="start_time" id="start_time" required
                    class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500       transition duration-200">
            </div>

            <!-- End Time -->
            <div>
                <label for="end_time" class="block text-sm font-medium text-gray-700  mb-2">End Time*</label>
                <input type="time" name="end_time" id="end_time" required
                    class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500       transition duration-200">
            </div>
        </div>

        <!-- Grid Row 3: URL, Tags -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Event URL -->
            <div>
                <label for="event_url" class="block text-sm font-medium text-gray-700  mb-2">Event URL</label>
                <input type="url" name="event_url" id="event_url"
                    class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500       transition duration-200"
                    placeholder="https://example.com/event">
                <small class="text-gray-500 ">If not filled, then Event URL will use default internal link.</small>
            </div>

            <!-- Tags -->
            <div>
                <label for="event_tags" class="block text-sm font-medium text-gray-700  mb-2">Tags</label>
                <input type="text" name="event_tags" id="event_tags"
                    class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500       transition duration-200"
                    placeholder="Comma separated tags (e.g., music, conference, workshop)">
            </div>
        </div>

        <!-- Description -->
        <div>
            <label for="event_content" class="block text-sm font-medium text-gray-700  mb-2">Description*</label>
            <textarea name="event_content" id="event_content" rows="5" required
                class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500       transition duration-200"
                placeholder="Enter detailed event description"></textarea>
        </div>

        <input type="hidden" name="action" value="save_event">
        <?php wp_nonce_field('save_event_nonce', 'security'); ?>

        <!-- Form Actions -->
        <div class="flex justify-end gap-4 pt-4 border-t border-gray-200 ">
            <a href="<?php echo home_url('/?tab=events-manage'); ?>" id="cancelEventBtn" class="px-6 py-2.5 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg font-medium    transition duration-200">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-200  transition duration-200">
                Create Event
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
            const response = await fetch(`${wpApiSettings.root}prm/v1/tbyte_prm_events`, {
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
            showError('Failed to create event. Please try again.');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    }

    document.getElementById('eventForm').addEventListener('submit', submitEventForm);
</script>