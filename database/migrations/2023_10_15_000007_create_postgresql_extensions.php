<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Enable PostgreSQL extensions
        DB::statement('CREATE EXTENSION IF NOT EXISTS "uuid-ossp"');
        DB::statement('CREATE EXTENSION IF NOT EXISTS "pg_trgm"'); // For text search
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Disable PostgreSQL extensions
        DB::statement('DROP EXTENSION IF EXISTS "uuid-ossp"');
        DB::statement('DROP EXTENSION IF EXISTS "pg_trgm"');
    }
};