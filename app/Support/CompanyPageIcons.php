<?php

namespace App\Support;

/**
 * Maps service/strength labels to lucide icon names.
 */
class CompanyPageIcons
{
    public static function serviceIcon(string $serviceName): string
    {
        $s = strtolower($serviceName);

        if (str_contains($s, 'genuine parts') || str_contains($s, 'spare parts')) return 'package';
        if (str_contains($s, 'performance parts') || str_contains($s, 'performance')) return 'zap';
        if (str_contains($s, 'accessories')) return 'shopping-bag';
        if (str_contains($s, 'lubricant') || str_contains($s, 'fluid') || str_contains($s, 'oil')) return 'droplet';
        if (str_contains($s, 'tire') || str_contains($s, 'tyre')) return 'gauge';
        if (str_contains($s, 'batter')) return 'battery';
        if (str_contains($s, 'custom order') || str_contains($s, 'customization')) return 'settings';
        if (str_contains($s, 'maintenance') || str_contains($s, 'repair')) return 'wrench';
        if (str_contains($s, 'installation')) return 'hammer';
        if (str_contains($s, 'diagnostic') || str_contains($s, 'inspection')) return 'cog';
        if (str_contains($s, 'consulting') || str_contains($s, 'consultation')) return 'briefcase';
        if (str_contains($s, 'technical support') || str_contains($s, 'support')) return 'headphones';
        if (str_contains($s, 'training')) return 'award';
        if (str_contains($s, 'delivery') || str_contains($s, 'shipping')) return 'truck';
        if (str_contains($s, 'investment') || str_contains($s, 'financing')) return 'wallet';
        if (str_contains($s, 'trading') || str_contains($s, 'import') || str_contains($s, 'export')) return 'globe';
        if (str_contains($s, 'wholesale') || str_contains($s, 'distribution')) return 'warehouse';
        if (str_contains($s, 'retail') || str_contains($s, 'sales')) return 'shopping-cart';
        if (str_contains($s, 'supply chain') || str_contains($s, 'logistics')) return 'container';
        if (str_contains($s, 'construction') || str_contains($s, 'building')) return 'building2';
        if (str_contains($s, 'contracting')) return 'file-text';
        if (str_contains($s, 'manufacturing')) return 'layers';
        if (str_contains($s, 'painting') || str_contains($s, 'coating')) return 'paint-bucket';
        if (str_contains($s, 'design')) return 'sparkles';
        if (str_contains($s, 'real estate') || str_contains($s, 'property')) return 'house';
        if (str_contains($s, 'development')) return 'trending-up';
        if (str_contains($s, 'quality') || str_contains($s, 'assurance')) return 'shield';
        if (str_contains($s, 'warranty')) return 'circle-check';
        if (str_contains($s, 'analysis') || str_contains($s, 'analytics')) return 'chart-column';
        if (str_contains($s, 'management')) return 'target';

        return 'box';
    }

    public static function strengthIcon(string $strengthName): string
    {
        $s = strtolower($strengthName);

        if (str_contains($s, 'premium') || str_contains($s, 'quality') || str_contains($s, 'brand')) return 'award';
        if (str_contains($s, 'genuine') || str_contains($s, 'authentic') || str_contains($s, 'certified')) return 'shield';
        if (str_contains($s, 'warranty') || str_contains($s, 'guarantee')) return 'circle-check';
        if (str_contains($s, 'competitive') || str_contains($s, 'pricing') || str_contains($s, 'price')) return 'trending-up';
        if (str_contains($s, 'value') || str_contains($s, 'affordable')) return 'wallet';
        if (str_contains($s, 'expert') || str_contains($s, 'consultation') || str_contains($s, 'professional')) return 'headphones';
        if (str_contains($s, 'customer service') || str_contains($s, 'support')) return 'heart-handshake';
        if (str_contains($s, 'experience') || str_contains($s, 'expertise')) return 'briefcase';
        if (str_contains($s, 'fast') || str_contains($s, 'quick') || str_contains($s, 'rapid')) return 'zap';
        if (str_contains($s, 'delivery') || str_contains($s, 'shipping')) return 'truck';
        if (str_contains($s, '24/7') || str_contains($s, 'available')) return 'clock';
        if (str_contains($s, 'trust') || str_contains($s, 'reliable') || str_contains($s, 'dependable')) return 'star';
        if (str_contains($s, 'comprehensive') || str_contains($s, 'complete') || str_contains($s, 'full')) return 'layers';
        if (str_contains($s, 'team') || str_contains($s, 'staff') || str_contains($s, 'people')) return 'users';
        if (str_contains($s, 'innovative') || str_contains($s, 'modern') || str_contains($s, 'technology')) return 'sparkles';
        if (str_contains($s, 'wide range') || str_contains($s, 'variety') || str_contains($s, 'selection')) return 'package';

        return 'target';
    }
}

