<?php

namespace App\Support;

/**
 * Turns the plain-text job description typed in the admin into structured blocks
 * so the career detail page can style headings, bullet lists and key facts.
 *
 * Recognised input (all optional):
 *  - Headings: a short line ending in ":" or a short line followed by bullet lines ("Key Responsibilities").
 *  - Bullets: lines starting with a symbol or emoji (🔹 • - * ✔ ➤ …) or "1." / "1)".
 *  - Facts: "Salary: …", "Application Deadline: …" etc. (see FACT_KEYS), lifted out for the summary card.
 *  - Everything else: paragraphs, separated by blank lines.
 */
class JobDescription
{
    /** Lower-cased label => display label. */
    private const FACT_KEYS = [
        'salary' => 'Salary',
        'pay' => 'Salary',
        'salary range' => 'Salary',
        'location' => 'Location',
        'deadline' => 'Deadline',
        'application deadline' => 'Deadline',
        'closing date' => 'Deadline',
        'apply before' => 'Deadline',
        'experience' => 'Experience',
        'working hours' => 'Working hours',
        'start date' => 'Start date',
        'contract' => 'Contract',
        'reports to' => 'Reports to',
        'reporting to' => 'Reports to',
    ];

    private const PREFIX = '/^(?:[\s\p{So}\p{Sk}\p{Sm}\p{Cf}\p{Co}\x{FE0F}\x{2022}\x{00B7}\x{2023}\x{2043}\x{25E6}*\-\x{2013}\x{2014}>~]|\d{1,2}[.)](?=\s))+/u';

    /**
     * @return array{blocks: list<array<string, mixed>>, facts: array<string, string>}
     */
    public static function parse(?string $text): array
    {
        $text = trim(str_replace(["\r\n", "\r"], "\n", (string) $text));
        if ($text === '') {
            return ['blocks' => [], 'facts' => []];
        }

        // Classify every line first so headings can look ahead at the next line.
        $lines = [];
        foreach (explode("\n", $text) as $raw) {
            $raw = trim($raw);
            if ($raw === '') {
                $lines[] = ['kind' => 'blank'];

                continue;
            }

            $clean = trim((string) preg_replace(self::PREFIX, '', $raw));
            $hadMarker = $clean !== $raw;
            if ($clean === '') {
                continue;
            }

            if (preg_match('/^([\p{L}][\p{L} \-]{1,30}?)\s*:\s*(.+)$/u', $clean, $m)) {
                $key = mb_strtolower(trim($m[1]));
                if (isset(self::FACT_KEYS[$key]) && mb_strlen($m[2]) <= 120) {
                    $lines[] = ['kind' => 'fact', 'label' => self::FACT_KEYS[$key], 'value' => trim($m[2])];

                    continue;
                }
            }

            if (mb_strlen($clean) <= 60 && str_ends_with($clean, ':')) {
                $lines[] = ['kind' => 'heading', 'text' => rtrim($clean, ': ')];

                continue;
            }

            $lines[] = ['kind' => $hadMarker ? 'bullet' : 'text', 'text' => $clean];
        }

        // Short text lines without end punctuation that introduce a list become headings.
        foreach ($lines as $i => $line) {
            if ($line['kind'] !== 'text' || mb_strlen($line['text']) > 50 || preg_match('/[.!?,;]$/u', $line['text'])) {
                continue;
            }
            $next = $lines[$i + 1] ?? null;
            if ($next && $next['kind'] === 'bullet') {
                $lines[$i]['kind'] = 'heading';
            }
        }

        $blocks = [];
        $facts = [];
        $current = null;

        $flush = function () use (&$blocks, &$current) {
            if ($current !== null) {
                $blocks[] = $current;
                $current = null;
            }
        };

        foreach ($lines as $line) {
            switch ($line['kind']) {
                case 'blank':
                    $flush();
                    break;
                case 'fact':
                    $flush();
                    $facts[$line['label']] ??= $line['value'];
                    break;
                case 'heading':
                    $flush();
                    $blocks[] = ['type' => 'heading', 'text' => $line['text']];
                    break;
                case 'bullet':
                    if (($current['type'] ?? null) !== 'list') {
                        $flush();
                        $current = ['type' => 'list', 'items' => []];
                    }
                    $current['items'][] = $line['text'];
                    break;
                default:
                    if (($current['type'] ?? null) !== 'paragraph') {
                        $flush();
                        $current = ['type' => 'paragraph', 'lines' => []];
                    }
                    $current['lines'][] = $line['text'];
            }
        }
        $flush();

        return ['blocks' => $blocks, 'facts' => $facts];
    }

    /**
     * Groups blocks into sections that start at each heading (the first may have no heading).
     *
     * @param  list<array<string, mixed>>  $blocks
     * @return list<array{heading: ?string, blocks: list<array<string, mixed>>}>
     */
    public static function sections(array $blocks): array
    {
        $sections = [];
        $current = ['heading' => null, 'blocks' => []];

        foreach ($blocks as $block) {
            if ($block['type'] === 'heading') {
                if ($current['heading'] !== null || $current['blocks'] !== []) {
                    $sections[] = $current;
                }
                $current = ['heading' => $block['text'], 'blocks' => []];

                continue;
            }
            $current['blocks'][] = $block;
        }

        if ($current['heading'] !== null || $current['blocks'] !== []) {
            $sections[] = $current;
        }

        return $sections;
    }

    /** Escapes text and turns email addresses and URLs into links. */
    public static function linkify(string $text): string
    {
        $escaped = e($text);

        $escaped = preg_replace(
            '/\b[A-Za-z0-9._%+\-]+@[A-Za-z0-9.\-]+\.[A-Za-z]{2,}\b/',
            '<a href="mailto:$0" class="font-semibold text-brand-600 underline decoration-brand-200 underline-offset-4 hover:text-brand-700">$0</a>',
            $escaped
        );

        return (string) preg_replace(
            '~(?<!["=])\bhttps?://[^\s<]+~',
            '<a href="$0" target="_blank" rel="noopener" class="font-semibold text-brand-600 underline decoration-brand-200 underline-offset-4 hover:text-brand-700">$0</a>',
            $escaped
        );
    }
}
