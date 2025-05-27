<div class="prm-deleted-partners-container max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">All Deleted Partners</h1>

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
                        'meta_query' => [
                            [
                                'key'     => 'is_deleted',
                                'value'   => '1',
                                'compare' => '=',
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
                                    <button data-user-id="<?php echo $user->ID; ?>" class="restore-user text-green-600 hover:text-green-900">Restore</button>
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const userDetailModal = document.getElementById('userDetailModal');
        const userDetailContent = document.getElementById('userDetailContent');
        const closeModal = document.getElementById('closeModal');
        const spinner = document.getElementById('prm-user-loading');

        // Handle user detail modal
        const userRows = document.querySelectorAll('.user-row');
        userRows.forEach(row => {
            row.addEventListener('click', async function() {
                const userId = this.getAttribute('data-user-id');
                showModal();
                generateUserInModal(userId);
            });
        });

        const generateUserInModal = async (id) => {
            spinner.classList.remove('hidden');

            const user = await fetchUserDetail(id);

            // TODO Add more details 
            // TODO Give more stylings
            const content = `
                <p><strong>Email:</strong> ${user.email}</p>
                <p><strong>Company:</strong> ${user.company}</p>
                <p><strong>Registered:</strong> ${user.registered}</p>
            `;

            userDetailContent.innerHTML = content;

            spinner.classList.add('hidden');
        }

        const fetchUserDetail = async (userId) => {
            try {
                const response = await fetch(`${wpApiSettings.root}prm/v1/tbyte_prm_partner/${userId}`, {
                    method: "GET",
                    headers: { 
                        'Content-Type': 'application/json',
                        'X-WP-Nonce': wpApiSettings.nonce,
                    },
                })

                if (!response.ok) {
                    throw new Error("Network response was not ok")
                }

                const data = await response.json();

                return data;
            } catch(error) {
                showError('Error fetching assets. Please try again later.');
            }
        };

        const showModal = () => {
            userDetailContent.innerHTML = '';
            userDetailModal.classList.remove('hidden');
        }

        const hideModal = () => {
            userDetailContent.innerHTML = '';
            userDetailModal.classList.add('hidden');
        }

        document.getElementById('closeModal').addEventListener('click', function() {
            hideModal();
        });

        /**
         * Restore a soft deleted partner
         */
        document.querySelectorAll('.restore-user').forEach(btn => {
            btn.addEventListener('click', async function(e) {
                e.stopPropagation();
                const userId = this.getAttribute('data-user-id');
                if (!confirm('Are you sure you want to restore this partner?')) return;

                try {
                    const response = await fetch(`${wpApiSettings.root}prm/v1/tbyte_prm_partner/restore_soft_delete/${userId}`, {
                        method: "PUT",
                        headers: {
                            'Content-Type': 'application/json',
                            'X-WP-Nonce': wpApiSettings.nonce,
                        }
                    });

                    if (!response.ok) {
                        throw new Error('Failed to restore partner.');
                    }

                    console.error(response)

                } catch (error) {
                    showError('Error restoring partner. Please try again later.');
                } finally {
                    // Remove row from table
                    this.closest('tr').remove();
                }
            });
        });
    });
</script>