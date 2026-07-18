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
        Schema::table('ag_reviews', function (Blueprint $table) {
            $table->text('submission_notes')->nullable()->after('submitted_at');
        });

        // ag_comments previously did double duty as the lawyer's submission
        // note (set by store()) until the AG overwrote it with their own
        // decision comments (set by update()) — backfill existing rows so
        // that history isn't lost now that the two are separate columns.
        DB::table('ag_reviews')->whereNotNull('ag_comments')->update([
            'submission_notes' => DB::raw('ag_comments'),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ag_reviews', function (Blueprint $table) {
            $table->dropColumn('submission_notes');
        });
    }
};
