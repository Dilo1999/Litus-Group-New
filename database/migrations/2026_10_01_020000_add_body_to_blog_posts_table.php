<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Blog posts move from "plain text + content sections" to one editable HTML body.
 * Existing posts are converted so they render exactly as before; the old
 * content / content_blocks columns are kept untouched as a backup.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('blog_posts', function (Blueprint $table) {
            $table->longText('body')->nullable()->after('content_blocks');
        });

        DB::table('blog_posts')->orderBy('id')->each(function (object $post): void {
            $html = $this->legacyToHtml($post->content_blocks, $post->content);

            if ($html !== '') {
                DB::table('blog_posts')->where('id', $post->id)->update(['body' => $html]);
            }
        });
    }

    public function down(): void
    {
        Schema::table('blog_posts', function (Blueprint $table) {
            $table->dropColumn('body');
        });
    }

    private function legacyToHtml(?string $blocksJson, ?string $content): string
    {
        $blocks = json_decode((string) $blocksJson, true);
        $parts = [];

        foreach (is_array($blocks) ? $blocks : [] as $block) {
            $type = $block['type'] ?? null;
            $data = is_array($block['data'] ?? null) ? $block['data'] : [];

            if ($type === 'paragraph') {
                $text = (string) ($data['text'] ?? '');
                if ($text === '' && is_string($data['html'] ?? null)) {
                    $text = strip_tags($data['html']);
                }
                $parts[] = $this->paragraphs($text);
            } elseif ($type === 'quote' && trim((string) ($data['text'] ?? '')) !== '') {
                // The old page wrapped quotes in curly quote marks at render time.
                $quote = '<blockquote><p>“'.nl2br(e(trim($data['text'])), false).'”</p>';
                if (trim((string) ($data['attribution'] ?? '')) !== '') {
                    $quote .= '<cite>'.e(trim($data['attribution'])).'</cite>';
                }
                $parts[] = $quote.'</blockquote>';
            }
        }

        $parts = array_filter($parts);

        // Same rule as the old article page: sections win, plain content is the fallback.
        if ($parts === []) {
            $parts = [$this->paragraphs((string) $content)];
        }

        return trim(implode("\n", array_filter($parts)));
    }

    /** Blank lines start a new paragraph; single line breaks are kept (old page used whitespace-pre-line). */
    private function paragraphs(string $text): string
    {
        $chunks = preg_split('/\R\s*\R/u', trim(str_replace("\r\n", "\n", $text))) ?: [];

        return implode("\n", array_map(
            fn (string $chunk): string => '<p>'.nl2br(e(trim($chunk)), false).'</p>',
            array_filter($chunks, fn (string $chunk): bool => trim($chunk) !== '')
        ));
    }
};
