<?php
function prm_partner_approval_menu() {
    add_menu_page(
        'Pending Partners',
        'Pending Partners',
        'manage_options',
        'pending-partners',
        'prm_render_pending_partners',
        'dashicons-groups',
        30
    );
}
add_action('admin_menu', 'prm_partner_approval_menu');

function prm_render_pending_partners() {
    if (isset($_GET['approve_user'])) {
        $user_id = intval($_GET['approve_user']);
        $user = get_user_by('id', $user_id);
        if ($user && in_array('pending_partner', $user->roles)) {
            $user->set_role('partner');
            wp_mail($user->user_email, 'Approved', 'Your partner registration was approved!');
            echo '<div class="updated"><p>User approved.</p></div>';
        }
    }

    if (isset($_GET['reject_user'])) {
        $user_id = intval($_GET['reject_user']);
        wp_delete_user($user_id);
        echo '<div class="updated"><p>User rejected and deleted.</p></div>';
    }

    $args = [
        'role' => 'pending_partner',
        'orderby' => 'registered',
        'order' => 'DESC'
    ];
    $users = get_users($args);
    ?>
    <div class="wrap">
        <h2>Pending Partner Registrations</h2>
        <table class="widefat fixed">
            <thead>
                <tr>
                    <th>Email</th>
                    <th>Registered</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo esc_html($user->user_email); ?></td>
                        <td><?php echo esc_html($user->user_registered); ?></td>
                        <td>
                            <a href="?page=pending-partners&approve_user=<?php echo $user->ID; ?>" class="button">Approve</a>
                            <a href="?page=pending-partners&reject_user=<?php echo $user->ID; ?>" class="button" style="color:red">Reject</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
}
