// Sidebar Toggle Functionality
document.addEventListener('DOMContentLoaded', function() {
  const toggleBtn = document.getElementById('toggleSidebar');
  const sidebar = document.getElementById('sidebar');
  const mainContent = document.querySelector('.main-content');
  const adminFooter = document.querySelector('.admin-footer');

  if (toggleBtn && sidebar) {
      // Desktop toggle
      if (window.innerWidth > 768) {
          toggleBtn.addEventListener('click', function(e) {
              e.stopPropagation();
              sidebar.classList.toggle('collapsed');
              
              // Save state to localStorage
              const isCollapsed = sidebar.classList.contains('collapsed');
              localStorage.setItem('sidebarCollapsed', isCollapsed);
          });

          // Restore sidebar state on page load
          const savedState = localStorage.getItem('sidebarCollapsed');
          if (savedState === 'true') {
              sidebar.classList.add('collapsed');
          }
      } else {
          // Mobile toggle
          toggleBtn.addEventListener('click', function(e) {
              e.stopPropagation();
              sidebar.classList.toggle('active');
              document.body.classList.toggle('sidebar-open');
          });

          // Close sidebar when clicking outside
          document.addEventListener('click', function(e) {
              if (!sidebar.contains(e.target) && !toggleBtn.contains(e.target)) {
                  sidebar.classList.remove('active');
                  document.body.classList.remove('sidebar-open');
              }
          });

          // Close sidebar when clicking on a link
          const sidebarLinks = sidebar.querySelectorAll('a');
          sidebarLinks.forEach(link => {
              link.addEventListener('click', function() {
                  sidebar.classList.remove('active');
                  document.body.classList.remove('sidebar-open');
              });
          });
      }
  }
});

// Profile Dropdown Toggle (for mobile)
const profileBtn = document.querySelector('.profile-btn');
const profileMenu = document.querySelector('.profile-menu');

if (profileBtn && profileMenu && window.innerWidth <= 768) {
  profileBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      profileMenu.style.display = profileMenu.style.display === 'block' ? 'none' : 'block';
  });

  document.addEventListener('click', () => {
      profileMenu.style.display = 'none';
  });
}

// Search Functionality
const searchInput = document.querySelector('.header-search input');
if (searchInput) {
  searchInput.addEventListener('input', (e) => {
      const query = e.target.value.toLowerCase();
      // Add your search logic here
      console.log('Searching for:', query);
  });
}

// Notification Click Handler
const notificationBtn = document.querySelector('.notification-btn');
if (notificationBtn) {
  notificationBtn.addEventListener('click', () => {
      // Add notification panel logic here
      console.log('Show notifications');
  });
}

// Smooth Scroll for Anchor Links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
  anchor.addEventListener('click', function (e) {
      e.preventDefault();
      const target = document.querySelector(this.getAttribute('href'));
      if (target) {
          target.scrollIntoView({
              behavior: 'smooth',
              block: 'start'
          });
      }
  });
});

// Auto-hide alerts/notifications after 5 seconds
const alerts = document.querySelectorAll('.alert');
alerts.forEach(alert => {
  setTimeout(() => {
      alert.style.opacity = '0';
      setTimeout(() => alert.remove(), 300);
  }, 5000);
});

// Confirm delete actions
const deleteButtons = document.querySelectorAll('[data-action="delete"]');
deleteButtons.forEach(btn => {
  btn.addEventListener('click', (e) => {
      if (!confirm('Are you sure you want to delete this item?')) {
          e.preventDefault();
      }
  });
});

// Form validation helper
const validateForm = (form) => {
  const inputs = form.querySelectorAll('input[required], textarea[required]');
  let isValid = true;

  inputs.forEach(input => {
      if (!input.value.trim()) {
          input.classList.add('is-invalid');
          isValid = false;
      } else {
          input.classList.remove('is-invalid');
      }
  });

  return isValid;
};

// Add form validation to all forms
const forms = document.querySelectorAll('form');
forms.forEach(form => {
  form.addEventListener('submit', (e) => {
      if (!validateForm(form)) {
          e.preventDefault();
      }
  });
});

// Real-time input validation
const requiredInputs = document.querySelectorAll('input[required], textarea[required]');
requiredInputs.forEach(input => {
  input.addEventListener('blur', () => {
      if (!input.value.trim()) {
          input.classList.add('is-invalid');
      } else {
          input.classList.remove('is-invalid');
      }
  });
});

// Image preview for file uploads
const imageInputs = document.querySelectorAll('input[type="file"][accept*="image"]');
imageInputs.forEach(input => {
  input.addEventListener('change', (e) => {
      const file = e.target.files[0];
      if (file) {
          const reader = new FileReader();
          reader.onload = (e) => {
              const preview = document.querySelector(`#preview-${input.id}`);
              if (preview) {
                  preview.src = e.target.result;
                  preview.style.display = 'block';
              }
          };
          reader.readAsDataURL(file);
      }
  });
});

// Toast notification system
const showToast = (message, type = 'info') => {
  const toast = document.createElement('div');
  toast.className = `toast toast-${type}`;
  toast.textContent = message;
  toast.style.cssText = `
      position: fixed;
      bottom: 24px;
      right: 24px;
      background: ${type === 'success' ? '#10B981' : type === 'error' ? '#EF4444' : '#3B82F6'};
      color: white;
      padding: 16px 24px;
      border-radius: 8px;
      box-shadow: 0 10px 25px rgba(0,0,0,0.2);
      z-index: 9999;
      animation: slideIn 0.3s ease;
  `;
  
  document.body.appendChild(toast);
  
  setTimeout(() => {
      toast.style.animation = 'slideOut 0.3s ease';
      setTimeout(() => toast.remove(), 300);
  }, 3000);
};

// Add CSS for toast animations
const style = document.createElement('style');
style.textContent = `
  @keyframes slideIn {
      from {
          transform: translateX(400px);
          opacity: 0;
      }
      to {
          transform: translateX(0);
          opacity: 1;
      }
  }
  
  @keyframes slideOut {
      from {
          transform: translateX(0);
          opacity: 1;
      }
      to {
          transform: translateX(400px);
          opacity: 0;
      }
  }
`;
document.head.appendChild(style);

// Loading indicator
const showLoading = (element) => {
  const loader = document.createElement('div');
  loader.className = 'loader';
  loader.innerHTML = '<div class="spinner"></div>';
  element.appendChild(loader);
};

const hideLoading = (element) => {
  const loader = element.querySelector('.loader');
  if (loader) loader.remove();
};

// Debounce function for search inputs
const debounce = (func, wait) => {
  let timeout;
  return function executedFunction(...args) {
      const later = () => {
          clearTimeout(timeout);
          func(...args);
      };
      clearTimeout(timeout);
      timeout = setTimeout(later, wait);
  };
};

// Export functions for use in other scripts
window.adminUtils = {
  showToast,
  showLoading,
  hideLoading,
  validateForm,
  debounce
};

console.log('Admin dashboard initialized');