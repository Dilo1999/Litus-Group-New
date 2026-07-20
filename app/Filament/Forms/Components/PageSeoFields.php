<?php

namespace App\Filament\Forms\Components;

use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

class PageSeoFields
{
    /**
     * Advanced SEO fields for static pages managed via Page SEO.
     * These map 1:1 to the PageSeo model and are consumed by SeoService::applyForPage().
     */
    public static function section(string $imageDirectory = 'site/seo/pages'): Forms\Components\Section
    {
        return Forms\Components\Section::make('Search engine optimization')
            ->description('Controls the page title, search-result snippet, and how this page appears when shared on social networks. Leave fields empty to fall back to Global SEO defaults.')
            ->schema([
                TextInput::make('meta_title')
                    ->label('Meta title')
                    ->maxLength(70)
                    ->helperText('Recommended: ≤60 characters. Shown in browser tab and search results.'),
                Textarea::make('meta_description')
                    ->label('Meta description')
                    ->rows(3)
                    ->maxLength(320)
                    ->helperText('Recommended: 150–160 characters. Shown as the snippet in search results.'),
                TextInput::make('og_title')
                    ->label('Social title (Open Graph)')
                    ->maxLength(95)
                    ->helperText('Defaults to meta title if empty. Used by Facebook, LinkedIn, etc.'),
                Textarea::make('og_description')
                    ->label('Social description (Open Graph)')
                    ->rows(2)
                    ->maxLength(300)
                    ->helperText('Defaults to meta description if empty.'),
                FileUpload::make('og_image')
                    ->label('Social / Open Graph image')
                    ->disk('public')
                    ->directory($imageDirectory)
                    ->visibility('public')
                    ->image()
                    ->imagePreviewHeight('160')
                    ->maxSize(4096)
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->helperText('Recommended: 1200×630px. Leave empty to use the Global SEO default image.'),
                TextInput::make('twitter_title')
                    ->label('Twitter / X title')
                    ->maxLength(70),
                Textarea::make('twitter_description')
                    ->label('Twitter / X description')
                    ->rows(2)
                    ->maxLength(300),
                FileUpload::make('twitter_image')
                    ->label('Twitter / X image')
                    ->disk('public')
                    ->directory($imageDirectory.'/twitter')
                    ->visibility('public')
                    ->image()
                    ->imagePreviewHeight('160')
                    ->maxSize(4096)
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->helperText('Optional. Defaults to the Open Graph image above if empty.'),
                TextInput::make('canonical_url')
                    ->label('Canonical URL')
                    ->url()
                    ->maxLength(2048)
                    ->helperText('Optional. Only set if the preferred URL differs (e.g. trailing slash, www).'),
                TextInput::make('robots')
                    ->label('Robots')
                    ->maxLength(120)
                    ->helperText('Examples: index,follow | noindex,nofollow. Leave blank to use Global SEO / site defaults.'),
            ])
            ->columns(1)
            ->collapsible()
            ->collapsed(false);
    }
}
