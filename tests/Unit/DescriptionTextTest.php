<?php

namespace Tests\Unit;

use App\Support\DescriptionText;
use PHPUnit\Framework\TestCase;

class DescriptionTextTest extends TestCase
{
    public function test_descriptions_keep_readable_paragraphs_lists_and_literal_characters(): void
    {
        $html = '<p>Research &amp; design&nbsp;review</p><ul><li>Fix &lt;header&gt;</li><li>Client &quot;notes&quot;</li></ul><script>bad()</script>';
        $this->assertSame("Research & design review\n• Fix <header>\n• Client \"notes\"", DescriptionText::fromHtml($html));
    }
}
