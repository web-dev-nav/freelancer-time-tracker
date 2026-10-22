<?php

namespace App\Support;

class DescriptionText
{
    public static function fromHtml(?string $html): string
    {
        $text = preg_replace('~<(script|style)\b[^>]*>.*?</\1>~is', '', $html ?? '');
        $text = preg_replace('~<br\s*/?>|</(?:p|div|li|ul|ol|blockquote|h[1-6])\s*>~i', "\n", $text);
        $text = preg_replace('~<li\b[^>]*>~i', '• ', $text);
        $text = strip_tags($text);
        // Decode after removing real markup, preserving literal user-entered <text>.
        for ($i = 0; $i < 2; $i++) {
            $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }
        $text = str_replace(["\xc2\xa0", "\r\n", "\r"], [' ', "\n", "\n"], $text);
        $text = preg_replace('/[\t ]+/', ' ', $text);
        $text = preg_replace('/ *\n */', "\n", $text);

        return trim(preg_replace('/\n{3,}/', "\n\n", $text));
    }
}
