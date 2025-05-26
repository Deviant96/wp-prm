<?php
/**
 * Template Name: Partner Approval
 */

// Check permissions
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
                                    <a href="?approve_user=<?php echo $user->ID; ?>" class="text-green-600 hover:text-green-900 mr-4">Approve</a>
                                    <a href="?reject_user=<?php echo $user->ID; ?>" class="text-red-600 hover:text-red-900">Reject</a>
                                </td>
                            </tr>
                        <?php endforeach;
                    endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php get_footer(); ?>