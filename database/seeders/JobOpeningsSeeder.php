<?php

namespace Database\Seeders;

use App\Models\JobOpening;
use Illuminate\Database\Seeder;

class JobOpeningsSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            [
                'title' => 'Senior Automotive Technician',
                'company' => 'LITUS Service Center',
                'location' => 'Maldives',
                'type' => 'Full-time',
                'department' => 'Automotive',
            ],
            [
                'title' => 'Hotel Manager',
                'company' => 'Zaha Residence & Hotels',
                'location' => 'Maldives',
                'type' => 'Full-time',
                'department' => 'Hospitality',
            ],
            [
                'title' => 'Network Engineer',
                'company' => 'LITUS Connect',
                'location' => 'Maldives',
                'type' => 'Full-time',
                'department' => 'Technology',
            ],
            [
                'title' => 'Construction Project Manager',
                'company' => 'LITUS Constructions',
                'location' => 'Maldives',
                'type' => 'Full-time',
                'department' => 'Construction',
            ],
            [
                'title' => 'Sales Executive',
                'company' => 'LITUS Automobiles',
                'location' => 'Maldives',
                'type' => 'Full-time',
                'department' => 'Sales',
            ],
            [
                'title' => 'Logistics Coordinator',
                'company' => 'LITUS Shipping',
                'location' => 'Maldives',
                'type' => 'Full-time',
                'department' => 'Logistics',
            ],
        ];

        foreach ($rows as $index => $row) {
            JobOpening::query()->updateOrCreate(
                [
                    'title' => $row['title'],
                    'company' => $row['company'],
                ],
                [
                    'location' => $row['location'] ?? null,
                    'type' => $row['type'] ?? null,
                    'department' => $row['department'] ?? null,
                    'description' => $row['description'] ?? null,
                    'is_active' => true,
                    'sort_order' => $index + 1,
                ]
            );
        }
    }
}

