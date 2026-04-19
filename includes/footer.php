<?php
// includes/footer.php
?>
</main> <script>
  // 1. Render Ikon Offline
  if (typeof lucide !== 'undefined') {
    lucide.createIcons();
  }

  // 2. Jam Digital (Untuk layar Kasir)
  const clockEl = document.getElementById('live-clock');
  if (clockEl) {
      setInterval(() => {
          const now = new Date();
          clockEl.innerText = now.toLocaleTimeString('id-ID');
      }, 1000);
  }

  // 3. Tanggal Dinamis (Untuk layar Owner)
  const dateEl = document.getElementById('page-date');
  if (dateEl) {
    dateEl.textContent = new Date().toLocaleDateString('id-ID', { 
      weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' 
    });
  }

  // 4. Fungsi Tema Global
  function toggleTheme() {
    const html = document.documentElement;
    const isDark = html.getAttribute('data-theme') === 'dark';
    html.setAttribute('data-theme', isDark ? 'light' : 'dark');
    const themeIcon = document.getElementById('theme-icon');
    if(themeIcon) {
        themeIcon.setAttribute('data-lucide', isDark ? 'moon' : 'sun');
        lucide.createIcons();
    }
  }

  // 5. Placeholder Akhiri Shift
  function confirmEndShift() {
      alert("Fase Blind Close (Tutup Shift Buta) akan dihubungkan ke Backend.");
  }
</script>

<script src="<?= BASE_URL ?>/assets/js/kasir.js"></script>

</body>
</html>