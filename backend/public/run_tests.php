<?php
header('Content-Type: text/plain; charset=utf-8');
// Run PHPUnit via shell exec and write to file
$cmd = '..\\vendor\\bin\\phpunit --configuration ..\\phpunit.xml';
exec($cmd, $output, $return_var);

$result = "Command: $cmd\n";
$result .= "Exit Code: $return_var\n\n";
$result .= implode("\n", $output);

file_put_contents(__DIR__.'/test_results.txt', $result);
echo $result;
