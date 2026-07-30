<?php

namespace App\Services;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\CommonMark\Node\Block\FencedCode;
use League\CommonMark\Extension\CommonMark\Node\Block\IndentedCode;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\MarkdownConverter;
use Spatie\CommonMarkHighlighter\FencedCodeRenderer;
use Spatie\CommonMarkHighlighter\IndentedCodeRenderer;

class MarkdownService
{
    private ?MarkdownConverter $converter = null;

    /**
     * Render markdown to safe HTML. Raw HTML in the input is escaped;
     * code blocks are syntax-highlighted server-side (no CDN/JS needed).
     */
    public function toHtml(string $markdown, bool $fix = true): string
    {
        if ($fix) {
            $markdown = $this->fixEncoding($markdown);
        }

        return (string) $this->converter()->convert($markdown)->getContent();
    }

    /**
     * Mac Roman high half (bytes 0x80–0xFF) → Unicode code points. Index 0 is
     * byte 0x80. Used to reverse "mojibake" without relying on iconv having a
     * "Macintosh" encoding (which many PHP builds lack).
     */
    private const MAC_ROMAN_HIGH = [
        0x00C4, 0x00C5, 0x00C7, 0x00C9, 0x00D1, 0x00D6, 0x00DC, 0x00E1, 0x00E0, 0x00E2, 0x00E4, 0x00E3, 0x00E5, 0x00E7, 0x00E9, 0x00E8,
        0x00EA, 0x00EB, 0x00ED, 0x00EC, 0x00EE, 0x00EF, 0x00F1, 0x00F3, 0x00F2, 0x00F4, 0x00F6, 0x00F5, 0x00FA, 0x00F9, 0x00FB, 0x00FC,
        0x2020, 0x00B0, 0x00A2, 0x00A3, 0x00A7, 0x2022, 0x00B6, 0x00DF, 0x00AE, 0x00A9, 0x2122, 0x00B4, 0x00A8, 0x2260, 0x00C6, 0x00D8,
        0x221E, 0x00B1, 0x2264, 0x2265, 0x00A5, 0x00B5, 0x2202, 0x2211, 0x220F, 0x03C0, 0x222B, 0x00AA, 0x00BA, 0x03A9, 0x00E6, 0x00F8,
        0x00BF, 0x00A1, 0x00AC, 0x221A, 0x0192, 0x2248, 0x2206, 0x00AB, 0x00BB, 0x2026, 0x00A0, 0x00C0, 0x00C3, 0x00D5, 0x0152, 0x0153,
        0x2013, 0x2014, 0x201C, 0x201D, 0x2018, 0x2019, 0x00F7, 0x25CA, 0x00FF, 0x0178, 0x2044, 0x20AC, 0x2039, 0x203A, 0xFB01, 0xFB02,
        0x2021, 0x00B7, 0x201A, 0x201E, 0x2030, 0x00C2, 0x00CA, 0x00C1, 0x00CB, 0x00C8, 0x00CD, 0x00CE, 0x00CF, 0x00CC, 0x00D3, 0x00D4,
        0xF8FF, 0x00D2, 0x00DA, 0x00DB, 0x00D9, 0x0131, 0x02C6, 0x02DC, 0x00AF, 0x02D8, 0x02D9, 0x02DA, 0x00B8, 0x02DD, 0x02DB, 0x02C7,
    ];

    /**
     * Repair "mojibake" — UTF-8 text that was mis-decoded as Mac Roman when
     * copied out of a terminal, turning — → · └─ ⛽ into ‚Äî ‚Üí ¬∑ ‚îî‚îÄ ‚õΩ.
     *
     * Each garbled character is mapped back to the original byte, then the
     * byte string is kept only if it is valid UTF-8 (otherwise left untouched).
     */
    public function fixEncoding(string $s): string
    {
        if ($s === '' || ! preg_match('/[‚√¬ÃÄÅ]|â€/u', $s)) {
            return $s;
        }

        static $map = null;
        if ($map === null) {
            $map = [];
            foreach (self::MAC_ROMAN_HIGH as $i => $cp) {
                $map[mb_chr($cp, 'UTF-8')] = 0x80 + $i;
            }
        }

        $bytes = '';
        foreach (preg_split('//u', $s, -1, PREG_SPLIT_NO_EMPTY) as $ch) {
            $cp = mb_ord($ch, 'UTF-8');
            if ($cp === false) {
                return $s;
            }
            if ($cp < 0x80) {
                $bytes .= chr($cp);
            } elseif (isset($map[$ch])) {
                $bytes .= chr($map[$ch]);
            } else {
                return $s; // contains a real non-Mac-Roman char → not clean mojibake
            }
        }

        return mb_check_encoding($bytes, 'UTF-8') ? $bytes : $s;
    }

    private function converter(): MarkdownConverter
    {
        if ($this->converter !== null) {
            return $this->converter;
        }

        $environment = new Environment([
            'html_input' => 'escape',
            'allow_unsafe_links' => false,
        ]);

        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new GithubFlavoredMarkdownExtension());

        // Optional server-side syntax highlighting. Only wired up if the
        // spatie/commonmark-highlighter package is installed — otherwise code
        // blocks still render (just without colouring).
        if (class_exists(FencedCodeRenderer::class)) {
            $environment->addRenderer(FencedCode::class, new FencedCodeRenderer());
            $environment->addRenderer(IndentedCode::class, new IndentedCodeRenderer());
        }

        return $this->converter = new MarkdownConverter($environment);
    }
}
