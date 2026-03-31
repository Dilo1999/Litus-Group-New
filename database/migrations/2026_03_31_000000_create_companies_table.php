<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('tagline')->nullable();
            $table->text('description')->nullable();
            $table->string('category')->nullable();
            $table->string('division')->nullable()->index();
            $table->string('logo')->nullable();
            $table->string('icon')->default('building2');
            $table->string('hotline')->nullable();
            $table->string('email')->nullable();
            $table->json('services')->nullable();
            $table->json('strengths')->nullable();
            $table->boolean('featured')->default(false);
            $table->unsignedSmallInteger('sort_order')->default(0)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
