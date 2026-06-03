<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')
            ->whereIn('email', ['admin@gmail.com', 'superadmin@gmail.com'])
            ->update(['role' => 'superadmin']);
    }

    public function down(): void
    {
        DB::table('users')
            ->where('role', 'superadmin')
            ->update(['role' => 'admin']);
    }
};
