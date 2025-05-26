<?php
$current_user = wp_get_current_user();

if (!in_array('partner_manager', $current_user->roles) && !in_array('administrator', $current_user->roles)) {
    echo '<p class="text-red-500">You do not have permission to access this page.</p>';
    return;
}

?>
<style>
    /* Loading Spinner */
    .spinner {
        display: inline-block;
        width: 14px;
        height: 14px;
        border: 2px solid rgba(255,255,255,0.3);
        border-radius: 50%;
        border-top-color: white;
        animation: spin 1s ease-in-out infinite;
        margin-right: 8px;
        vertical-align: middle;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    button .spinner {
        border-top-color: currentColor;
    }
</style>

<div class="space-y-6">
    <!-- Header & Add Button -->
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-800 ">Language Management</h2>
        <button id="show-create-form" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
            Add New Language
        </button>
    </div>

    <!-- Create Form (hidden by default) -->
    <div id="create-language-form" class="hidden bg-gray-50  p-6 rounded-lg shadow">
        <h3 class="text-xl font-semibold mb-4 text-gray-800 ">Add New Language</h3>
        <form id="new-language-form" class="space-y-4">
            <div>
                <label for="language-name" class="block text-sm font-medium text-gray-700 ">Language*</label>
                <input type="text" id="language-name" name="name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500   ">
            </div>
            
            <input type="hidden" name="action" value="create_document_type">
            
            <div class="flex justify-end gap-2">
                <button type="button" id="cancel-create" class="px-4 py-2 text-gray-600 hover:text-gray-800  ">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition">
                    Add Document Type
                </button>
            </div>
        </form>
    </div>

    <!-- Languages Table -->
    <div id="language-list" class="overflow-auto rounded-lg shadow">
        <table class="min-w-full divide-y divide-gray-200 ">
            <thead class="bg-gray-100 ">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500  uppercase tracking-wider">Language</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500  uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white  divide-y divide-gray-200 ">
                <?php
                $terms = get_terms(array(
                    'taxonomy' => 'language',
                    'hide_empty' => false,
                    'orderby' => 'name',
                ));
                
                foreach ($terms as $term) {                    
                    echo '<tr data-term-id="' . $term->term_id . '" class="hover:bg-gray-50 ">';
                    echo '<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 ">' . $term->name . '</td>';
                    echo '<td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">';
                    echo '<button class="edit-language text-blue-600 hover:text-blue-900   mr-3" data-term-id="' . $term->term_id . '"><ion-icon name="create-outline"></ion-icon></button>';
                    echo '<button class="delete-language text-red-600 hover:text-red-900  " data-term-id="' . $term->term_id . '"><ion-icon name="trash-outline"></ion-icon></button>';
                    echo '</td>';
                    echo '</tr>';
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Edit Form (hidden by default) -->
    <div id="edit-language-form" class="hidden bg-gray-50  p-6 rounded-lg shadow">
        <h3 class="text-xl font-semibold mb-4 text-gray-800 ">Edit Language</h3>
        <form id="update-language-form" class="space-y-4">
            <div>
                <label for="edit-language-name" class="block text-sm font-medium text-gray-700 ">Name*</label>
                <input type="text" id="edit-language-name" name="name" required 
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500   ">
            </div>            
            
            <input type="hidden" id="edit-language-id" name="term_id">
            <input type="hidden" name="action" value="update_language">
            
            <div class="flex justify-end gap-2">
                <button type="button" id="cancel-edit" class="px-4 py-2 text-gray-600 hover:text-gray-800  ">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition">
                    Update Language
                </button>
            </div>
        </form>
    </div>
</div>