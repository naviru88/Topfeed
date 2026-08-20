document.addEventListener('DOMContentLoaded', () => {
  const themeSelect = document.querySelector('select[name="theme"]');
  if (themeSelect) {
    themeSelect.addEventListener('change', () => {
      document.body.className = themeSelect.value;
    });
  }
});
