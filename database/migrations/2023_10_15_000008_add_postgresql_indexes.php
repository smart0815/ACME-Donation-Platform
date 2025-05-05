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
        // Add GIN index for full-text search on campaigns
        DB::statement('CREATE INDEX campaign_search_idx ON campaigns USING GIN(to_tsvector(\'english\', title || \' \' || description))');
        
        // Add index for campaign status (frequently queried)
        Schema::table('campaigns', function (Blueprint $table) {
            $table->index('status');
            $table->index('end_date');
            $table->index('featured');
        });
        
        // Add index for donations
        Schema::table('donations', function (Blueprint $table) {
            $table->index(['campaign_id', 'created_at']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove GIN index
        DB::statement('DROP INDEX IF EXISTS campaign_search_idx');
        
        // Remove other indexes
        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['end_date']);
            $table->dropIndex(['featured']);
        });
        
        Schema::table('donations', function (Blueprint $table) {
            $table->dropIndex(['campaign_id', 'created_at']);
            $table->dropIndex(['status']);
        });
    }
};