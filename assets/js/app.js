/**
 * Custom Type Master Pro - Global App Utilities
 */

document.addEventListener('DOMContentLoaded', () => {
  initTheme();
  initSidebar();
  initKeyboardShortcuts();
});

/**
 * Initializes and handles theme toggling
 */
function initTheme() {
  let savedTheme = localStorage.getItem('typemaster_theme');
  if (savedTheme === 'white-orange') savedTheme = 'light';
  const activeTheme = savedTheme || document.documentElement.getAttribute('data-theme') || 'light';
  document.documentElement.setAttribute('data-theme', activeTheme);
  localStorage.setItem('typemaster_theme', activeTheme);
  updateThemeUI(activeTheme);

  const themeSelectors = document.querySelectorAll('[data-set-theme]');
  themeSelectors.forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      const newTheme = btn.dataset.setTheme;
      setTheme(newTheme);
    });
  });
}

function getThemeDisplayName(theme) {
  if (theme === 'light' || theme === 'white-orange') return 'Soft UI White';
  if (theme === 'orange') return 'Dark Orange';
  if (theme === 'dark') return 'Dark Obsidian';
  if (theme === 'cyberpunk') return 'Cyberpunk Neon';
  if (theme === 'nord') return 'Nord Arctic';
  return theme.charAt(0).toUpperCase() + theme.slice(1);
}

function updateThemeUI(theme) {
  const themeText = getThemeDisplayName(theme);
  const themeBtn = document.querySelector('.dropdown button[title="Switch Theme"]');
  if (themeBtn) {
    const label = themeBtn.querySelector('span');
    const icon = themeBtn.querySelector('i');
    if (label) label.textContent = themeText;
    if (icon) {
      if (theme === 'light' || theme === 'white-orange') {
        icon.className = 'bi bi-sun-fill';
        icon.style.color = '#ff5532';
      } else if (theme === 'orange') {
        icon.className = 'bi bi-fire';
        icon.style.color = '#ff6a00';
      } else if (theme === 'dark') {
        icon.className = 'bi bi-moon-stars-fill text-primary';
        icon.style.color = '';
      } else if (theme === 'cyberpunk') {
        icon.className = 'bi bi-lightning-charge-fill text-danger';
        icon.style.color = '';
      } else if (theme === 'nord') {
        icon.className = 'bi bi-snow text-info';
        icon.style.color = '';
      }
    }
  }
}

function setTheme(theme) {
  document.documentElement.setAttribute('data-theme', theme);
  localStorage.setItem('typemaster_theme', theme);
  updateThemeUI(theme);

  const themeText = getThemeDisplayName(theme);
  if (typeof showToast === 'function') {
    showToast('info', `Switched theme to ${themeText}`);
  }

  // If user is logged in, optionally persist to server
  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
  if (csrfToken) {
    fetch('api/save-settings.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken
      },
      body: JSON.stringify({ theme: theme, partial: true })
    }).catch(() => {});
  }
}

/**
 * Responsive sidebar toggling
 */
function initSidebar() {
  const toggleBtn = document.getElementById('sidebar-toggle-btn');
  const sidebar = document.querySelector('.app-sidebar');
  const container = document.querySelector('.app-container');

  if (toggleBtn && sidebar) {
    toggleBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      if (window.innerWidth < 992) {
        sidebar.classList.toggle('show');
      } else {
        sidebar.classList.toggle('collapsed');
        if (container) {
          container.classList.toggle('sidebar-collapsed');
        }
      }
    });

    // Close when clicking outside on small screens
    document.addEventListener('click', (e) => {
      if (window.innerWidth < 992 && sidebar.classList.contains('show') && !sidebar.contains(e.target) && !toggleBtn.contains(e.target)) {
        sidebar.classList.remove('show');
      }
    });
  }
}

/**
 * Global Keyboard shortcuts
 */
function initKeyboardShortcuts() {
  document.addEventListener('keydown', (e) => {
    // Quick restart typing test when pressing Tab then Enter
    if (e.key === 'Tab') {
      const restartBtn = document.getElementById('btn-restart-test');
      if (restartBtn && document.activeElement !== document.getElementById('hidden-typing-input')) {
        restartBtn.focus();
      }
    }
  });
}

/**
 * Dynamic Toast Notification System
 *
 * @param {string} type 'success' | 'danger' | 'warning' | 'info'
 * @param {string} message Text to display
 * @param {number} duration Timeout in ms
 */
function showToast(type, message, duration = 4000) {
  let container = document.querySelector('.toast-container-custom');
  if (!container) {
    container = document.createElement('div');
    container.className = 'toast-container-custom';
    document.body.appendChild(container);
  }

  const icons = {
    success: 'bi-check-circle-fill text-success',
    danger: 'bi-exclamation-octagon-fill text-danger',
    warning: 'bi-exclamation-triangle-fill text-warning',
    info: 'bi-info-circle-fill text-info'
  };

  const toast = document.createElement('div');
  toast.className = 'glass-card p-3 shadow-lg d-flex align-items-center gap-3 animate__animated animate__fadeInUp';
  toast.style.minWidth = '280px';
  toast.style.maxWidth = '400px';
  toast.style.zIndex = '9999';

  toast.innerHTML = `
    <i class="bi ${icons[type] || icons.info} fs-4"></i>
    <div class="flex-grow-1 text-light small fw-medium">${message}</div>
    <button type="button" class="btn-close btn-close-white ms-auto" aria-label="Close"></button>
  `;

  container.appendChild(toast);

  const closeBtn = toast.querySelector('.btn-close');
  closeBtn.addEventListener('click', () => toast.remove());

  setTimeout(() => {
    if (toast.parentNode) {
      toast.style.opacity = '0';
      toast.style.transform = 'translateY(10px)';
      toast.style.transition = 'all 0.3s ease';
      setTimeout(() => toast.remove(), 300);
    }
  }, duration);
}

/**
 * Desktop PWA & Install on PC Handler
 */
let deferredPwaPrompt = null;
window.addEventListener('beforeinstallprompt', (e) => {
  e.preventDefault();
  deferredPwaPrompt = e;
});

document.addEventListener('DOMContentLoaded', () => {
  const installBtns = document.querySelectorAll('#btn-install-pc, #btn-install-header');
  installBtns.forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      if (deferredPwaPrompt) {
        deferredPwaPrompt.prompt();
        deferredPwaPrompt.userChoice.then((choiceResult) => {
          if (choiceResult.outcome === 'accepted') {
            showToast('success', 'TypeMaster Pro is now installed on your PC!');
          }
          deferredPwaPrompt = null;
        });
      } else {
        showToast('info', 'To install on PC: Look for the Install icon in your browser address bar (top right) or press Ctrl+D to bookmark!');
      }
    });
  });
});

