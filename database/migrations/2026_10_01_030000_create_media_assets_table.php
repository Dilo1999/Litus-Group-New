<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Shared image library ("Assets" in the admin). Existing images on the public disk are
 * registered with: php artisan media-assets:sync (or "Scan storage" on the Assets page).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media_assets', function (Blueprint $table) {
            $table->id();
            $table->text('name')->nullable();
            // TEXT: Filament / Livewire upload names can exceed VARCHAR(191).
            $table->text('path');
            $table->string('disk')->default('public');
            $table->string('mime_type', 100)->nullable();
            $table->unsignedBigInteger('size')->nullable();
            $table->timestamps();

            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media_assets');
    }
};
