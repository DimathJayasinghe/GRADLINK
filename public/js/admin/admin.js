document.addEventListener('DOMContentLoaded', function(){
  // Sidebar tab switching for in-page sections
  document.querySelectorAll('.menu-item[data-section]').forEach(item => {
    item.addEventListener('click', function(e){
      const id = this.getAttribute('data-section');
      document.querySelectorAll('.admin-section').forEach(s => s.classList.remove('active'));
      const target = document.getElementById(id);
      if (target) target.classList.add('active');
      document.querySelectorAll('.menu-item').forEach(m => m.classList.remove('active'));
      this.classList.add('active');
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


