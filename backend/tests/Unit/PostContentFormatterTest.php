<?php

namespace Tests\Unit;

use App\Services\PostContentFormatter;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class PostContentFormatterTest extends TestCase
{
    #[Test]
    public function it_normalizes_html_and_extracts_structured_content(): void
    {
        $result = PostContentFormatter::format(
            '<h2>Overview</h2>\\n<p style="text-align: center"><b>Bold</b> <s>removed</s></p>'
            . '<ol><li>First</li><li>Second</li></ol>'
            . '<ul><li>Alpha</li></ul>'
            . '<table><tr><th>Name</th><th>Price</th></tr><tr><td>Hosting</td><td>100</td></tr></table>'
        );

        $this->assertStringContainsString('<strong>Bold</strong>', $result['content']);
        $this->assertStringContainsString('<del>removed</del>', $result['content']);
        $this->assertStringNotContainsString('\\n', $result['content']);
        $this->assertSame(['Name', 'Price'], $result['table']['head']);
        $this->assertSame([['Name' => 'Hosting', 'Price' => '100']], $result['table']['rows']);
        $this->assertSame([
            ['type' => 'ordered', 'list' => ['First', 'Second']],
            ['type' => 'unordered', 'list' => ['Alpha']],
        ], $result['list']);
    }

    #[Test]
    public function it_keeps_literal_newlines_inside_code_samples(): void
    {
        $result = PostContentFormatter::format('<p>Before\\nAfter</p><pre><code>line 1\\nline 2</code></pre>');

        $this->assertStringContainsString("<p>Before\nAfter</p>", $result['content']);
        $this->assertStringContainsString('<code>line 1\\nline 2</code>', $result['content']);
    }
}
