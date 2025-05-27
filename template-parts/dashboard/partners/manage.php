<div class="prm-partners-container max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">All Partners</h1>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Company</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php
                    // Set your dynamic country value here, e.g. from a variable or request
                    $country = isset($_GET['country']) ? sanitize_text_field($_GET['country']) : '';

                    $country = 'Indonesia';

                    $args = [
                        'role'    => 'partner',
                        'orderby' => 'registered',
                        'order'   => 'DESC',
                        'number'  => 5,
                        'meta_query' => [
                            'relation' => 'OR',
                            [
                                'key'     => 'is_deleted',
                                'value'   => '0',
                                'compare' => '=',
                            ],
                            [
                                'key'     => 'is_deleted',
                                'compare' => 'NOT EXISTS',
                            ],
                        ],
                    ];

                    // if (!empty($country)) {
                    //     $args['meta_query'][] = [
                    //         'key'   => 'country',
                    //         'value' => $country,
                    //     ];
                    // }

                    $users = get_users($args);
                    
                    if (empty($users)): ?>
                        <tr>
                            <td colspan="3" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">No partner found.</td>
                        </tr>
                    <?php else:
                        foreach ($users as $user): 
                            $company = esc_html(get_user_meta($user->ID, 'company', true));
                            if (empty($company)) {
                                $company = 'N/A';
                            }
                        ?>
                            <tr class="user-row cursor-pointer hover:bg-gray-50" data-user-id="<?php echo $user->ID; ?>">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900"><?php echo esc_html($user->user_email); ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-500"><?php echo $company ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button data-user-id="<?php echo $user->ID; ?>" class="delete-user text-red-600 hover:text-red-900">Delete</button>
                                </td>
                            </tr>
                        <?php endforeach;
                    endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Popup modal for user detail -->
<div id="userDetailModal" class="fixed inset-0 z-50 hidden bg-gray-800 bg-opacity-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-lg p-6 max-w-md w-full relative">
        <div id="prm-user-error" class="hidden bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-4"></div>
        <h2 class="text-xl font-bold mb-4">User Details</h2>
        <div id="prm-user-loading" class="mb-4">
            <div class="animate-pulse space-y-2">
                <div class="h-4 bg-gray-200 rounded w-3/4"></div>
                <div class="h-4 bg-gray-200 rounded w-1/2"></div>
                <div class="h-4 bg-gray-200 rounded w-2/3"></div>
            </div>
        </div>
        <div id="userDetailContent"></div>
        <button id="closeModal" class="mt-4 px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded">Close</button>
    </div>
</div>