<?php
/**
 * Template Name: Settings Dashboard
 */

// Ensure only administrators can access this page
if (!current_user_can('manage_options')) {
    wp_die(__('You do not have sufficient permissions to access this page.'));
}

$cache_key = 'terrabyte_newsletters_cache';
$cached_data = get_transient($cache_key);
$cache_duration = get_option('terrabyte_cache_duration', HOUR_IN_SECONDS);  
$last_updated = get_transient('terrabyte_last_updated');
$current_duration = get_option('terrabyte_cache_duration', 24 * HOUR_IN_SECONDS);
$hours = $current_duration / HOUR_IN_SECONDS;

get_header();
?>

<div class="wrap">
    <div class="settings-dashboard">
        <?php
        // Check if settings were saved
        if (isset($_POST['save_settings'])) {
            // Add your settings save logic here
            echo '<div class="updated"><p>Settings saved!</p></div>';
        }
        ?>

        <div class="max-w-4xl mx-auto p-6 space-y-6">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-indigo-700 mb-2">API Cache Control</h1>
                <p class="text-gray-500">Manage your newsletter content caching system</p>
                <p class="text-gray-500">The posts is shown for Latest Newsletter section in partner's mail when they first registered and in homepage of this portal.</p>
            </div>

            <!-- Stats Card -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden p-6 space-y-4 border border-gray-100">
                <div class="flex items-center space-x-3">
                    <div class="p-2 rounded-full bg-indigo-100 text-indigo-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <h2 class="text-xl font-semibold text-gray-800">Newsletter Cache Status</h2>
                </div>
                
                <div class="grid md:grid-cols-2 gap-4">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm font-medium text-gray-500 mb-1">Last Updated</p>
                        <p class="text-lg font-semibold text-gray-800">
                            <?php 
                                if ($last_updated) {
                                    $date = new DateTime();
                                    $date->setTimestamp($last_updated);
                                    $date->setTimezone(new DateTimeZone('Asia/Jakarta'));
                                    echo $date->format('Y-m-d H:i:s');
                                } else {
                                    echo 'Never';
                                }
                            ?>
                        </p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm font-medium text-gray-500 mb-1">Auto-Refresh</p>
                        <p class="text-lg font-semibold text-gray-800">
                            Every <?php echo round($cache_duration / 3600, 1); ?> hours
                        </p>
                    </div>
                </div>
            </div>

            <!-- Latest Articles Card -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
                <div class="p-6 border-b border-gray-100">
                    <div class="flex items-center space-x-3">
                        <div class="p-2 rounded-full bg-green-100 text-green-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                            </svg>
                        </div>
                        <h2 class="text-xl font-semibold text-gray-800">Latest Article Titles</h2>
                    </div>
                </div>
                <div class="divide-y divide-gray-100">
                    <?php 
                        if (!empty($cached_data)) {
                            foreach ($cached_data as $posts) {
                                echo '<div class="p-4 hover:bg-gray-50 transition-colors">';
                                echo '<h3 class="text-md font-medium text-gray-800">' . $posts->title->rendered . '</h3>';
                                echo '</div>';
                            }
                        } else {
                            echo '<div class="py-4 px-8">';
                            echo '<p class="text-gray-500">No cached articles found.</p>';
                            echo '</div>';
                        }
                    ?>
                </div>
            </div>

            <!-- Replace the entire form section with this: -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden p-6 space-y-4 border border-gray-100">
                <h3 class="text-lg font-semibold text-gray-800">Cache Actions</h3>
                <div class="flex flex-wrap gap-3">
                    <button id="refreshCache" 
                            class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg flex items-center space-x-2 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd" />
                        </svg>
                        <span>Refresh Now</span>
                    </button>
                    <button id="clearCache" 
                            class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg flex items-center space-x-2 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        <span>Clear Cache</span>
                    </button>
                </div>
            </div>

            <!-- Replace the settings form with this: -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden p-6 space-y-4 border border-gray-100">
                <h3 class="text-lg font-semibold text-gray-800">Auto-Fetch Settings</h3>
                <div class="space-y-4">
                    <div class="flex items-center space-x-3">
                        <label for="cacheDuration" class="text-sm font-medium text-gray-700 whitespace-nowrap">
                            Refresh every:
                        </label>
                        <input type="number" id="cacheDuration" 
                            value="<?php echo round($cache_duration / 3600, 1); ?>" 
                            min="0.1" step="0.1" 
                            class="w-20 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        <span class="text-sm font-medium text-gray-700">hours</span>
                    </div>
                    <button id="saveSettings" 
                            class="px-4 py-2 bg-gray-800 hover:bg-gray-900 text-white rounded-lg flex items-center space-x-2 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                        <span>Save Settings</span>
                    </button>
                </div>
            </div>

            <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Get elements
                const refreshBtn = document.getElementById('refreshCache');
                const clearBtn = document.getElementById('clearCache');
                const saveBtn = document.getElementById('saveSettings');
                const durationInput = document.getElementById('cacheDuration');
                
                // Handle refresh
                refreshBtn.addEventListener('click', async function() {
                    if (!confirm('Force fetch latest data?')) return;
                    
                    refreshBtn.disabled = true;
                    refreshBtn.innerHTML = '<span>Refreshing...</span>';
                    
                    try {
                        const response = await fetch(`${wpApiSettings.root}cache-control/v1/refresh`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-WP-Nonce': '<?php echo wp_create_nonce("wp_rest"); ?>'
                            }
                        });
                        
                        const data = await response.json();
                        
                        if (data.success) {
                            alert('Cache refreshed successfully!');
                            location.reload(); // Refresh the page to show new data
                        } else {
                            throw new Error(data.message || 'Failed to refresh cache');
                        }
                    } catch (error) {
                        alert(error.message);
                    } finally {
                        refreshBtn.disabled = false;
                        refreshBtn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd" /></svg><span>Refresh Now</span>';
                    }
                });
                
                // Handle clear
                clearBtn.addEventListener('click', async function() {
                    if (!confirm('Delete all cached data?')) return;
                    
                    clearBtn.disabled = true;
                    clearBtn.innerHTML = '<span>Clearing...</span>';
                    
                    try {
                        const response = await fetch(`${wpApiSettings.root}cache-control/v1/clear`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-WP-Nonce': '<?php echo wp_create_nonce("wp_rest"); ?>'
                            }
                        });
                        
                        const data = await response.json();
                        
                        if (data.success) {
                            alert('Cache cleared successfully!');
                            location.reload();
                        } else {
                            throw new Error(data.message || 'Failed to clear cache');
                        }
                    } catch (error) {
                        alert(error.message);
                    } finally {
                        clearBtn.disabled = false;
                        clearBtn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" /></svg><span>Clear Cache</span>';
                    }
                });
                
                // Handle settings save
                saveBtn.addEventListener('click', async function() {
                    const duration = durationInput.value;
                    
                    if (!duration || isNaN(duration)) {
                        alert('Please enter a valid duration');
                        return;
                    }
                    
                    saveBtn.disabled = true;
                    saveBtn.innerHTML = '<span>Saving...</span>';
                    
                    try {
                        const response = await fetch(`${wpApiSettings.root}cache-control/v1/settings`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-WP-Nonce': '<?php echo wp_create_nonce("wp_rest"); ?>'
                            },
                            body: JSON.stringify({ duration: duration })
                        });
                        
                        const data = await response.json();
                        
                        if (data.success) {
                            alert('Settings saved successfully!');
                        } else {
                            throw new Error(data.message || 'Failed to save settings');
                        }
                    } catch (error) {
                        alert(error.message);
                    } finally {
                        saveBtn.disabled = false;
                        saveBtn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg><span>Save Settings</span>';
                    }
                });
            });
            </script>

            <!-- Article List Preview -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
                <div class="p-6 border-b border-gray-100">
                    <div class="flex items-center space-x-3">
                        <div class="p-2 rounded-full bg-blue-100 text-blue-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h2 class="text-xl font-semibold text-gray-800">Content Preview</h2>
                    </div>
                </div>
                <div class="divide-y divide-gray-100">
                    <table width="100%" border="0" cellspacing="0" cellpadding="0" style="max-width: 600px; margin: 0 auto; font-family: Arial, sans-serif; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
                        <tr>
                            <td style="padding: 20px; background: #2a5db0; color: #ffffff;">
                                <h1 style="margin: 0; font-size: 20px; font-weight: normal;">While You Wait for Approval...</h1>
                            </td>
                        </tr>';
                    <?php 
                    if (!empty($cached_data)) {
                        foreach ($cached_data as $post) { ?>
                        <tr>
                            <td style="padding: 20px; border-bottom: 1px solid #f0f0f0;">
                                <img src="' . esc_url($image_url) . '" alt="' . esc_attr(strip_tags($news->title->rendered)) . '" style="width: 100%; max-height: 180px; object-fit: cover; border-radius: 4px; margin-bottom: 15px;">
                                <h3 style="margin: 0 0 10px; font-size: 18px;">
                                    <a href="' . esc_url($news->link) . '" style="color: #2a5db0; text-decoration: none;"><?php esc_html(strip_tags($news->title->rendered)); ?></a>
                                </h3>
                                <a href=<?php echo esc_url($news->link); ?>" style="color: #666666; text-decoration: none; font-size: 14px; display: inline-block; padding: 6px 12px; background: #f5f5f5; border-radius: 4px;">Read Now →</a>
                            </td>
                        </tr>
                        <?php }
                    } else {
                        echo '<div class="py-4 px-8">';
                        echo '<p class="text-gray-500">No articles available for preview.</p>';
                        echo '</div>';
                    }
                    ?>
                            <tr>
                            <td style="padding: 15px 20px; text-align: center; background: #f9f9f9;">
                                <a href="https://www.terrabytegroup.com/newsletter" style="color: #2a5db0; text-decoration: none; font-weight: bold; font-size: 14px;">Browse All Articles →</a>
                            </td>
                        </tr>
                    </table>';
                </div>
            </div>
        </div>
    </div>
</div>

<?php get_footer(); ?>