<?php
/**
 * Script untuk menjalankan Development Server LokerOne
 * Cara penggunaan: php run.php
 */

$host = 'localhost';
$port = 8000;

echo "=================================================\n";
echo "         MEMULAI SERVER LOKERONE...              \n";
echo "=================================================\n\n";

echo "Server berjalan di: http://$host:$port\n";
echo "Tekan Ctrl+C untuk menghentikan server.\n\n";

// Menjalankan built-in web server PHP
$command = sprintf('php -S %s:%d', $host, $port);

// passthru akan mengeksekusi perintah dan meneruskan outputnya langsung ke terminal
passthru($command);
?>
