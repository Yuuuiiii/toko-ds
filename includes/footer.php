</main> 
<script>
  // Fungsi Tema Global (Netral untuk Kasir & Owner)
  function toggleTheme() {
    const html = document.documentElement;
    const isDark = html.getAttribute('data-theme') === 'dark';
    html.setAttribute('data-theme', isDark ? 'light' : 'dark');
    const themeIcon = document.getElementById('theme-icon');
    if(themeIcon) {
        themeIcon.setAttribute('data-lucide', isDark ? 'moon' : 'sun');
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }
  }

  // TAMBAHAN: Paksa ikon langsung dirender saat halaman pertama kali dimuat
  document.addEventListener('DOMContentLoaded', function() {
      if (typeof lucide !== 'undefined') {
          lucide.createIcons();
      }
  });
</script>
</body>
</html>