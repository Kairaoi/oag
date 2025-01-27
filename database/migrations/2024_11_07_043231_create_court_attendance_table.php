<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourtAttendanceTable extends Migration
{
    public function up()
    {
        Schema::create('court_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('civil_case_id')->constrained('civil_cases');
            
            // Opposing counsel details
          
            $table->foreignId('opposing_counsel_name')->constrained('users');
            // Hearing details
            $table->date('hearing_date');
            $table->string('hearing_type')->nullable(); // For mention, hearing, etc.
            $table->time('hearing_time')->nullable();
            
            // Status
            $table->enum('case_status', ['Concluded', 'Ongoing', 'Adjourned', 'Other']);
            $table->text('status_notes')->nullable(); // For additional status details
            
            // Standard fields
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('court_attendances');
    }
}
