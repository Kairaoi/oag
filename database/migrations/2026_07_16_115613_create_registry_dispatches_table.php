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
        Schema::create('registry_dispatches', function (Blueprint $table) {
            $table->id();

            $table->foreignId('case_id')->constrained('cases')->onDelete('cascade');
            $table->foreignId('dispatched_by')->constrained('users');
            $table->date('date_dispatched');
            $table->string('dispatched_to')->default('High Court Registry');

            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->softDeletes();
            $table->timestamps();

            $table->index(['case_id', 'date_dispatched']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registry_dispatches');
    }
};
