<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('accused', function (Blueprint $table) {
            $table->text('accused_particulars')->nullable()->after('last_name');
            $table->string('custom_offence')->nullable()->after('island_id');
        });

        Schema::table('appeal_details', function (Blueprint $table) {
            $table->date('appeal_filing_received_date')->nullable()->after('appeal_filing_date');
            $table->enum('appeal_status', ['pending', 'in_progress', 'decided', 'withdrawn'])->nullable()->after('appeal_filing_received_date');
            $table->text('appeal_grounds')->nullable()->after('appeal_status');
            $table->text('appeal_decision')->nullable()->after('appeal_grounds');
            $table->date('appeal_decision_date')->nullable()->after('appeal_decision');
        });

        Schema::table('cases', function (Blueprint $table) {
            $table->boolean('is_appeal_case')->default(false)->after('status');
            $table->boolean('is_on_appeal')->default(false)->after('is_appeal_case');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('accused', function (Blueprint $table) {
            $table->dropColumn(['accused_particulars', 'custom_offence']);
        });

        Schema::table('appeal_details', function (Blueprint $table) {
            $table->dropColumn(['appeal_filing_received_date', 'appeal_status', 'appeal_grounds', 'appeal_decision', 'appeal_decision_date']);
        });

        Schema::table('cases', function (Blueprint $table) {
            $table->dropColumn(['is_appeal_case', 'is_on_appeal']);
        });
    }
};
