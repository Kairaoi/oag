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
        Schema::create('ag_reviews', function (Blueprint $table) {
            $table->id();

            // Reference to criminal case. Not unique — a rejected review can
            // be revised and resubmitted, so a case can have multiple rows
            // (the latest one is "current").
            $table->foreignId('case_id')->constrained('cases')->onDelete('cascade');

            $table->foreignId('submitted_by')->constrained('users');
            $table->date('submitted_at');

            $table->enum('ag_decision', ['pending', 'approved', 'rejected'])->default('pending');
            $table->date('decision_date')->nullable();
            $table->text('ag_comments')->nullable();

            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->softDeletes();
            $table->timestamps();

            $table->index(['case_id', 'submitted_at']);
            $table->index('ag_decision');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ag_reviews');
    }
};
