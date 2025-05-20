<div class="toast-container" id="toast-container"></div>

<!-- Toast -->
<script src="<?php echo get_template_directory_uri() . '/assets/js/toast.js' ?>"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {
  const sidebar = document.getElementById('sidebar-menu');
  const toggleSidebar = document.getElementById('toggleSidebar');
  const mobileBtn = document.getElementById('mobileMenuBtn');

  // Persistent sidebar collapse
  function updateSidebarState() {
    const expanded = localStorage.getItem('sidebar') !== 'collapsed';
    sidebar.classList.toggle('sidebar-collapsed', !expanded);
    sidebar.classList.toggle('sidebar-expanded', expanded);
  }

  toggleSidebar?.addEventListener('click', () => {
    const isCollapsed = sidebar.classList.contains('sidebar-collapsed');
    localStorage.setItem('sidebar', isCollapsed ? 'expanded' : 'collapsed');
    updateSidebarState();
  });

  // Responsive toggle
  mobileBtn?.addEventListener('click', () => {
    sidebar.classList.toggle('-translate-x-full');
  });

  // Submenu animation
  document.querySelectorAll('.submenu-toggle').forEach(btn => {
    btn.addEventListener('click', () => {
      const content = btn.nextElementSibling;
      const arrow = btn.querySelector('.submenu-arrow');
      content.classList.toggle('max-h-0');
      content.classList.toggle('max-h-40');
      arrow.classList.toggle('rotate-180');
    });
  });

  updateSidebarState();
});
</script>

<script>
    function toggleMenu() {
        const menu = document.querySelector('.vertical-menu');
        menu.classList.toggle('collapsed');
    }

    function toggleSubmenu(event) {
        event.preventDefault();
        const submenu = event.target.nextElementSibling;

        if (submenu) {
            submenu.style.display = submenu.style.display === 'block' ? 'none' : 'block';
        }
    }
</script>