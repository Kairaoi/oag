<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLegalTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('legal_tasks', function (Blueprint $table) {
            $table->id(); // Auto-incrementing ID
            $table->date('date');
            $table->text('task');
            $table->string('ministry');
            $table->string('legal_advice_meeting');
            $table->date('allocated_date')->nullable();
            $table->foreignId('allocated_to')->nullable()->constrained('users')->nullOnDelete(); // Changed this line
            $table->string('status')->nullable();
            $table->text('onward_action')->nullable();
            $table->date('date_task_achieved')->nullable();
            $table->date('date_approved_by_ag')->nullable();
            $table->date('meeting_date')->nullable();
            $table->string('time_frame')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps(); // Creates created_at & updated_at columns
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('legal_tasks');
    }
}