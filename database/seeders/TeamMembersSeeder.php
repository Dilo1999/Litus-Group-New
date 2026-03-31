<?php

namespace Database\Seeders;

use App\Models\TeamMember;
use Illuminate\Database\Seeder;

class TeamMembersSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            [
                'name' => 'Mohamed Zahid',
                'role' => 'Chief Executive Officer',
                'bio' => "With exceptional vision and strategic leadership, Mohamed Zahid guides LITUS Group's overall direction and growth across all business divisions. His expertise in corporate governance and business development has positioned LITUS Group as a prominent conglomerate, driving innovation and excellence across 16 diverse subsidiaries.",
                'expertise' => 'Strategic Leadership, Corporate Governance, Business Growth',
                'image' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?auto=format&fit=crop&w=800&q=80',
            ],
            [
                'name' => 'Chamara Madusanka',
                'role' => 'Chief Business Development Officer',
                'bio' => "Chamara spearheads LITUS Group's business development initiatives, identifying new opportunities and forging strategic partnerships across multiple sectors. His innovative approach to market expansion and relationship building has been instrumental in the company's continuous growth and diversification.",
                'expertise' => 'Business Development, Strategic Partnerships, Market Expansion',
                'image' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?auto=format&fit=crop&w=800&q=80',
            ],
            [
                'name' => 'Ahmed Zahir',
                'role' => 'General Manager, LITUS Automobiles',
                'bio' => 'Ahmed leads LITUS Automobiles with deep industry expertise and a customer-centric approach. His operational excellence and market knowledge have established LITUS Automobiles as a trusted name in the automotive sector, delivering premium brands and exceptional service to customers.',
                'expertise' => 'Automotive Management, Operations Excellence, Customer Relations',
                'image' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?auto=format&fit=crop&w=800&q=80',
            ],
            [
                'name' => 'Asif Rasheed',
                'role' => 'Director - Sales & Marketing',
                'bio' => "Asif drives LITUS Group's sales and marketing strategies with creativity and market insight. His comprehensive approach to brand building and customer engagement has elevated the group's market presence, ensuring consistent growth and strong brand recognition across all business divisions.",
                'expertise' => 'Sales Strategy, Brand Marketing, Customer Engagement',
                'image' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?auto=format&fit=crop&w=800&q=80',
            ],
        ];

        foreach ($rows as $index => $row) {
            TeamMember::query()->updateOrCreate(
                ['name' => $row['name']],
                [
                    'role' => $row['role'] ?? null,
                    'bio' => $row['bio'] ?? null,
                    'expertise' => $row['expertise'] ?? null,
                    'photo' => $row['image'] ?? null,
                    'is_active' => true,
                    'sort_order' => $index + 1,
                ]
            );
        }
    }
}

