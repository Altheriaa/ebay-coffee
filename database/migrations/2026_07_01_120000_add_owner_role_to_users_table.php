<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Ubah enum role untuk menambahkan 'owner'
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'pelanggan', 'owner') DEFAULT 'pelanggan'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'pelanggan') DEFAULT 'pelanggan'");
    }
};
