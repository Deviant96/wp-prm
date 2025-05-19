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
    <div id="create-document-type-form" class="hidden bg-gray-50  p-6 rounded-lg shadow">
        <h3 class="text-xl font-semibold mb-4 text-gray-800 ">Add New Language</h3>
        <form id="new-document-type-form" class="space-y-4">
            <div>
                <label for="document-type-name" class="block text-sm font-medium text-gray-700 ">Name*</label>
                <input type="text" id="document-type-name" name="name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500   ">
            </div>
            
            <div>
                <label for="document-type-slug" class="block text-sm font-medium text-gray-700 ">Slug</label>
                <input type="text" id="document-type-slug" name="slug"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500   ">
                <p class="mt-1 text-sm text-gray-500 ">The "slug" is the URL-friendly version of the name.</p>
            </div>
            
            <div>
                <label for="document-type-parent" class="block text-sm font-medium text-gray-700 ">Parent</label>
                <select id="document-type-parent" name="parent"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500   ">
                    <option value="0">None</option>
                    <?php
                    $terms = get_terms(array(
                        'taxonomy' => 'doc_type',
                        'hide_empty' => false,
                        'parent' => 0
                    ));
                    
                    foreach ($terms as $term) {
                        echo '<option value="' . $term->term_id . '">' . $term->name . '</option>';
                    }
                    ?>
                </select>
            </div>
            
            <div>
                <label for="document-type-description" class="block text-sm font-medium text-gray-700 ">Description</label>
                <textarea id="document-type-description" name="description" rows="3"
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500   "></textarea>
            </div>

            <!-- Add the field type selector -->
            <div>
                <label for="document-type-field-type" class="block text-sm font-medium text-gray-700 ">Field Type*</label>
                <select id="document-type-field-type" name="field_type" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500   ">
                    <option value="text">Text</option>
                    <option value="url">URL</option>
                    <option value="image">Image (.jpg, .png, .gif)</option>
                    <option value="pdf">PDF (.pdf)</option>
                    <option value="document">Document (.doc, .docx, .pdf)</option>
                </select>
                <p class="mt-1 text-sm text-gray-500 ">Determines what type of content can be uploaded for this document type.</p>
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
                    echo '<button class="edit-document-type text-blue-600 hover:text-blue-900   mr-3" data-term-id="' . $term->term_id . '">Edit</button>';
                    echo '<button class="delete-document-type text-red-600 hover:text-red-900  " data-term-id="' . $term->term_id . '">Delete</button>';
                    echo '</td>';
                    echo '</tr>';
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Edit Form (hidden by default) -->
    <div id="edit-document-type-form" class="hidden bg-gray-50  p-6 rounded-lg shadow">
        <h3 class="text-xl font-semibold mb-4 text-gray-800 ">Edit Document Type</h3>
        <form id="update-document-type-form" class="space-y-4">
            <div>
                <label for="edit-document-type-name" class="block text-sm font-medium text-gray-700 ">Name*</label>
                <input type="text" id="edit-document-type-name" name="name" required 
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500   ">
            </div>
            
            <div>
                <label for="edit-document-type-slug" class="block text-sm font-medium text-gray-700 ">Slug</label>
                <input type="text" id="edit-document-type-slug" name="slug"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500   ">
                <p class="mt-1 text-sm text-gray-500 ">The "slug" is the URL-friendly version of the name.</p>
            </div>
            
            <div>
                <label for="edit-document-type-parent" class="block text-sm font-medium text-gray-700 ">Parent</label>
                <select id="edit-document-type-parent" name="parent"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500   ">
                    <option value="0">None</option>
                    <?php
                    $terms = get_terms(array(
                        'taxonomy' => 'doc_type',
                        'hide_empty' => false,
                        'parent' => 0
                    ));
                    
                    foreach ($terms as $term) {
                        echo '<option value="' . $term->term_id . '">' . $term->name . '</option>';
                    }
                    ?>
                </select>
            </div>
            
            <div>
                <label for="edit-document-type-description" class="block text-sm font-medium text-gray-700 ">Description</label>
                <textarea id="edit-document-type-description" name="description" rows="3"
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500   "></textarea>
            </div>
            
            <input type="hidden" id="edit-document-type-id" name="term_id">
            <input type="hidden" name="action" value="update_document_type">
            
            <div class="flex justify-end gap-2">
                <button type="button" id="cancel-edit" class="px-4 py-2 text-gray-600 hover:text-gray-800  ">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition">
                    Update Document Type
                </button>
            </div>
        </form>
    </div>
</div>