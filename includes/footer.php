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
// Fungsi Akhiri Shift & Logout
  function confirmEndShift() {
      // Validasi keamanan: cegah kasir tidak sengaja kepencet
      const konfirmasi = confirm("YAKIN INGIN MENGAKHIRI SHIFT?\n\nPastikan uang laci sudah sesuai sebelum keluar dari sistem.");
      
      if (konfirmasi) {
          // Arahkan ke file pemutus sesi
          window.location.href = "<?= BASE_URL ?>/logout.php";
      }
  }
</script>

<script src="<?= BASE_URL ?>/assets/js/kasir.js"></script>

</body>
</html>