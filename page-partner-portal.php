<?php
/**
 * Template Name: Partner Portal
 * Description: Standalone PRM Portal with Tailwind, no header/footer
 */

?><!DOCTYPE html>
<html <?php language_attributes(); ?> class="dark">

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>

<body <?php body_class('bg-gray-100 dark:bg-gray-900'); ?>>
    <div class="min-h-screen flex items-center justify-center">
        <div
            class="w-full max-w-4xl bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 shadow-xl rounded-2xl p-8 sm:p-10 mx-4 sm:mx-auto mt-10">
            <div class="text-center mb-6">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/logo.png" alt="Logo"
                    class="h-14 mx-auto mb-4">
                <h1 class="text-3xl sm:text-4xl font-bold text-gray-800">Partner Portal</h1>
            </div>

            <!-- Tabs -->
            <div class="relative">
                <div class="flex justify-end mb-4">
                    <button id="theme-toggle"
                        class="text-sm text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition">
                        Toggle <span id="theme-label">Dark</span> Mode
                    </button>
                </div>

                <div class="flex gap-4 justify-center border-b border-gray-300 mb-6" id="prm-tabs">
                    <a href="#"
                        class="tab-btn text-sm sm:text-base text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 pb-2 border-b-2 border-transparent transition"
                        data-tab="login">Login</a>
                    <a href="#"
                        class="tab-btn text-sm sm:text-base text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 pb-2 border-b-2 border-transparent transition"
                        data-tab="register">Register</a>
                    <a href="#"
                        class="tab-btn text-sm sm:text-base text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 pb-2 border-b-2 border-transparent transition"
                        data-tab="support">Support</a>
                </div>

                <!-- Spinner -->
                <div id="prm-spinner" class="absolute right-4 top-1/2 -translate-y-1/2 hidden">
                    <svg class="animate-spin h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                        </circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                    </svg>
                </div>
            </div>

            <!-- Dynamic tab content -->
            <div id="prm-tab-content">
                <?php
                $tab = $_GET['tab'] ?? 'login';
                get_template_part('template-parts/portal/tab', $tab);
                ?>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const tabButtons = document.querySelectorAll('.tab-btn');
            const tabContent = document.getElementById('prm-tab-content');
            const spinner = document.getElementById('prm-spinner');

            function loadTab(tab) {
                // Update tab button styles
                tabButtons.forEach(btn => {
                    const isActive = btn.dataset.tab === tab;
                    btn.classList.toggle('border-blue-600', isActive);
                    btn.classList.toggle('text-blue-600', isActive);
                    btn.classList.toggle('font-semibold', isActive);
                    btn.classList.toggle('pointer-events-none', isActive);
                    btn.classList.toggle('cursor-default', isActive);
                });

                spinner.classList.remove('hidden');

                fetch(`/prm-wp/wp-json/prm/v1/tab?tab=${tab}`)
                    .then(res => res.text())
                    .then(html => {
                        spinner.classList.add('hidden');
                        tabContent.innerHTML = JSON.parse(html);
                    })
                    .catch(() => {
                        spinner.classList.add('hidden');
                        tabContent.innerHTML = '<div class="text-red-600">Failed to load tab content.</div>';
                    });

                history.pushState({ tab }, '', `?tab=${tab}`);
            }

            tabButtons.forEach(btn => {
                btn.addEventListener('click', () => loadTab(btn.dataset.tab));
            });

            window.addEventListener('popstate', () => {
                const tab = new URLSearchParams(location.search).get('tab') || 'login';
                loadTab(tab);
            });

            const initialTab = new URLSearchParams(location.search).get('tab') || 'login';
            loadTab(initialTab);
        });
    </script>

    <script>
        (function () {
            const html = document.documentElement;
            const themeToggle = document.getElementById('theme-toggle');
            const themeLabel = document.getElementById('theme-label');

            // Init theme from storage or system
            const storedTheme = localStorage.getItem('theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            const initialDark = storedTheme === 'dark' || (!storedTheme && prefersDark);

            if (initialDark) html.classList.add('dark');

            function updateLabel() {
                themeLabel.textContent = html.classList.contains('dark') ? 'Light' : 'Dark';
            }

            themeToggle.addEventListener('click', () => {
                html.classList.toggle('dark');
                const newTheme = html.classList.contains('dark') ? 'dark' : 'light';
                localStorage.setItem('theme', newTheme);
                updateLabel();
            });

            updateLabel();
        })();
    </script>


    <?php wp_footer(); ?>
</body>

</html>