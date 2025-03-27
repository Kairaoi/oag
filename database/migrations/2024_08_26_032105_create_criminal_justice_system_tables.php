<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCriminalJusticeSystemTables extends Migration
{
    public function up()
    {
        // Create OffenceCategories Table
        Schema::create('offence_categories', function (Blueprint $table) {
            $table->id();
            $table->string('category_name');
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->softDeletes();
            $table->timestamps();
        });

        // Create Islands Table
        Schema::create('islands', function (Blueprint $table) {
            $table->id();
            $table->string('island_name');
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->softDeletes();
            $table->timestamps();
        });

        // Create ReasonsForClosure Table
        Schema::create('reasons_for_closure', function (Blueprint $table) {
            $table->id();
            $table->text('reason_description');
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->softDeletes();
            $table->timestamps();
        });

        // Create Cases Table
        Schema::create('cases', function (Blueprint $table) {
            $table->id();
            $table->string('case_file_number')->unique();
            $table->date('date_file_received');
            $table->string('case_name');
            $table->date('date_of_allocation')->nullable();
            $table->date('date_file_closed')->nullable();
            $table->foreignId('reason_for_closure_id')->nullable()->constrained('reasons_for_closure');
            $table->foreignId('lawyer_id')->constrained('users'); // Replaces council_id with lawyer_id
            $table->foreignId('island_id')->constrained('islands');
            $table->string('court_case_number')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('case_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('case_id')->constrained('cases')->onDelete('cascade');
            // Evidence status tracking
            $table->enum('evidence_status', [
                'pending_review',
                'sufficient_evidence',
                'insufficient_evidence',
                'returned_to_police'
            ])->default('pending_review');
        
            // Review details
            $table->text('review_notes')->nullable(); // Made nullable for flexibility
            $table->datetime('review_date');
        
            // Action tracking
            $table->string('action_type')->nullable(); // Track review, reassignment, or court update
            $table->foreignId('new_lawyer_id')->nullable()->constrained('users'); // Store reassigned lawyer
            $table->text('reallocation_reason')->nullable(); // Store reason for reassignment
            $table->string('court_case_number')->nullable(); // Store updated court case number
        
            // Record tracking
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->softDeletes();
            $table->timestamps();
        
            // Performance indexing
            $table->index('case_id');
            // $table->index('lawyer_id');
            $table->index('evidence_status');
            $table->index('review_date');
        });
        

        Schema::create('case_reallocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('case_id')->constrained('cases');
            $table->foreignId('from_lawyer_id')->constrained('users');
            $table->foreignId('to_lawyer_id')->constrained('users');
            $table->text('reallocation_reason');
            $table->date('reallocation_date');
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
        });

        // Create Accused Table
        Schema::create('accused', function (Blueprint $table) {
            $table->id();
            $table->foreignId('case_id')->constrained('cases');
           
          
            $table->string('first_name');
            $table->string('last_name');
            $table->text('accused_particulars');
            $table->enum('gender', ['Male', 'Female', 'Other']);
            $table->date('date_of_birth');
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->softDeletes();
            $table->timestamps();
        });

        // Create Victims Table
        Schema::create('victims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('case_id')->constrained('cases');
            $table->foreignId('lawyer_id')->constrained('users'); // Replaces council_id with lawyer_id
            $table->foreignId('island_id')->constrained('islands');
            $table->string('first_name');
            $table->string('last_name');
            $table->text('victim_particulars');
            $table->enum('gender', ['Male', 'Female', 'Other']);
            $table->date('date_of_birth');
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->softDeletes();
            $table->timestamps();
        });

        // Create Offences Table
        Schema::create('offences', function (Blueprint $table) {
            $table->id();
            $table->string('offence_name');
            $table->foreignId('offence_category_id')->constrained('offence_categories');
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->softDeletes();
            $table->timestamps();
        });

        // Create Incidents Table
        Schema::create('incidents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('case_id')->constrained('cases');
            $table->foreignId('lawyer_id')->constrained('users'); // Replaces council_id with lawyer_id
            $table->foreignId('island_id')->constrained('islands');
            $table->date('date_of_incident_start')->nullable();
            $table->date('date_of_incident_end')->nullable();
            $table->string('place_of_incident');
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->softDeletes();
            $table->timestamps();
        });

        // Create AccusedOffence Table
        Schema::create('accused_offence', function (Blueprint $table) {
            $table->id();
            $table->foreignId('accused_id')->constrained('accused')->onDelete('cascade');
            $table->foreignId('offence_id')->constrained('offences')->onDelete('cascade');
            $table->timestamps();
        });

        // Create ReportGroups Table
        Schema::create('report_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->timestamps();
            $table->softDeletes(); // Soft delete column
        });

        // Create Reports Table
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_group_id')->constrained('report_groups')->onDelete('cascade');
            $table->string('name');
            $table->text('description');
            $table->longText('query'); // Use longText for SQL queries
            $table->timestamps();
            $table->softDeletes(); // Soft delete column
        });
    }

    public function down()
    {
        
       
        Schema::dropIfExists('reports');
        Schema::dropIfExists('report_groups');
        Schema::dropIfExists('accused_offence');
        Schema::dropIfExists('incidents');
        Schema::dropIfExists('offences');
        Schema::dropIfExists('victims');
        Schema::dropIfExists('accused');
        Schema::dropIfExists('case_reallocations');
        Schema::dropIfExists('case_reviews');
        Schema::dropIfExists('cases');
        Schema::dropIfExists('reasons_for_closure');
        Schema::dropIfExists('islands');
        Schema::dropIfExists('offence_categories');
    }
}
