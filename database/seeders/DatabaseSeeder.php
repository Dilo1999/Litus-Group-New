<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(SuperAdminSeeder::class);
        $this->call(CompaniesSeeder::class);
        $this->call(TeamMembersSeeder::class);
        $this->call(JobOpeningsSeeder::class);
        $this->call(BlogPostSeeder::class);
        $this->call(GalleryEventSeeder::class);
        $this->call(PageSeoSeeder::class);
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
