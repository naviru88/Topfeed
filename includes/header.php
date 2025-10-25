<header class="main-header">
  <div class="container">
    <nav class="navbar">
      <div class="nav-brand">
        <a href="<?= getBasePath() ?>pages/index.php">
          <svg width="32" height="32" viewBox="0 0 32 32" fill="none">
            <rect width="32" height="32" rx="8" fill="url(#gradient)"/>
            <path d="M8 12h16M8 16h16M8 20h10" stroke="white" stroke-width="2" stroke-linecap="round"/>
            <defs>
              <linearGradient id="gradient" x1="0" y1="0" x2="32" y2="32">
                <stop offset="0%" stop-color="#667eea"/>
                <stop offset="100%" stop-color="#764ba2"/>
              </linearGradient>
            </defs>
          </svg>
          <span>Topfeed</span>
        </a>
      </div>
      
      <button class="mobile-menu-toggle" onclick="toggleMobileMenu()">
        <span></span>
        <span></span>
        <span></span>
      </button>
      
      <div class="nav-links" id="navLinks">
        <a href="<?= getBasePath() ?>pages/index.php" class="nav-link">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
          </svg>
          Home
        </a>
        <a href="<?= getBasePath() ?>pages/discover.php" class="nav-link">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="11" cy="11" r="8"/>
            <path d="m21 21-4.35-4.35"/>
          </svg>
          Discover
        </a>
        <?php if (isset($_SESSION['user_id'])): ?>
          <a href="<?= getBasePath() ?>pages/personal.php" class="nav-link">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
              <circle cx="12" cy="7" r="4"/>
            </svg>
            My Page
          </a>
          <a href="<?= getBasePath() ?>pages/settings.php" class="nav-link">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="12" cy="12" r="3"/>
              <path d="M12 1v6m0 6v6M5.64 5.64l4.24 4.24m4.24 4.24l4.24 4.24M1 12h6m6 0h6M5.64 18.36l4.24-4.24m4.24-4.24l4.24-4.24"/>
            </svg>
            Settings
          </a>
          <a href="<?= getBasePath() ?>blog/create.php" class="btn-create">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <line x1="12" y1="5" x2="12" y2="19"/>
              <line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Create Blog
          </a>
          <a href="<?= getBasePath() ?>auth/logout.php" class="nav-link">Logout</a>
        <?php else: ?>
          <a href="<?= getBasePath() ?>auth/login.php" class="btn-login">Login</a>
          <a href="<?= getBasePath() ?>auth/register.php" class="btn-signup">Sign Up</a>
        <?php endif; ?>
      </div>
    </nav>
  </div>
</header>

<script>
function toggleMobileMenu() {
  const navLinks = document.getElementById('navLinks');
  navLinks.classList.toggle('active');
}

// Apply theme on page load
document.addEventListener('DOMContentLoaded', function() {
  const themeClass = document.body.className;
  if (themeClass === 'auto') {
    // Auto mode: use system preference
    if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
      document.body.classList.add('dark');
    }
    document.body.classList.remove('auto');
  }
});
</script>
