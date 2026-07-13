<?php
header('Content-Type: text/plain');

$path = '/home2/serv5eg/public_html/serv5-backend2026/app/Http/Resources/NavLinkResource.php';
if (!file_exists($path)) {
    echo "File not found: $path\n";
    exit;
}

echo "File content of $path:\n";
echo "==================================================\n";
echo file_get_contents($path);
echo "\n==================================================\n";
