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

    <form 
        action="<?php echo esc_url(admin_url('admin-post.php')); ?>" 
        method="post" 
        enctype="multipart/form-data"
        id="prm-registration-form"
        class="p-4 max-w-lg mx-auto mt-8 space-y-6"
        role="tabpanel"
        >
        <input type="hidden" name="action" value="process_registration">

        <h2 class="text-2xl font-bold text-center text-gray-800 mb-4">Register for Access</h2>

        <!-- Email -->
        <div>
            <label for="email" class="block text-sm font-semibold text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
            <input 
            type="email" 
            name="email" 
            id="email" 
            required 
            placeholder="your@email.com"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition"
            />
        </div>

        <!-- First & Last Name -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
            <label for="first_name" class="block text-sm font-semibold text-gray-700 mb-1">First Name <span class="text-red-500">*</span></label>
            <input 
                type="text" 
                name="first_name" 
                id="first_name" 
                required 
                placeholder="John"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition"
            />
            </div>
            <div>
            <label for="last_name" class="block text-sm font-semibold text-gray-700 mb-1">Last Name <span class="text-red-500">*</span></label>
            <input 
                type="text" 
                name="last_name" 
                id="last_name" 
                required 
                placeholder="Doe"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition"
            />
            </div>
        </div>

        <!-- Job Title -->
        <div>
            <label for="job_title" class="block text-sm font-semibold text-gray-700 mb-1">Job Title <span class="text-red-500">*</span></label>
            <input 
            type="text" 
            name="job_title" 
            id="job_title" 
            required 
            placeholder="e.g. Marketing Manager"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition"
            />
        </div>

        <!-- Company -->
        <div>
            <label for="company" class="block text-sm font-semibold text-gray-700 mb-1">Company <span class="text-red-500">*</span></label>
            <input 
            type="text" 
            name="company" 
            id="company" 
            required 
            placeholder="Company Name"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition"
            />
        </div>

        <!-- Country -->
        <div>
            <label for="country" class="block text-sm font-semibold text-gray-700 mb-1">Country <span class="text-red-500">*</span></label>
            <select 
            name="country" 
            id="country" 
            required
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition"
            >
            <option value="" selected disabled>Select Country</option>
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

        <!-- Company Address -->
        <div>
            <label for="street_address" class="block text-sm font-semibold text-gray-700 mb-1">Company Address <span class="text-red-500">*</span></label>
            <input 
            type="text" 
            name="street_address" 
            id="street_address" 
            required 
            placeholder="1234 Main St"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition"
            />
        </div>

        <!-- City & ZIP -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
            <label for="city" class="block text-sm font-semibold text-gray-700 mb-1">City <span class="text-red-500">*</span></label>
            <input 
                type="text" 
                name="city" 
                id="city" 
                required 
                placeholder="Jakarta"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition"
            />
            </div>
            <div>
            <label for="zip_code" class="block text-sm font-semibold text-gray-700 mb-1">ZIP/Postal Code <span class="text-red-500">*</span></label>
            <input 
                type="text" 
                name="zip_code" 
                id="zip_code" 
                required 
                placeholder="12345"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition"
            />
            </div>
        </div>

        <!-- Office Phone -->
        <div>
            <label for="phone" class="block text-sm font-semibold text-gray-700 mb-1">Office Phone <span class="text-red-500">*</span></label>
            <input 
            type="tel" 
            name="phone" 
            id="phone" 
            required 
            placeholder="+62 21 1234 5678"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition"
            />
        </div>

        <!-- Mobile Phone -->
        <div>
            <label for="mobile_phone" class="block text-sm font-semibold text-gray-700 mb-1">Mobile Phone <span class="text-red-500">*</span></label>
            <input 
            type="tel" 
            name="mobile_phone" 
            id="mobile_phone" 
            required 
            placeholder="+62 812 3456 7890"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition"
            />
        </div>

        <!-- Submit Button -->
        <button 
            type="submit" 
            name="prm_register_partner"
            class="w-full border-none bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg shadow-md transition focus:outline-none focus:ring-2 focus:ring-blue-400 cursor-pointer"
        >
            Request Access
        </button>
    </form>

    <?php
        $cache_key = 'terrabyte_newsletters_cache';
        $cached_data = get_transient($cache_key);

        // Debug mode - uncomment to preview in browser
        if (isset($_GET['debug_newsletter'])) {
            header('Content-Type: text/html; charset=utf-8');
            echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Newsletter Preview</title></head><body style="font-family: Arial, sans-serif; background: #f5f5f5; padding: 20px;">';
        }

        // if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) {
            $newsletters = $cached_data;
            
            $html_content = '
            <!-- Newsletter Section -->
            <table width="100%" border="0" cellspacing="0" cellpadding="0" style="max-width: 600px; margin: 0 auto; font-family: Arial, sans-serif; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
                <tr>
                    <td style="padding: 20px; background: #2a5db0; color: #ffffff;">
                        <h1 style="margin: 0; font-size: 20px; font-weight: normal;">While You Wait for Approval...</h1>
                    </td>
                </tr>';

            $count = 0;
            foreach ($newsletters as $news) {
                // Alternative for non-Jetpack sites (requires extra API call)
                $media_url = 'https://www.terrabytegroup.com/wp-json/wp/v2/media/' . $news->featured_media;
                $media_response = wp_remote_get($media_url);
                $image_url = $news->image_url;
                if (!empty($image_url) && $count < 3) {
                    $html_content .= '
                    <tr>
                        <td style="padding: 20px; border-bottom: 1px solid #f0f0f0;">
                            <img src="' . esc_url($image_url) . '" alt="' . esc_attr(strip_tags($news->title->rendered)) . '" style="width: 100%; max-height: 180px; object-fit: cover; border-radius: 4px; margin-bottom: 15px;">
                            <h3 style="margin: 0 0 10px; font-size: 18px;">
                                <a href="' . esc_url($news->link) . '" style="color: #2a5db0; text-decoration: none;">' . esc_html(strip_tags($news->title->rendered)) . '</a>
                            </h3>
                            <a href="' . esc_url($news->link) . '" style="color: #666666; text-decoration: none; font-size: 14px; display: inline-block; padding: 6px 12px; background: #f5f5f5; border-radius: 4px;">Read Now →</a>
                        </td>
                    </tr>';
                    $count++;
                }
            }

            if ($count === 0) {
                $html_content .= '
                <tr>
                    <td style="padding: 20px; color: #666666; font-size: 14px;">
                        No recent updates found. Check back later!
                    </td>
                </tr>';
            }

            $html_content .= '
                <tr>
                    <td style="padding: 15px 20px; text-align: center; background: #f9f9f9;">
                        <a href="https://www.terrabytegroup.com/newsletter" style="color: #2a5db0; text-decoration: none; font-weight: bold; font-size: 14px;">Browse All Articles →</a>
                    </td>
                </tr>
            </table>';

            $message .= $html_content;
        // }

        // Debug mode - uncomment to preview
        if (isset($_GET['debug_newsletter'])) {
            echo $html_content;
            echo '</body></html>';
            die();
        }
    ?>

<?php endif; ?>