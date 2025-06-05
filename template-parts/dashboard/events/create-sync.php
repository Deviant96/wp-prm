<?php
/* Dashboard Event Sync Template */
$regions = [
    'sg' => 'Singapore',
    'id' => 'Indonesia',
    'vn' => 'Vietnam',
    'my' => 'Malaysia',
    'ph' => 'Philippines'
];
$success = get_transient('event_sync_success'); // Success message handler
$errors = get_transient('event_sync_errors'); // Error handler
?>

<div class="max-w-4xl mx-auto p-6 bg-white rounded-lg shadow-md">
    <!-- Error Display -->
    <?php if (isset($sync_errors)) : ?>
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
            <h3 class="font-bold mb-2">Sync Errors:</h3>
            <ul class="list-disc pl-4">
                <?php foreach ($sync_errors as $error) : ?>
                    <li><?= $error ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if ($success = get_transient('event_sync_success')) : ?>
        <div class="bg-green-100 p-4 mb-4">
            <?= $success ?>
        </div>
        <?php delete_transient('event_sync_success'); ?>
    <?php endif; ?>

    <!-- Sync Form -->
    <form method="post" action="<?= admin_url('admin-post.php') ?>" class="space-y-4">
        <input type="hidden" name="action" value="sync_event">
        <!-- Region Selection -->
        <div class="border p-4 rounded">
            <label class="block text-sm font-medium mb-2">Publish to Regions:</label>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                <?php foreach ($regions as $code => $name) : ?>
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="regions[]" value="<?= $code ?>" 
                               class="form-checkbox h-4 w-4 text-indigo-600">
                        <span class="ml-2"><?= $name ?></span>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Event Details -->
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium">Event Title</label>
                <input type="text" name="event_title" required
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium">Start Date</label>
                    <input type="datetime-local" name="start_date" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium">End Date</label>
                    <input type="datetime-local" name="end_date" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
            </div>
        </div>

        <button type="submit" name="sync_event" 
                class="w-full bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700 transition">
            Publish Event
        </button>
    </form>
</div>