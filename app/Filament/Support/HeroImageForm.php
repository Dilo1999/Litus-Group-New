<?php

namespace App\Filament\Support;

use Filament\Forms;

class HeroImageForm
{
    /**
     * Hero image upload + vertical position control.
     *
     * @return array<int, Forms\Components\Component>
     */
    public static function fields(string $uploadDirectory): array
    {
        return [
            Forms\Components\FileUpload::make('hero_image_path')
                ->label('Hero image')
                ->disk('public')
                ->directory($uploadDirectory)
                ->visibility('public')
                ->preserveFilenames()
                ->image()
                ->imageCropAspectRatio('16:9')
                ->imageResizeTargetWidth(1920)
                ->imageResizeTargetHeight(1080)
                ->imageResizeMode('cover')
                ->imagePreviewHeight('180')
                ->maxSize(4096)
                ->reactive()
                ->helperText('Crop/zoom to fit the hero. Output: 1920×1080 (16:9).'),
            Forms\Components\TextInput::make('hero_image_position_y')
                ->label('Hero image vertical position (%)')
                ->numeric()
                ->minValue(0)
                ->maxValue(100)
                ->default(50)
                ->reactive()
                ->helperText('0 = show more top, 50 = center, 100 = show more bottom.'),
        ];
    }

    public static function section(string $uploadDirectory, string $description): Forms\Components\Section
    {
        return Forms\Components\Section::make('Hero image')
            ->description($description)
            ->schema(static::fields($uploadDirectory))
            ->columns(1);
    }
}
