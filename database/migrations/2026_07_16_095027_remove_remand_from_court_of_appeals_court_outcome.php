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
        DB::statement("ALTER TABLE court_of_appeals MODIFY court_outcome ENUM('win', 'lose') NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE court_of_appeals MODIFY court_outcome ENUM('win', 'lose', 'remand') NULL");
    }
};
