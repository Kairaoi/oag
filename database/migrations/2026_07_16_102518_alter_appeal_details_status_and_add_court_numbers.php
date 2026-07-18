<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE appeal_details MODIFY appeal_status ENUM('pending', 'appealed', 'dismissed', 'withdrawn') NULL");

        Schema::table('appeal_details', function (Blueprint $table) {
            $table->string('high_court_case_number')->nullable()->after('case_id');
            $table->string('magistrate_court_case_number')->nullable()->after('high_court_case_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appeal_details', function (Blueprint $table) {
            $table->dropColumn(['high_court_case_number', 'magistrate_court_case_number']);
        });

        DB::statement("ALTER TABLE appeal_details MODIFY appeal_status ENUM('pending', 'in_progress', 'decided', 'withdrawn') NULL");
    }
};
