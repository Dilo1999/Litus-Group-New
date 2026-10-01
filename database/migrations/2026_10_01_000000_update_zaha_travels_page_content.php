<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Zaha Travels now has a bespoke page (site/companies/zaha-travels.blade.php)
 * that reads its copy from the company record. Replace the original seed
 * values with the approved copy, leaving any field an admin already edited.
 */
return new class extends Migration
{
    /** @var array<string, mixed> */
    protected array $seedValues = [
        'tagline' => 'Your journey, our passion',
        'description' => 'Zaha Travels provides complete travel and tourism services, creating unforgettable experiences for every traveler.',
        'description_secondary' => null,
        'services' => ['Flight Bookings', 'Hotel Reservations', 'Tour Packages', 'Visa Assistance', 'Travel Insurance', 'Corporate Travel'],
        'strengths' => ['Competitive Prices', 'Expert Guidance', 'Custom Packages', '24/7 Support'],
    ];

    /** @var array<string, mixed> */
    protected array $newValues = [
        'tagline' => 'Travel guided by Experts',
        'description' => 'Zaha Travels is a destination management company within LITUS Group, specialising in the Maldives and Sri Lanka. We serve individual travellers, travel agencies and tour operators with destination knowledge and personalised travel arrangements.',
        'description_secondary' => 'With our headquarters in Malé and an office in Colombo, we bring local insight to island escapes, private tours and combined holidays. Our experts connect the right stays, experiences and transfers into a journey shaped around each traveller.',
        'services' => ['Personal travel planning', 'Stays & private tours', 'Transfers & experiences', 'Support during the journey', 'Combined holidays', 'Travel trade partnerships'],
        'strengths' => ['100+', 'Two destinations', 'Personal experts', '24/7 support'],
    ];

    public function up(): void
    {
        $this->swap($this->seedValues, $this->newValues);
    }

    public function down(): void
    {
        $this->swap($this->newValues, $this->seedValues);
    }

    /**
     * @param  array<string, mixed>  $from
     * @param  array<string, mixed>  $to
     */
    protected function swap(array $from, array $to): void
    {
        if (! Schema::hasTable('companies')) {
            return;
        }

        $row = DB::table('companies')->where('slug', 'zaha-travels')->first();
        if (! $row) {
            return;
        }

        $updates = [];
        foreach ($from as $column => $expected) {
            $current = $row->{$column};
            if (is_array($expected)) {
                $current = json_decode((string) $current, true);
            }
            if ($current === $expected || blank($current)) {
                $updates[$column] = is_array($to[$column]) ? json_encode($to[$column]) : $to[$column];
            }
        }

        if ($updates !== []) {
            DB::table('companies')->where('id', $row->id)->update($updates + ['updated_at' => now()]);
        }
    }
};
