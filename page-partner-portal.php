<?php
/**
 * Template Name: Partner Portal
 * Description: Standalone PRM Portal with Tailwind, no header/footer
 */

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> class="">

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
    <style>
        input {
            background-color: #ededed;
        }
    </style>
</head>

<body <?php body_class('bg-gray-100'); ?>>
    <div class="min-h-screen flex items-center justify-center">
        <div
            class="w-full max-w-4xl bg-white text-gray-800 shadow-xl rounded-2xl p-8 sm:p-10 mx-4 sm:mx-auto mt-10">
            <div class="text-center mb-6">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/tb-logo.png" alt="Logo"
                    class="h-32 mx-auto">
                <!-- <h1 class="text-3xl sm:text-4xl font-bold text-gray-800">Partner Portal</h1> -->
            </div>

            <?php get_template_part('template-parts/portal/registration', 'message'); ?>

            <!-- Tabs -->
            <div class="relative">
                <div class="flex gap-4 justify-center border-b border-gray-300 mb-6" id="prm-tabs" role="tablist" aria-label="Tab navigation">
                    <a href="#"
                        class="tab-btn text-sm no-underline sm:text-base text-gray-600 hover:text-[#086ad7] pb-2 border-b-2 border-transparent transition"
                        data-tab="login" role="tab" aria-selected="false">Login</a>
                    <a href="#"
                        class="tab-btn text-sm no-underline sm:text-base text-gray-600 hover:text-[#086ad7] pb-2 border-b-2 border-transparent transition"
                        data-tab="register" role="tab" aria-selected="false">Request Access</a>
                    <a href="#"
                        class="tab-btn text-sm no-underline sm:text-base text-gray-600 hover:text-[#086ad7] pb-2 border-b-2 border-transparent transition"
                        data-tab="support" role="tab" aria-selected="false">Support</a>
                </div>

                <!-- Spinner -->
                <div id="prm-spinner" class="absolute right-4 top-1/2 -translate-y-1/2 hidden">
                    <svg class="animate-spin h-5 w-5 text-[#086ad7]" xmlns="http://www.w3.org/2000/svg" fill="none"
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
            const DEFAULT_TAB = 'login';

            const tabButtons = document.querySelectorAll('.tab-btn');
            const tabContent = document.getElementById('prm-tab-content');
            const spinner = document.getElementById('prm-spinner');

            const activeClasses = ['border-[#086ad7]', 'text-[#086ad7]', 'font-semibold', 'pointer-events-none', 'cursor-default'];
            let isLoading = false;
            const cache = new Map();    

            function setActiveTabStyles(tab) {
                tabButtons.forEach(btn => {
                    const isActive = btn.dataset.tab === tab;

                    activeClasses.forEach(cls => btn.classList.toggle(cls, isActive));
                    btn.classList.toggle('border-transparent', !isActive);

                    btn.setAttribute('aria-selected', isActive);
                });
            }

            function loadTab(tab, skipHistoryUpdate = false) {
                if (isLoading) return;

                setActiveTabStyles(tab);

                // Serve from cache if available
                if (cache.has(tab)) {
                    tabContent.innerHTML = JSON.parse(cache.get(tab));
                    if (!skipHistoryUpdate) updateHistory(tab);
                    return;
                }
                
                spinner.classList.remove('hidden');
                isLoading = true;

                fetch(`/prm-wp/wp-json/prm/v1/tab?tab=${tab}`)
                    .then(res => res.text())
                    .then(html => {
                        cache.set(tab, html);
                        tabContent.innerHTML = JSON.parse(html);
                    })
                    .catch(() => {
                        tabContent.innerHTML = '<div class="text-red-600">Failed to load tab content.</div>';
                    })
                    .finally(() => {
                        spinner.classList.add('hidden');
                        isLoading = false;
                        if (!skipHistoryUpdate) updateHistory(tab);
                    });

                if (!skipHistoryUpdate) {
                    const currentTab = new URLSearchParams(location.search).get('tab');
                    if (currentTab !== tab) {
                        history.pushState({ tab }, '', `?tab=${tab}`);
                    }
                }
            }

            function updateHistory(tab) {
                const currentTab = new URLSearchParams(location.search).get('tab');
                if (currentTab !== tab) {
                    history.pushState({ tab }, '', `?tab=${tab}`);
                }
            }

            tabButtons.forEach(btn => {
                btn.addEventListener('click', () => loadTab(btn.dataset.tab));
            });

            window.addEventListener('popstate', () => {
                const tab = new URLSearchParams(location.search).get('tab') || DEFAULT_TAB;
                loadTab(tab, true);
            });

            const initialTab = new URLSearchParams(location.search).get('tab') || DEFAULT_TAB;
            loadTab(initialTab, true);
        });
    </script>

    <?php wp_footer(); ?>
</body>

</html>