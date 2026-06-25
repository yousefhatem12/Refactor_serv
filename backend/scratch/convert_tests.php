<?php

$dir = __DIR__ . '/../tests/Feature';
$files = glob($dir . '/*.php');

foreach ($files as $file) {
    $content = file_get_contents($file);
    
    // Add use PHPUnit\Framework\Attributes\Test; after namespace
    if (strpos($content, 'use PHPUnit\Framework\Attributes\Test;') === false) {
        $content = preg_replace(
            '/(namespace Tests\\\Feature;)/',
            "$1\n\nuse PHPUnit\Framework\Attributes\Test;",
            $content
        );
    }
    
    // Replace /** @test */ and /**  @test  */ with #[Test]
    $content = preg_replace(
        '/\/\*\*?\s*@test\s*\*\/(\r?\n\s*)public function/i',
        "#[Test]$1public function",
        $content
    );
    
    file_put_contents($file, $content);
    echo "Converted $file\n";
}
