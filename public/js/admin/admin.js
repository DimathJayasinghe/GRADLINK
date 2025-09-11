document.addEventListener('DOMContentLoaded', function(){
  // Tab persistence and activation
  const menuItems = document.querySelectorAll('.menu-item[data-section], .sidebar-menu a.menu-item');
  const sections = document.querySelectorAll('.admin-section');
  // Restore last tab
  let lastTab = localStorage.getItem('admin_active_tab');
  if (!lastTab) lastTab = 'overview';
  sections.forEach(s => s.classList.remove('active'));
  menuItems.forEach(m => m.classList.remove('active'));
  const section = document.getElementById(lastTab);
  if (section) section.classList.add('active');
  menuItems.forEach(m => {
    if ((m.dataset.section && m.dataset.section === lastTab) || (m.getAttribute('href') && m.getAttribute('href').endsWith('/' + lastTab))) {
      m.classList.add('active');
    }
  });
  // Sidebar tab switching for in-page sections
  document.querySelectorAll('.menu-item[data-section]').forEach(item => {
    item.addEventListener('click', function(e){
      const id = this.getAttribute('data-section');
      document.querySelectorAll('.admin-section').forEach(s => s.classList.remove('active'));
      const target = document.getElementById(id);
      if (target) target.classList.add('active');
      document.querySelectorAll('.menu-item').forEach(m => m.classList.remove('active'));
      this.classList.add('active');
      localStorage.setItem('admin_active_tab', id);
    });
  });
  // Overview tab click
  document.querySelectorAll('.sidebar-menu a.menu-item').forEach(item => {
    item.addEventListener('click', function(e){
      if (this.getAttribute('href') && this.getAttribute('href').endsWith('/admin/dashboard')) {
        sections.forEach(s => s.classList.remove('active'));
        menuItems.forEach(m => m.classList.remove('active'));
        const overview = document.getElementById('overview');
        if (overview) overview.classList.add('active');
        this.classList.add('active');
        localStorage.setItem('admin_active_tab', 'overview');
        e.preventDefault();
      }
    });
  });
  // Quick action buttons map to sections
  document.querySelectorAll('.quick-action-btn[data-section]').forEach(btn => {
    btn.addEventListener('click', function(){
      const id = this.getAttribute('data-section');
      const menu = document.querySelector('.menu-item[data-section="'+id+'"]');
      if (menu) menu.click();
    });
  });
});


