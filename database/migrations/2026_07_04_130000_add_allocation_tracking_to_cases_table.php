<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * date_of_allocation already appears (unused) in the case edit/show views
     * and ReportSeeder, referencing a column that was never actually added to
     * the table — this adds it for real, plus allocated_by to record who
     * performed the allocation.
     */
    public function up(): void
    {
        Schema::table('cases', function (Blueprint $table) {
            $table->date('date_of_allocation')->nullable()->after('lawyer_id');
            $table->unsignedBigInteger('allocated_by')->nullable()->after('date_of_allocation');
        });
    }

    public function down(): void
    {
        Schema::table('cases', function (Blueprint $table) {
            $table->dropColumn(['date_of_allocation', 'allocated_by']);
        });
    }
};
