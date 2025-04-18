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

        // Create Courts of Appeal Table
        Schema::create('courts_of_appeal', function (Blueprint $table) {
            $table->id();
            $table->string('court_name');
            $table->text('description')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->softDeletes();
            $table->timestamps();
        });

        // Create Cases Table - Modified to include appeal-related fields
        Schema::create('cases', function (Blueprint $table) {
            $table->id();
            $table->string('case_file_number')->unique();
            $table->date('date_file_received');
            $table->string('case_name');
            $table->date('date_of_allocation')->nullable();
            $table->foreignId('lawyer_id')->constrained('users');
            $table->foreignId('island_id')->constrained('islands');
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            // New fields for review status
            $table->enum('status', ['pending', 'accepted', 'rejected', 'reallocate'])->default('pending');
            $table->foreignId('reviewer_id')->nullable()->constrained('users');
            $table->timestamp('reviewed_at')->nullable();
            $table->text('rejection_reason')->nullable(); // Optional: To store reason for rejection
            $table->softDeletes();
            $table->timestamps();
        });
        // Create CourtHearings Table - ADD HERE
        Schema::create('court_hearings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('case_id')->constrained('cases')->onDelete('cascade');
            $table->date('hearing_date');
            $table->string('hearing_type'); 
            $table->text('hearing_notes')->nullable();
            $table->boolean('is_completed')->default(false);
            
            // Verdict fields
            $table->boolean('has_verdict')->default(false);
            $table->enum('verdict', ['guilty', 'not_guilty', 'dismissed', 'withdrawn', 'other'])->nullable();
            $table->text('verdict_details')->nullable();
            $table->date('verdict_date')->nullable();
            $table->text('sentencing_details')->nullable();
            
            // Record tracking
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->softDeletes();
            $table->timestamps();
            
            // Performance indexing
            $table->index(['case_id', 'hearing_date']);
            $table->index('has_verdict');
        });

        // Create Appeal Details Table
        Schema::create('appeal_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('case_id')->constrained('cases')->onDelete('cascade');
            $table->string('appeal_case_number')->nullable();
            $table->date('appeal_filing_date')->nullable();
            $table->enum('appeal_status', ['pending', 'in_progress', 'decided', 'withdrawn'])->default('pending');
            $table->text('appeal_grounds')->nullable();
            $table->text('appeal_decision')->nullable();
            $table->date('appeal_decision_date')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('case_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('case_id')->constrained('cases')->onDelete('cascade');
        
            // Review tracking
            $table->enum('evidence_status', [
                'pending_review',
                'sufficient_evidence',
                'insufficient_evidence',
                'returned_to_police'
            ])->default('pending_review');
            $table->text('review_notes')->nullable();
            $table->datetime('review_date');
        
            // Case closure fields moved here
            $table->date('date_file_closed')->nullable();
            $table->foreignId('reason_for_closure_id')->nullable()->constrained('reasons_for_closure');
        
            // Lawyer reassignment (new addition)
            $table->foreignId('new_lawyer_id')->nullable()->constrained('users');
        
            // Auditing
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->softDeletes();
            $table->timestamps();
        
            $table->index('case_id');
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
            $table->text('address')->nullable();
            $table->text('contact')->nullable();
            $table->text('phone')->nullable();            
            $table->enum('gender', ['Male', 'Female', 'Other']);
            $table->string('age');
            $table->date('date_of_birth');
            $table->foreignId('island_id')->constrained('islands');
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->softDeletes();
            $table->timestamps();
        });

        // Create Victims Table
        Schema::create('victims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('case_id')->constrained('cases');
            $table->string('first_name');
            $table->string('last_name');
            $table->text('address')->nullable();
            $table->text('contact')->nullable();
            $table->text('phone')->nullable();            
            $table->enum('gender', ['Male', 'Female', 'Other']);
            $table->string('age');
            $table->date('date_of_birth');
            $table->foreignId('island_id')->constrained('islands');
            $table->enum('age_group', ['Under 13', 'Under 15', 'Under 18', 'Above 18'])->nullable();
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
            $table->foreignId('lawyer_id')->constrained('users');
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
            $table->softDeletes();
        });

        // Create Reports Table
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_group_id')->constrained('report_groups')->onDelete('cascade');
            $table->string('name');
            $table->text('description');
            $table->longText('query');
            $table->timestamps();
            $table->softDeletes();
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
        Schema::dropIfExists('appeal_details');
        Schema::dropIfExists('court_hearings');
        Schema::dropIfExists('cases');
        Schema::dropIfExists('courts_of_appeal');
        Schema::dropIfExists('reasons_for_closure');
        Schema::dropIfExists('islands');
        Schema::dropIfExists('offence_categories');
    }
}