<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Create ministries table
        Schema::create('ministries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Create counsels table
        Schema::create('counsels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('position', ['DLD', 'Senior Counsel', 'Junior Counsel', 'AG']);
            $table->boolean('is_active')->default(true);
            $table->integer('max_assignments')->default(5);
            $table->timestamps();
        });

        // Create bills table
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('receipt_date');
            $table->foreignId('ministry_id')->constrained();
            $table->enum('status', ['Draft', 'First Reading', 'Second Reading', 'Third Reading', 'Passed', 'Rejected'])
                  ->default('Draft');
            $table->enum('priority', ['Normal', 'Urgent', 'High Priority'])->default('Normal');
            $table->string('task');
            $table->enum('progress_status', ['Not Started', 'Ongoing', 'Achieved'])->default('Not Started');
            $table->text('comments')->nullable();
            $table->date('target_completion_date')->nullable();
            $table->date('actual_completion_date')->nullable();
            $table->string('version')->default('1.0');
            $table->timestamps();
            $table->softDeletes();
            
            // Add a simple unique constraint on name
            $table->unique('name');
        });

        // After creating the bills table, add the generated column
        DB::statement('ALTER TABLE bills ADD COLUMN receipt_year INT AS (YEAR(receipt_date)) STORED');
        DB::statement('CREATE INDEX year_receipt_date_idx ON bills(receipt_year)');

        // Create regulations table
        Schema::create('regulations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('receipt_date');
            $table->foreignId('ministry_id')->constrained();
            $table->enum('status', ['Pending', 'In Review', 'Approved', 'Published', 'Rejected'])
                  ->default('Pending');
            $table->enum('priority', ['Normal', 'Urgent', 'High Priority'])->default('Normal');
            $table->text('comments')->nullable();
            $table->date('target_completion_date')->nullable();
            $table->date('actual_completion_date')->nullable();
            $table->string('version')->default('1.0');
            $table->boolean('requires_cabinet_approval')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        // Create bill_counsel pivot table
        Schema::create('bill_counsel', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bill_id')->constrained()->onDelete('cascade');
            $table->foreignId('counsel_id')->constrained()->onDelete('cascade');
            $table->date('assigned_date');
            $table->date('due_date')->nullable();
            $table->enum('role', ['Lead', 'Support', 'Review'])->default('Support');
            $table->timestamps();
            $table->unique(['bill_id', 'counsel_id']);
        });

        // Create regulation_counsel pivot table
        Schema::create('regulation_counsel', function (Blueprint $table) {
            $table->id();
            $table->foreignId('regulation_id')->constrained()->onDelete('cascade');
            $table->foreignId('counsel_id')->constrained()->onDelete('cascade');
            $table->date('assigned_date');
            $table->date('due_date')->nullable();
            $table->enum('role', ['Lead', 'Support', 'Review'])->default('Support');
            $table->timestamps();
            $table->unique(['regulation_id', 'counsel_id']);
        });

        // Create bill_history table
        Schema::create('bill_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bill_id')->constrained()->onDelete('cascade');
            $table->string('action');
            $table->string('changed_by');
            $table->text('details');
            $table->timestamps();
        });

        // Create regulation_history table
        Schema::create('regulation_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('regulation_id')->constrained()->onDelete('cascade');
            $table->string('action');
            $table->string('changed_by');
            $table->text('details');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('regulation_history');
        Schema::dropIfExists('bill_history');
        Schema::dropIfExists('regulation_counsel');
        Schema::dropIfExists('bill_counsel');
        Schema::dropIfExists('regulations');
        Schema::dropIfExists('bills');
        Schema::dropIfExists('counsels');
        Schema::dropIfExists('ministries');
    }
};