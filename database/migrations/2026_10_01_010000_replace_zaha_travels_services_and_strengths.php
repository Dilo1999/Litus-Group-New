<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * The earlier Zaha Travels content migration skipped services and strengths when
 * an admin had uploaded icons for the original seed items, because the stored
 * values were arrays rather than plain strings. This compares labels only and
 * replaces the original seed set with the approved design copy, including the
 * service descriptions and strength captions (editable in the admin).
 * Lists an admin has changed (different labels) are left alone.
 */
return new class extends Migration
{
    /** @var array<string, list<string>> */
    protected array $seedLabels = [
        'services' => ['Flight Bookings', 'Hotel Reservations', 'Tour Packages', 'Visa Assistance', 'Travel Insurance', 'Corporate Travel'],
        'strengths' => ['Competitive Prices', 'Expert Guidance', 'Custom Packages', '24/7 Support'],
    ];

    /** @var array<string, list<array{label: string, description: string}>> */
    protected array $designItems = [
        'services' => [
            ['label' => 'Personal travel planning', 'description' => 'Destination advice and itineraries shaped around interests, preferences and budget.'],
            ['label' => 'Stays & private tours', 'description' => 'Resort reservations, split stays and Sri Lanka touring for couples, families and groups.'],
            ['label' => 'Transfers & experiences', 'description' => 'Airport assistance, island connections, ground transport and memorable excursions.'],
            ['label' => 'Support during the journey', 'description' => 'Personal guidance before departure and 24/7 assistance during the trip.'],
            ['label' => 'Combined holidays', 'description' => 'Sri Lanka discovery and Maldives relaxation, coordinated in one personalised journey.'],
            ['label' => 'Travel trade partnerships', 'description' => 'Destination advice, property recommendations and booking coordination for agencies and tour operators.'],
        ],
        'strengths' => [
            ['label' => '100+', 'description' => 'Resort & hotel partners'],
            ['label' => 'Two destinations', 'description' => 'Maldives & Sri Lanka'],
            ['label' => 'Personal experts', 'description' => 'Guidance from start to finish'],
            ['label' => '24/7 support', 'description' => 'During your trip'],
        ],
    ];

    /**
     * Labels written by the earlier migration (plain strings, no descriptions).
     *
     * @var array<string, list<string>>
     */
    protected array $earlierLabels = [
        'services' => ['Personal travel planning', 'Stays & private tours', 'Transfers & experiences', 'Support during the journey', 'Combined holidays', 'Travel trade partnerships'],
        'strengths' => ['100+', 'Two destinations', 'Personal experts', '24/7 support'],
    ];

    public function up(): void
    {
        $row = $this->zahaRow();
        if (! $row) {
            return;
        }

        $updates = [];
        foreach ($this->designItems as $column => $items) {
            $labels = $this->labels($row->{$column});
            // Replace the original seed set, or the earlier plain-string copy that lacks descriptions.
            if ($labels === [] || $labels === $this->seedLabels[$column] || ($labels === $this->earlierLabels[$column] && ! $this->hasDescriptions($row->{$column}))) {
                $updates[$column] = json_encode($items);
            }
        }

        if ($updates !== []) {
            DB::table('companies')->where('id', $row->id)->update($updates + ['updated_at' => now()]);
        }
    }

    public function down(): void
    {
        $row = $this->zahaRow();
        if (! $row) {
            return;
        }

        $updates = [];
        foreach ($this->designItems as $column => $items) {
            if ($this->labels($row->{$column}) === array_column($items, 'label')) {
                $updates[$column] = json_encode($this->seedLabels[$column]);
            }
        }

        if ($updates !== []) {
            DB::table('companies')->where('id', $row->id)->update($updates + ['updated_at' => now()]);
        }
    }

    protected function zahaRow(): ?object
    {
        if (! Schema::hasTable('companies')) {
            return null;
        }

        return DB::table('companies')->where('slug', 'zaha-travels')->first();
    }

    /**
     * @return list<string>
     */
    protected function labels(?string $json): array
    {
        $items = json_decode((string) $json, true);
        if (! is_array($items)) {
            return [];
        }

        return array_values(array_map(
            fn ($item) => trim((string) (is_array($item) ? ($item['label'] ?? '') : $item)),
            $items
        ));
    }

    protected function hasDescriptions(?string $json): bool
    {
        $items = json_decode((string) $json, true);

        return is_array($items) && collect($items)->contains(fn ($item) => is_array($item) && filled($item['description'] ?? null));
    }
};
