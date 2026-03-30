<?php

namespace App\Support;

class SiteData
{
    /**
     * Company logos live in `public/assets/logo/` (filenames may contain spaces).
     */
    public static function companyLogoUrl(?string $basename): ?string
    {
        if ($basename === null || $basename === '') {
            return null;
        }
        if (str_starts_with($basename, 'http://') || str_starts_with($basename, 'https://')) {
            return $basename;
        }

        return asset('assets/logo/' . rawurlencode($basename));
    }

    /** Header/footer brand mark. */
    public static function brandLogoUrl(): string
    {
        return self::companyLogoUrl('LITUS Group - logos_Artboard 1 - LITUS Group.png') ?? '';
    }

    public static function companies(): array
    {
        return [
            [
                'id' => '1',
                'name' => 'LITUS Group',
                'slug' => 'litus-group',
                'tagline' => 'Taking diversification to a whole new level',
                'description' => 'LITUS Group is a diversified business conglomerate with a strong presence across multiple sectors including hospitality, construction, automotive, technology, and trading.',
                'category' => 'Corporate Headquarters',
                'division' => 'corporate',
                'logo' => 'LITUS Group - logos_Artboard 1 - LITUS Group.png',
                'icon' => 'building2',
                'hotline' => '+960 332 2288',
                'email' => 'info@litusgroup.com',
                'services' => [
                    'Corporate Management',
                    'Strategic Planning',
                    'Business Development',
                    'Investment Management',
                    'Group Operations',
                    'Financial Services',
                ],
                'strengths' => [
                    'Diversified Portfolio',
                    'Strategic Leadership',
                    'Innovation Focus',
                    'Sustainable Growth',
                ],
                'featured' => true,
            ],
            [
                'id' => '2',
                'name' => 'LITUS Maldives',
                'slug' => 'litus-maldives',
                'tagline' => 'Premium hospitality experiences',
                'description' => 'LITUS Maldives delivers world-class resort and hotel operations, providing exceptional experiences for travelers seeking luxury and comfort in paradise.',
                'category' => 'Hospitality',
                'division' => 'logistics-shipping',
                'logo' => 'LITUS Group - logos_Artboard 2 - LITUS Maldives.png',
                'icon' => 'hotel',
                'hotline' => '+960 332 2289',
                'email' => 'info@litusmaldives.com',
                'services' => [
                    'Resort Management',
                    'Hotel Operations',
                    'Guest Services',
                    'Event Management',
                    'Catering Services',
                    'Concierge Services',
                ],
                'strengths' => [
                    'Luxury Accommodations',
                    'Exceptional Service',
                    'Prime Locations',
                    'World-Class Amenities',
                ],
                'featured' => true,
            ],
            [
                'id' => '3',
                'name' => 'LITUS Shipping',
                'slug' => 'litus-shipping',
                'tagline' => 'Connecting the world through logistics',
                'description' => 'LITUS Shipping provides comprehensive international freight and cargo services, ensuring reliable and efficient transportation solutions across global markets.',
                'category' => 'Logistics',
                'division' => 'logistics-shipping',
                'logo' => 'LITUS Group - logos_Artboard 3 - LITUS Shipping.png',
                'icon' => 'ship',
                'hotline' => '+960 332 2290',
                'email' => 'shipping@litusgroup.com',
                'services' => [
                    'International Freight',
                    'Cargo Services',
                    'Customs Clearance',
                    'Warehousing',
                    'Supply Chain Management',
                    'Express Delivery',
                ],
                'strengths' => [
                    'Global Network',
                    'Timely Delivery',
                    'Secure Handling',
                    'Cost-Effective Solutions',
                ],
                'featured' => true,
            ],
            [
                'id' => '4',
                'name' => 'LITUS Automobiles',
                'slug' => 'litus-automobiles',
                'tagline' => 'Your trusted automotive partner',
                'description' => 'LITUS Automobiles offers premium vehicle sales and distribution, bringing the worlds leading automotive brands to the Maldives market.',
                'category' => 'Automotive',
                'division' => 'automotive',
                'logo' => 'LITUS Group - logos_Artboard 4 - LITUS Automobiles.png',
                'icon' => 'car',
                'hotline' => '+960 332 2291',
                'email' => 'sales@litusauto.com',
                'services' => [
                    'New Vehicle Sales',
                    'Used Vehicle Sales',
                    'Vehicle Financing',
                    'Trade-In Services',
                    'Vehicle Registration',
                    'After-Sales Support',
                ],
                'strengths' => [
                    'Premium Brands',
                    'Competitive Pricing',
                    'Expert Consultation',
                    'Comprehensive Warranty',
                ],
                'featured' => true,
            ],
            [
                'id' => '5',
                'name' => 'LITUS Service Center',
                'slug' => 'litus-service-center',
                'tagline' => 'Professional automotive care',
                'description' => 'LITUS Service Center provides expert vehicle maintenance and repair services, ensuring your vehicle performs at its best.',
                'category' => 'Automotive Services',
                'division' => 'automotive',
                'logo' => 'LITUS Group - logos_Artboard 5 - LITUS Service Center.png',
                'icon' => 'wrench',
                'hotline' => '+960 332 2292',
                'email' => 'service@litusauto.com',
                'services' => [
                    'Regular Maintenance',
                    'Major Repairs',
                    'Diagnostic Services',
                    'Brake Services',
                    'Engine Services',
                    'Body Work & Paint',
                ],
                'strengths' => [
                    'Certified Technicians',
                    'State-of-the-Art Equipment',
                    'Genuine Parts',
                    'Quality Assurance',
                ],
            ],
            [
                'id' => '6',
                'name' => 'LITUS Parts',
                'slug' => 'litus-parts',
                'tagline' => 'Genuine parts, genuine performance',
                'description' => 'LITUS Parts supplies genuine automotive parts and accessories, ensuring quality and reliability for all your vehicle needs.',
                'category' => 'Automotive Parts',
                'division' => 'automotive',
                'logo' => 'LITUS Group - logos_Artboard 6 - LITUS Parts.png',
                'icon' => 'package',
                'hotline' => '+960 332 2293',
                'email' => 'parts@litusauto.com',
                'services' => [
                    'Genuine Parts Supply',
                    'Accessories',
                    'Performance Parts',
                    'Lubricants & Fluids',
                    'Tires & Batteries',
                    'Custom Orders',
                ],
                'strengths' => [
                    'Authentic Products',
                    'Wide Selection',
                    'Competitive Prices',
                    'Fast Availability',
                ],
                'featured' => true,
            ],
            [
                'id' => '7',
                'name' => 'LITUS General Trading',
                'slug' => 'litus-general-trading',
                'tagline' => 'Your global trading partner',
                'description' => 'LITUS General Trading offers comprehensive import and export trading solutions, connecting businesses with global markets.',
                'category' => 'Trading',
                'division' => 'trading',
                'logo' => 'LITUS Group - logos_Artboard 7 - LITUS General Trading.png',
                'icon' => 'shopping-bag',
                'hotline' => '+960 332 2294',
                'email' => 'trading@litusgroup.com',
                'services' => [
                    'Import Services',
                    'Export Services',
                    'Wholesale Trading',
                    'Retail Distribution',
                    'Product Sourcing',
                    'Market Research',
                ],
                'strengths' => [
                    'Global Network',
                    'Quality Products',
                    'Competitive Rates',
                    'Reliable Service',
                ],
            ],
            [
                'id' => '8',
                'name' => 'LITUS Connect',
                'slug' => 'litus-connect',
                'tagline' => 'Connecting you to the future',
                'description' => 'LITUS Connect delivers advanced connectivity and network solutions, empowering businesses with cutting-edge technology.',
                'category' => 'Technology',
                'division' => 'technology-retail',
                'logo' => 'LITUS Group - logos_Artboard 8 - LITUS Connect.png',
                'icon' => 'network',
                'hotline' => '+960 332 2295',
                'email' => 'connect@litusgroup.com',
                'services' => [
                    'Network Solutions',
                    'Internet Services',
                    'IT Infrastructure',
                    'Cloud Services',
                    'Cybersecurity',
                    'Technical Support',
                ],
                'strengths' => [
                    'Advanced Technology',
                    '24/7 Support',
                    'Reliable Connectivity',
                    'Custom Solutions',
                ],
                'featured' => true,
            ],
            [
                'id' => '9',
                'name' => 'LITUS Connect Office Tech',
                'slug' => 'litus-connect-office-tech',
                'tagline' => 'Empowering your workspace',
                'description' => 'LITUS Connect Office Tech provides advanced office technology and equipment solutions for modern businesses.',
                'category' => 'Office Solutions',
                'division' => 'technology-retail',
                'logo' => 'LITUS Group - logos_Artboard 9 - LITUS Connect Office Tech.png',
                'icon' => 'monitor',
                'hotline' => '+960 332 2296',
                'email' => 'officetech@litusgroup.com',
                'services' => [
                    'Office Equipment',
                    'Printing Solutions',
                    'Audio-Visual Systems',
                    'Communication Tools',
                    'Software Solutions',
                    'Installation & Maintenance',
                ],
                'strengths' => [
                    'Latest Technology',
                    'Professional Installation',
                    'Training & Support',
                    'Cost-Effective Solutions',
                ],
            ],
            [
                'id' => '10',
                'name' => 'LITUS Constructions',
                'slug' => 'litus-constructions',
                'tagline' => 'Building tomorrow, today',
                'description' => 'LITUS Constructions delivers excellence in commercial and residential construction, creating structures that stand the test of time.',
                'category' => 'Construction',
                'division' => 'construction',
                'logo' => 'LITUS Group - logos_Artboard 10 - LITUS Construction.png',
                'icon' => 'hard-hat',
                'hotline' => '+960 332 2297',
                'email' => 'construction@litusgroup.com',
                'services' => [
                    'Commercial Construction',
                    'Residential Construction',
                    'Renovation Services',
                    'Project Management',
                    'Design & Build',
                    'Infrastructure Development',
                ],
                'strengths' => [
                    'Expert Craftsmanship',
                    'Quality Materials',
                    'Timely Completion',
                    'Safety Standards',
                ],
            ],
            [
                'id' => '11',
                'name' => 'Zaha Residence & Hotels',
                'slug' => 'zaha-residence-hotels',
                'tagline' => 'Luxury redefined',
                'description' => 'Zaha Residence & Hotels offers luxury accommodations and residences, providing world-class comfort and exceptional service.',
                'category' => 'Hospitality',
                'division' => 'hospitality-lifestyle',
                'logo' => 'LITUS Group - logos_Artboard 11 - ZAHA Residence & Hotels.png',
                'icon' => 'hotel',
                'hotline' => '+960 332 2298',
                'email' => 'info@zahahotels.com',
                'services' => [
                    'Luxury Accommodations',
                    'Long-Term Residences',
                    'Event Venues',
                    'Spa & Wellness',
                    'Fine Dining',
                    'Business Facilities',
                ],
                'strengths' => [
                    'Premium Locations',
                    'Exceptional Service',
                    'Modern Amenities',
                    'Personalized Experience',
                ],
            ],
            [
                'id' => '12',
                'name' => 'Zaha Travels',
                'slug' => 'zaha-travels',
                'tagline' => 'Your journey, our passion',
                'description' => 'Zaha Travels provides complete travel and tourism services, creating unforgettable experiences for every traveler.',
                'category' => 'Travel & Tourism',
                'division' => 'hospitality-lifestyle',
                'logo' => 'LITUS Group - logos_Artboard 12 - ZAHA Travels.png',
                'icon' => 'plane',
                'hotline' => '+960 332 2299',
                'email' => 'info@zahatravels.com',
                'services' => [
                    'Flight Bookings',
                    'Hotel Reservations',
                    'Tour Packages',
                    'Visa Assistance',
                    'Travel Insurance',
                    'Corporate Travel',
                ],
                'strengths' => [
                    'Competitive Prices',
                    'Expert Guidance',
                    'Custom Packages',
                    '24/7 Support',
                ],
            ],
            [
                'id' => '13',
                'name' => 'Al Zaha General Trading',
                'slug' => 'al-zaha-general-trading',
                'tagline' => 'Quality products, trusted service',
                'description' => 'Al Zaha General Trading specializes in wholesale and retail trading, delivering quality products across diverse categories.',
                'category' => 'Trading',
                'division' => 'trading',
                'logo' => 'LITUS Group - logos_Artboard 12 - ZAHA Travels copy.png',
                'icon' => 'store',
                'hotline' => '+960 332 2300',
                'email' => 'info@alzahatrading.com',
                'services' => [
                    'Wholesale Trading',
                    'Retail Operations',
                    'Product Distribution',
                    'Inventory Management',
                    'Quality Assurance',
                    'Supply Chain Solutions',
                ],
                'strengths' => [
                    'Wide Product Range',
                    'Competitive Pricing',
                    'Reliable Supply',
                    'Quality Products',
                ],
            ],
            [
                'id' => '14',
                'name' => 'Favala Supply',
                'slug' => 'favala-supply',
                'tagline' => 'Supplying excellence',
                'description' => 'Favala Supply provides industrial and commercial supplies, supporting businesses with quality products and reliable service.',
                'category' => 'Supply Chain',
                'division' => 'trading',
                'logo' => 'LITUS Group - logos_Artboard 15 - Favala supply.png',
                'icon' => 'droplet',
                'hotline' => '+960 332 2301',
                'email' => 'info@favalasupply.com',
                'services' => [
                    'Industrial Supplies',
                    'Commercial Products',
                    'Equipment Supply',
                    'Bulk Orders',
                    'Delivery Services',
                    'Technical Support',
                ],
                'strengths' => [
                    'Quality Products',
                    'Fast Delivery',
                    'Competitive Rates',
                    'Expert Service',
                ],
            ],
            [
                'id' => '15',
                'name' => 'Favala Hardware',
                'slug' => 'favala-hardware',
                'tagline' => 'Building your vision',
                'description' => 'Favala Hardware offers quality hardware and building materials, providing everything you need for construction and renovation projects.',
                'category' => 'Hardware',
                'division' => 'trading',
                'logo' => 'LITUS Group - logos_Artboard 14 - Favala hardware.png',
                'icon' => 'hammer',
                'hotline' => '+960 332 2302',
                'email' => 'info@favalahardware.com',
                'services' => [
                    'Building Materials',
                    'Hardware Tools',
                    'Plumbing Supplies',
                    'Electrical Supplies',
                    'Safety Equipment',
                    'Project Consultation',
                ],
                'strengths' => [
                    'Wide Selection',
                    'Quality Brands',
                    'Expert Advice',
                    'Competitive Prices',
                ],
            ],
            [
                'id' => '16',
                'name' => 'Favala Paint',
                'slug' => 'favala-paint',
                'tagline' => 'Colors that inspire',
                'description' => 'Favala Paint delivers professional paint and coating solutions, bringing vibrant colors and lasting protection to every project.',
                'category' => 'Paint & Coatings',
                'division' => 'trading',
                'logo' => 'LITUS Group - logos_Artboard 13 - Favala paint.png',
                'icon' => 'paint-bucket',
                'hotline' => '+960 332 2303',
                'email' => 'info@favalapaint.com',
                'services' => [
                    'Interior Paints',
                    'Exterior Paints',
                    'Specialty Coatings',
                    'Color Consultation',
                    'Surface Preparation',
                    'Application Services',
                ],
                'strengths' => [
                    'Premium Quality',
                    'Color Expertise',
                    'Eco-Friendly Options',
                    'Professional Guidance',
                ],
            ],
        ];
    }

    public static function divisions(): array
    {
        return [
            'logistics-shipping' => [
                'title' => 'Logistics & Shipping',
                'description' => 'Comprehensive logistics and hospitality solutions',
            ],
            'automotive' => [
                'title' => 'Automotive Division',
                'description' => 'Complete automotive sales, service, and parts',
            ],
            'trading' => [
                'title' => 'Trading Division',
                'description' => 'Diverse trading and supply solutions',
            ],
            'construction' => [
                'title' => 'Construction & Development',
                'description' => 'Building excellence in construction',
            ],
            'technology-retail' => [
                'title' => 'Technology & Retail',
                'description' => 'Advanced technology and office solutions',
            ],
            'hospitality-lifestyle' => [
                'title' => 'Hospitality & Lifestyle',
                'description' => 'Luxury hospitality and travel services',
            ],
        ];
    }

    public static function companiesByDivision(string $divisionKey): array
    {
        return array_values(array_filter(self::companies(), fn ($c) => ($c['division'] ?? '') === $divisionKey));
    }

    public static function companyBySlug(string $slug): ?array
    {
        foreach (self::companies() as $company) {
            if (($company['slug'] ?? '') === $slug) {
                return $company;
            }
        }

        return null;
    }

    public static function featuredCompanies(): array
    {
        return array_values(array_filter(self::companies(), fn ($c) => (bool)($c['featured'] ?? false)));
    }

    public static function blogPosts(): array
    {
        static $cached = null;
        if ($cached === null) {
            $cached = require __DIR__ . '/data/blog_posts.php';
        }

        return $cached;
    }

    public static function blogCategories(): array
    {
        return ['All', 'Company News', 'Logistics', 'Hospitality', 'Construction', 'Technology', 'Automotive', 'Retail', 'Team', 'Growth', 'Collaboration'];
    }

    public static function galleryEvents(): array
    {
        return [
            [
                'slug' => 'annual-general-meeting-2026',
                'title' => 'Annual General Meeting 2026',
                'date' => 'March 15, 2026',
                'description' => 'LITUS Group brought together leadership and stakeholders for our annual strategic review and planning session.',
                'image' => 'https://images.unsplash.com/photo-1638799869566-b17fa794c4de?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxtb2Rlcm4lMjBiYXRocm9vbSUyMGludGVyaW9yJTIwbHV4dXJ5fGVufDF8fHx8MTc3NDMyMzk0NXww&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral',
                'image_alt' => 'Luxury bathroom interior',
            ],
            [
                'slug' => 'zaha-hotels-grand-opening',
                'title' => 'Zaha Hotels Grand Opening',
                'date' => 'February 28, 2026',
                'description' => 'Celebrating the grand opening of our newest luxury property in the Maldives with world-class amenities.',
                'image' => 'https://images.unsplash.com/photo-1744974256549-8ece7cdb5dd2?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxtaW5pbWFsaXN0JTIwYmVkcm9vbSUyMHdoaXRlfGVufDF8fHx8MTc3NDM3MTIxNnww&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral',
                'image_alt' => 'Minimalist bedroom design',
            ],
            [
                'slug' => 'leadership-team-building-retreat',
                'title' => 'Leadership Team Building Retreat',
                'date' => 'February 20, 2026',
                'description' => 'An intensive retreat focused on strategic alignment and team cohesion for our executive leadership.',
                'image' => 'https://images.unsplash.com/photo-1667584523543-d1d9cc828a15?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxtb2Rlcm4lMjBsaXZpbmclMjByb29tJTIwaW50ZXJpb3J8ZW58MXx8fHwxNzc0MzQwNDg1fDA&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral',
                'image_alt' => 'Modern living room',
            ],
            [
                'slug' => 'excellence-award-ceremony-2026',
                'title' => 'Excellence Award Ceremony 2026',
                'date' => 'February 10, 2026',
                'description' => 'Honoring outstanding achievements and contributions across all LITUS Group divisions.',
                'image' => 'https://images.unsplash.com/photo-1759223198981-661cadbbff36?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxsdXh1cnklMjBob3RlbCUyMHN1aXRlJTIwcm9vbXxlbnwxfHx8fDE3NzQ0MzQ2MDJ8MA&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral',
                'image_alt' => 'Hotel suite interior',
            ],
            [
                'slug' => 'new-headquarters-inauguration',
                'title' => 'New Headquarters Inauguration',
                'date' => 'January 25, 2026',
                'description' => 'Opening our state-of-the-art headquarters facility, marking a new chapter in our company history.',
                'image' => 'https://images.unsplash.com/photo-1622131815452-cc00d8d89f02?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxtb2Rlcm4lMjBvZmZpY2UlMjB3b3Jrc3BhY2UlMjBpbnRlcmlvcnxlbnwxfHx8fDE3NzQzODcyNTF8MA&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral',
                'image_alt' => 'Modern workspace',
            ],
            [
                'slug' => 'favala-product-launch-event',
                'title' => 'Favala Product Launch Event',
                'date' => 'January 15, 2026',
                'description' => 'Introducing our latest line of premium hardware and construction materials to the market.',
                'image' => 'https://images.unsplash.com/photo-1680210849773-f97a41c6b7ed?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxjb250ZW1wb3JhcnklMjBraXRjaGVuJTIwZGVzaWdufGVufDF8fHx8MTc3NDQzODU3N3ww&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral',
                'image_alt' => 'Contemporary kitchen',
            ],
            [
                'slug' => 'international-trade-show-2026',
                'title' => 'International Trade Show 2026',
                'date' => 'January 5, 2026',
                'description' => "Showcasing LITUS Group's diverse portfolio at the premier international trade exhibition.",
                'image' => 'https://images.unsplash.com/photo-1771033834141-023d630b3965?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxtb2Rlcm4lMjByZXRhaWwlMjBzdG9yZSUyMGludGVyaW9yfGVufDF8fHx8MTc3NDQyMDgxN3ww&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral',
                'image_alt' => 'Retail store interior',
            ],
            [
                'slug' => '25th-anniversary-celebration',
                'title' => '25th Anniversary Celebration',
                'date' => 'December 20, 2025',
                'description' => 'A milestone celebration honoring 25 years of excellence, growth, and innovation.',
                'image' => 'https://images.unsplash.com/photo-1769773297747-bd00e31b33aa?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxlbGVnYW50JTIwcmVzdGF1cmFudCUyMGRpbmluZ3xlbnwxfHx8fDE3NzQ0Mzg1ODN8MA&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral',
                'image_alt' => 'Restaurant dining area',
            ],
        ];
    }

    public static function careerOpenings(): array
    {
        return [
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
    }

    public static function team(): array
    {
        $image = 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?auto=format&fit=crop&w=800&q=80';

        return [
            [
                'name' => 'Mohamed Zahid',
                'role' => 'Chief Executive Officer',
                'bio' => "With exceptional vision and strategic leadership, Mohamed Zahid guides LITUS Group's overall direction and growth across all business divisions. His expertise in corporate governance and business development has positioned LITUS Group as a prominent conglomerate, driving innovation and excellence across 16 diverse subsidiaries.",
                'expertise' => 'Strategic Leadership, Corporate Governance, Business Growth',
                'image' => $image,
            ],
            [
                'name' => 'Chamara Madusanka',
                'role' => 'Chief Business Development Officer',
                'bio' => "Chamara spearheads LITUS Group's business development initiatives, identifying new opportunities and forging strategic partnerships across multiple sectors. His innovative approach to market expansion and relationship building has been instrumental in the company's continuous growth and diversification.",
                'expertise' => 'Business Development, Strategic Partnerships, Market Expansion',
                'image' => $image,
            ],
            [
                'name' => 'Ahmed Zahir',
                'role' => 'General Manager, LITUS Automobiles',
                'bio' => 'Ahmed leads LITUS Automobiles with deep industry expertise and a customer-centric approach. His operational excellence and market knowledge have established LITUS Automobiles as a trusted name in the automotive sector, delivering premium brands and exceptional service to customers.',
                'expertise' => 'Automotive Management, Operations Excellence, Customer Relations',
                'image' => $image,
            ],
            [
                'name' => 'Asif Rasheed',
                'role' => 'Director - Sales & Marketing',
                'bio' => "Asif drives LITUS Group's sales and marketing strategies with creativity and market insight. His comprehensive approach to brand building and customer engagement has elevated the group's market presence, ensuring consistent growth and strong brand recognition across all business divisions.",
                'expertise' => 'Sales Strategy, Brand Marketing, Customer Engagement',
                'image' => $image,
            ],
        ];
    }
}

