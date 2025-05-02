<?php if (is_user_logged_in()) : ?>
    <p class="text-green-600">You are already logged in.</p>
<?php else : ?>
    <?php if (isset($_GET['registration']) && $_GET['registration'] === 'success') : ?>
        <div class="bg-green-100 text-green-700 p-3 mb-4 rounded">
            Registration successful! Please check your email to complete the process.
        </div>
    <?php elseif (isset($_GET['registration']) && $_GET['registration'] === 'failed') : ?>
        <div class="bg-red-100 text-red-700 p-3 mb-4 rounded">
            Registration failed. Please check your information and try again.
        </div>
    <?php endif; ?>

    <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post" enctype="multipart/form-data"  class="space-y-4 max-w-md mt-12 m-auto" role="tabpanel">
        <input type="hidden" name="action" value="process_registration">

        <div>
            <label for="email" class="block mb-1 font-medium">Email*</label>
            <input type="email" name="email" id="email" required class="w-full border px-3 py-2 rounded" />
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="first_name" class="block mb-1 font-medium">First Name*</label>
                <input type="text" name="first_name" id="first_name" required class="w-full border px-3 py-2 rounded" />
            </div>
            <div>
                <label for="last_name" class="block mb-1 font-medium">Last Name*</label>
                <input type="text" name="last_name" id="last_name" required class="w-full border px-3 py-2 rounded" />
            </div>
        </div>

        <div>
            <label for="job_title" class="block mb-1 font-medium">Job Title</label>
            <input type="text" name="job_title" id="job_title" class="w-full border px-3 py-2 rounded" />
        </div>

        <div>
            <label for="company" class="block mb-1 font-medium">Company</label>
            <input type="text" name="company" id="company" class="w-full border px-3 py-2 rounded" />
        </div>

        <div>
            <label for="profile_picture" class="block mb-1 font-medium">Profile Picture</label>
            <input type="file" name="profile_picture" id="profile_picture" accept=".gif,.png,.jpg,.jpeg,.jfif" class="w-full border px-3 py-2 rounded" />
            <p class="text-sm text-gray-500 mt-1">Image should be GIF, PNG, JPG, JPEG or JFIF and at least 256px in both dimensions.</p>
        </div>

        <div>
            <label for="country" class="block mb-1 font-medium">Country</label>
            <select name="country" id="country" class="w-full border px-3 py-2 rounded">
                <option value="">Select Country</option>
                <option value="ID">Indonesia</option>
                <option value="MY">Malaysia</option>
                <option value="PH">Philippines</option>
                <option value="SG">Singapore</option>
                <option value="TH">Thailand</option>
                <option value="VN">Vietnam</option>
                <option value="MM">Myanmar</option>
                <option value="BR">Brunei</option>
            </select>
        </div>

        <div>
            <label for="street_address" class="block mb-1 font-medium">Street Address</label>
            <input type="text" name="street_address" id="street_address" class="w-full border px-3 py-2 rounded" />
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="city" class="block mb-1 font-medium">City</label>
                <input type="text" name="city" id="city" class="w-full border px-3 py-2 rounded" />
            </div>
            <div>
                <label for="zip_code" class="block mb-1 font-medium">ZIP/Postal Code</label>
                <input type="text" name="zip_code" id="zip_code" class="w-full border px-3 py-2 rounded" />
            </div>
        </div>

        <div>
            <label for="phone" class="block mb-1 font-medium">Phone</label>
            <input type="tel" name="phone" id="phone" class="w-full border px-3 py-2 rounded" />
        </div>

        <div>
            <label for="mobile_phone" class="block mb-1 font-medium">Mobile Phone</label>
            <input type="tel" name="mobile_phone" id="mobile_phone" class="w-full border px-3 py-2 rounded" />
        </div>

        <button type="submit" name="prm_register_partner" class="border-none cursor-pointer bg-[#086ad7] text-white mt-8 px-4 py-2 rounded hover:bg-[#055bb8] w-full">
            Request Access
        </button>
    </form>
<?php endif; ?>