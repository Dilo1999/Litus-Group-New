<?php

namespace App\Filament\Forms\Components;

use Filament\Forms;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

class SeoFields
{
    /**
     * Basic SEO fields for Filament (search snippets). Open Graph and Twitter tags
     * are filled automatically in {@see \App\Services\SeoService} from meta fields,
     * titles, excerpts, and cover images.
     */
    public static function section(): Forms\Components\Section
    {
        return Forms\Components\Section::make('Search engine optimization')
            ->description('Controls the page title and search-result snippet. Social previews use this text plus your main/cover image when available.')
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
            ])
            ->columns(1)
            ->collapsible()
            ->collapsed(false);
    }
}
