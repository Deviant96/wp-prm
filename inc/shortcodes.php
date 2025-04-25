<?php
function prm_partner_registration_form() {
    ob_start(); ?>
    
    <form method="post">
        <input type="text" name="prm_fullname" placeholder="Full Name" required />
        <input type="email" name="prm_email" placeholder="Email" required />
        <input type="password" name="prm_password" placeholder="Password" required />
        <button type="submit" name="prm_register_partner">Register as Partner</button>
    </form>

    <?php
    return ob_get_clean();
}
add_shortcode('partner_registration_form', 'prm_partner_registration_form');