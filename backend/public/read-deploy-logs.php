<?php
header('Content-Type: text/plain');

$logDir = '/home2/serv5eg/.cpanel/logs';
if (!is_dir($logDir)) {
    // Try /home/serv5eg/ if /home2/ is mapped differently in some systems
    $logDir = '/home/serv5eg/.cpanel/logs';
}

if (!is_dir($logDir)) {
    echo "Log directory not found: /home2/serv5eg/.cpanel/logs or /home/serv5eg/.cpanel/logs\n";
    // List parent directory to see what exists
    echo "Parent directory /home2/serv5eg/.cpanel:\n";
    if (is_dir('/home2/serv5eg/.cpanel')) {
        print_r(scandir('/home2/serv5eg/.cpanel'));
    } else {
        echo "/home2/serv5eg/.cpanel does not exist or is not readable.\n";
    }
    exit;
}

echo "Found log directory: $logDir\n";
$files = scandir($logDir);
// Sort files by modified time desc
usort($files, function($a, $b) use ($logDir) {
    return filemtime("$logDir/$b") - filemtime("$logDir/$a");
});

echo "\nRecent log files:\n";
foreach (array_slice($files, 0, 5) as $file) {
    if ($file === '.' || $file === '..') continue;
    $path = "$logDir/$file";
    echo "File: $file (Size: " . filesize($path) . " bytes, Modified: " . date('Y-m-d H:i:s', filemtime($path)) . ")\n";
    echo "--------------------------------------------------\n";
    echo file_get_contents($path);
    echo "\n--------------------------------------------------\n\n";
}
