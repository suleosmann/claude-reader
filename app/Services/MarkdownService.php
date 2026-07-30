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
     * Repair "mojibake" — UTF-8 text that was mis-decoded as Mac Roman when
     * copied out of a terminal, turning — → · └─ ⛽ into ‚Äî ‚Üí ¬∑ ‚îî‚îÄ ‚õΩ.
     *
     * We re-encode the garbled string back to Mac Roman bytes (which are the
     * original UTF-8 bytes) and keep the result only if it is valid UTF-8.
     */
    public function fixEncoding(string $s): string
    {
        if ($s === '') {
            return $s;
        }

        // Only attempt when the tell-tale garbled sequences are present.
        if (! preg_match('/[‚√¬ÃÄÅ]|â€/u', $s)) {
            return $s;
        }

        $bytes = @iconv('UTF-8', 'Macintosh', $s);

        if ($bytes === false) {
            return $s; // contained a character not representable in Mac Roman
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
