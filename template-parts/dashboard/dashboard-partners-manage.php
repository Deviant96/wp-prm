<?php
/**
 * Template Name: Partner Approval
 */

if (!current_user_can('manage_options')) {
    wp_die('You do not have permission to view this page.');
}

get_header(); ?>

<div class="prm-partner-approval-container max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Pending Partner Registrations</h1>
    
    <?php
    // Handle approval/rejection actions
    if (isset($_GET['approve_user'])) {
        $user_id = intval($_GET['approve_user']);
        $user = get_user_by('id', $user_id);
        if ($user && in_array('pending_partner', $user->roles)) {
            $user->set_role('partner');
            wp_mail($user->user_email, 'Approved', 'Your partner registration was approved!');
            echo '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">User approved successfully.</div>';
        }
    }

    if (isset($_GET['reject_user'])) {
        require_once(ABSPATH . 'wp-admin/includes/user.php');
        $user_id = intval($_GET['reject_user']);
        wp_delete_user($user_id);
        echo '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">User rejected and deleted successfully.</div>';
    }
    ?>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Registered</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php
                    $args = [
                        'role' => 'pending_partner',
                        'orderby' => 'registered',
                        'order' => 'DESC'
                    ];
                    $users = get_users($args);
                    
                    if (empty($users)): ?>
                        <tr>
                            <td colspan="3" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">No pending partner registrations found.</td>
                        </tr>
                    <?php else:
                        foreach ($users as $user): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900"><?php echo esc_html($user->user_email); ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-500"><?php echo date('M j, Y @ g:i a', strtotime(esc_html($user->user_registered))); ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="?tab=partners&approve_user=<?php echo $user->ID; ?>" class="text-green-600 hover:text-green-900 mr-4">Approve</a>
                                    <a href="?tab=partners&reject_user=<?php echo $user->ID; ?>" class="text-red-600 hover:text-red-900">Reject</a>
                                </td>
                            </tr>
                        <?php endforeach;
                    endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="prm-partners-container max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Latest 5 Partners</h1>

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
         * Soft delete a partner
         */
        document.querySelectorAll('.delete-user').forEach(btn => {
            btn.addEventListener('click', async function(e) {
                e.stopPropagation();
                const userId = this.getAttribute('data-user-id');
                if (!confirm('Are you sure you want to delete this partner?')) return;

                try {
                    const response = await fetch(`${wpApiSettings.root}prm/v1/tbyte_prm_partner/soft_delete/${userId}`, {
                        method: "PUT",
                        headers: {
                            'Content-Type': 'application/json',
                            'X-WP-Nonce': wpApiSettings.nonce,
                        }
                    });

                    if (!response.ok) {
                        throw new Error('Failed to delete partner.');
                    }

                    console.error(response)

                } catch (error) {
                    showError('Error deleting partner. Please try again later.');
                } finally {
                    // Remove row from table
                    this.closest('tr').remove();
                }
            });
        });
    });
</script>

<?php get_footer(); ?>