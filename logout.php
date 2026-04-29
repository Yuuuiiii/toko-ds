<?php
session_start();
session_unset();
session_destroy();

// Tambahkan script ini untuk memastikan cache benar-benar bersih saat pindah halaman
header("Cache-Control: no-cache, no-store, must-revalidate"); 
header("Pragma: no-cache"); 
header("Expires: 0"); 

header("Location: index.php");
exit;
?>