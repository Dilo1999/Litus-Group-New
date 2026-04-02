<?php

namespace Database\Seeders;

use App\Models\GalleryEvent;
use Illuminate\Database\Seeder;

class GalleryEventSeeder extends Seeder
{
    public function run(): void
    {
        if (GalleryEvent::query()->exists()) {
            return;
        }

        $rows = [
            [
                'slug' => 'annual-general-meeting-2026',
                'title' => 'Annual General Meeting 2026',
                'date_display' => 'March 15, 2026',
                'description' => 'LITUS Group brought together leadership and stakeholders for our annual strategic review and planning session.',
                'cover_image' => 'https://images.unsplash.com/photo-1638799869566-b17fa794c4de?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxtb2Rlcm4lMjBiYXRocm9vbSUyMGludGVyaW9yJTIwbHV4dXJ5fGVufDF8fHx8MTc3NDMyMzk0NXww&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral',
                'image_alt' => 'Luxury bathroom interior',
                'sort_order' => 0,
            ],
            [
                'slug' => 'zaha-hotels-grand-opening',
                'title' => 'Zaha Hotels Grand Opening',
                'date_display' => 'February 28, 2026',
                'description' => 'Celebrating the grand opening of our newest luxury property in the Maldives with world-class amenities.',
                'cover_image' => 'https://images.unsplash.com/photo-1744974256549-8ece7cdb5dd2?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxtaW5pbWFsaXN0JTIwYmVkcm9vbSUyMHdoaXRlfGVufDF8fHx8MTc3NDM3MTIxNnww&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral',
                'image_alt' => 'Minimalist bedroom design',
                'sort_order' => 1,
            ],
            [
                'slug' => 'leadership-team-building-retreat',
                'title' => 'Leadership Team Building Retreat',
                'date_display' => 'February 20, 2026',
                'description' => 'An intensive retreat focused on strategic alignment and team cohesion for our executive leadership.',
                'cover_image' => 'https://images.unsplash.com/photo-1667584523543-d1d9cc828a15?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxtb2Rlcm4lMjBsaXZpbmclMjByb29tJTIwaW50ZXJpb3J8ZW58MXx8fHwxNzc0MzQwNDg1fDA&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral',
                'image_alt' => 'Modern living room',
                'sort_order' => 2,
            ],
            [
                'slug' => 'excellence-award-ceremony-2026',
                'title' => 'Excellence Award Ceremony 2026',
                'date_display' => 'February 10, 2026',
                'description' => 'Honoring outstanding achievements and contributions across all LITUS Group divisions.',
                'cover_image' => 'https://images.unsplash.com/photo-1759223198981-661cadbbff36?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxsdXh1cnklMjBob3RlbCUyMHN1aXRlJTIwcm9vbXxlbnwxfHx8fDE3NzQ0MzQ2MDJ8MA&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral',
                'image_alt' => 'Hotel suite interior',
                'sort_order' => 3,
            ],
            [
                'slug' => 'new-headquarters-inauguration',
                'title' => 'New Headquarters Inauguration',
                'date_display' => 'January 25, 2026',
                'description' => 'Opening our state-of-the-art headquarters facility, marking a new chapter in our company history.',
                'cover_image' => 'https://images.unsplash.com/photo-1622131815452-cc00d8d89f02?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxtb2Rlcm4lMjBvZmZpY2UlMjB3b3Jrc3BhY2UlMjBpbnRlcmlvcnxlbnwxfHx8fDE3NzQzODcyNTF8MA&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral',
                'image_alt' => 'Modern workspace',
                'sort_order' => 4,
            ],
            [
                'slug' => 'favala-product-launch-event',
                'title' => 'Favala Product Launch Event',
                'date_display' => 'January 15, 2026',
                'description' => 'Introducing our latest line of premium hardware and construction materials to the market.',
                'cover_image' => 'https://images.unsplash.com/photo-1680210849773-f97a41c6b7ed?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxjb250ZW1wb3JhcnklMjBraXRjaGVuJTIwZGVzaWdufGVufDF8fHx8MTc3NDQzODU3N3ww&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral',
                'image_alt' => 'Contemporary kitchen',
                'sort_order' => 5,
            ],
            [
                'slug' => 'international-trade-show-2026',
                'title' => 'International Trade Show 2026',
                'date_display' => 'January 5, 2026',
                'description' => "Showcasing LITUS Group's diverse portfolio at the premier international trade exhibition.",
                'cover_image' => 'https://images.unsplash.com/photo-1771033834141-023d630b3965?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxtb2Rlcm4lMjByZXRhaWwlMjBzdG9yZSUyMGludGVyaW9yfGVufDF8fHx8MTc3NDQyMDgxN3ww&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral',
                'image_alt' => 'Retail store interior',
                'sort_order' => 6,
            ],
            [
                'slug' => '25th-anniversary-celebration',
                'title' => '25th Anniversary Celebration',
                'date_display' => 'December 20, 2025',
                'description' => 'A milestone celebration honoring 25 years of excellence, growth, and innovation.',
                'cover_image' => 'https://images.unsplash.com/photo-1769773297747-bd00e31b33aa?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxlbGVnYW50JTIwcmVzdGF1cmFudCUyMGRpbmluZ3xlbnwxfHx8fDE3NzQ0Mzg1ODN8MA&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral',
                'image_alt' => 'Restaurant dining area',
                'sort_order' => 7,
            ],
        ];

        foreach ($rows as $row) {
            GalleryEvent::query()->create([
                ...$row,
                'gallery_images' => null,
                'is_active' => true,
            ]);
        }
    }
}
