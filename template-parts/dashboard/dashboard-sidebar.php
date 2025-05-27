<?php
$current_user = get_query_var('current_user');
$has_submenu = in_array('administrator', $current_user->roles) ? 'has-submenu' : '';
?>
<div id="sidebar-menu" class="vertical-menu transition-all duration-300 w-64 min-w-[64px] overflow-hidden fixed md:sticky top-0 left-0 z-40 h-full md:h-auto transform transition-transform duration-300 ease-in-out">
    <div class="menu-toggle" onclick="toggleMenu()">☰</div>

    <ul class="menu space-y-2">
        <li class="menu-item">
            <a href="<?php echo home_url('/?tab=dashboard'); ?>"
                class="flex items-center gap-3 px-4 py-2 rounded transition-all duration-200 hover:bg-blue-100  hover:pl-5 text-white hover:text-[#2376bb] ">
                <ion-icon name="home" class="text-xl"></ion-icon>
                <span class="menu-text">Dashboard</span>
            </a>
        </li>
        <li class="menu-item has-submenu">
            <a href="<?php echo home_url('/?tab=assets'); ?>"
                class="flex items-center gap-3 px-4 py-2 rounded transition-all duration-200 hover:bg-blue-100  hover:pl-5 text-white hover:text-[#2376bb] ">
                <ion-icon name="megaphone-outline" class="text-xl"></ion-icon>
                <span class="menu-text">Marketing Assets</span>
            </a>
            <?php if(in_array('administrator', $current_user->roles) || in_array('partner_manager', $current_user->roles)): ?>
                <ul class="submenu ml-8 mt-2 space-y-1 text-sm">
                    <li><a href="<?php echo home_url('/?tab=assets-manage'); ?>"
                            class="block px-3 py-1 rounded transition-all duration-200 hover:bg-blue-100  text-white hover:text-[#2376bb] ">All
                            Posts</a></li>
                    <li><a href="<?php echo home_url('/?tab=assets-create'); ?>"
                            class="block px-3 py-1 rounded transition-all duration-200 hover:bg-blue-100  text-white hover:text-[#2376bb] ">Add
                            New</a></li>
                    <li><a href="<?php echo home_url('/?tab=assets-language'); ?>"
                            class="block px-3 py-1 rounded transition-all duration-200 hover:bg-blue-100  text-white hover:text-[#2376bb] ">Language</a>
                    </li>
                    <li><a href="<?php echo home_url('/?tab=assets-doc-types'); ?>"
                            class="block px-3 py-1 rounded transition-all duration-200 hover:bg-blue-100  text-white hover:text-[#2376bb] ">Document Types</a>
                    </li>
                </ul>
            <?php endif; ?>
        </li>
        <li class="menu-item has-submenu">
            <a href="<?php echo home_url('/?tab=events'); ?>"
                class="flex items-center gap-3 px-4 py-2 rounded transition-all duration-200 hover:bg-blue-100  hover:pl-5 text-white hover:text-[#2376bb] ">
                <ion-icon name="calendar-outline" class="text-xl"></ion-icon>
                <span class="menu-text">Events</span>
            </a>
            <?php if(in_array('administrator', $current_user->roles) || in_array('partner_manager', $current_user->roles)): ?>
                <ul class="submenu ml-8 mt-2 space-y-1 text-sm">
                    <li><a href="<?php echo home_url('/?tab=events-manage'); ?>"
                            class="block px-3 py-1 rounded transition-all duration-200 hover:bg-blue-100  text-white hover:text-[#2376bb] ">All
                            Posts</a></li>
                    <li><a href="<?php echo home_url('/?tab=events-create'); ?>"
                            class="block px-3 py-1 rounded transition-all duration-200 hover:bg-blue-100  text-white hover:text-[#2376bb] ">Add
                            New</a></li>
                </ul>
            <?php endif; ?>
        </li>
        <?php if(in_array('administrator', $current_user->roles) || in_array('partner_manager', $current_user->roles)): ?>
            <li class="menu-item has-submenu">
                <a href="<?php echo home_url('/?tab=partners'); ?>"
                    class="flex items-center gap-3 px-4 py-2 rounded transition-all duration-200 hover:bg-blue-100  hover:pl-5 text-white hover:text-[#2376bb] ">
                    <ion-icon name="people-outline" class="text-xl"></ion-icon>
                    <span class="menu-text">Partners</span>
                </a>
                <ul class="submenu ml-8 mt-2 space-y-1 text-sm">
                    <li><a href="<?php echo home_url('/?tab=partners-all'); ?>"
                            class="block px-3 py-1 rounded transition-all duration-200 hover:bg-blue-100  text-white hover:text-[#2376bb] ">All
                            Partners</a></li>
                    <li><a href="<?php echo home_url('/?tab=partners-deleted'); ?>"
                            class="block px-3 py-1 rounded transition-all duration-200 hover:bg-blue-100  text-white hover:text-[#2376bb] ">Deleted</a></li>
                </ul>
            </li>
        <?php endif; ?>
    </ul>
</div>