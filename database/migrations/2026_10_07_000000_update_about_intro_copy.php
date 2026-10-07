<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Refresh the About page intro copy for the redesigned page.
 * Only replaces values still holding the original default text,
 * so anything edited in Page Customization is left untouched.
 */
return new class extends Migration
{
    private const COPY = [
        'about.intro.paragraph_1' => [
            'old' => 'LITUS Group is a diversified business conglomerate with a strong presence across multiple sectors including hospitality, construction, automotive, technology, and trading. Our commitment to excellence drives everything we do.',
            'new' => 'LITUS Group brings together businesses in automotive, building materials, logistics, engineering, currency exchange, travel, hospitality, construction and technology. Based in the Maldives, we serve individuals, businesses, resorts and project teams through companies specialising in their respective industries.',
        ],
        'about.intro.paragraph_2' => [
            'old' => 'With a portfolio spanning from luxury hotels and resorts to cutting-edge technology solutions, we deliver comprehensive services that meet the evolving needs of our clients. Our diverse businesses work in synergy to create value and drive sustainable growth.',
            'new' => 'From motorcycle spare parts and home improvements to freight movements and resort engineering support, our businesses provide practical products and services that help customers keep everyday life and business moving.',
        ],
    ];

    public function up(): void
    {
        $this->swap('old', 'new');
    }

    public function down(): void
    {
        $this->swap('new', 'old');
    }

    private function swap(string $from, string $to): void
    {
        foreach (self::COPY as $key => $copy) {
            DB::table('site_settings')
                ->where('key', $key)
                ->where('value', $copy[$from])
                ->update(['value' => $copy[$to], 'updated_at' => now()]);
        }
    }
};
