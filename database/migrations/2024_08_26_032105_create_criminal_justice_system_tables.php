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
        Schema::create('courts', function (Blueprint $table) {
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
            $table->date('date_of_incident')->nullable();
            $table->foreignId('lawyer_id')->nullable()->constrained('users');
            $table->foreignId('island_id')->constrained('islands');
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            
            // New fields for review status, including "closed"
            $table->enum('status', ['pending', 'accepted', 'rejected', 'reallocated', 'allocated', 'closed', 'reviewed', 'appealed', 'courtcased'])->default('pending');

            
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

        Schema::create('court_cases', function (Blueprint $table) {
            $table->id();

            // Reference to criminal case
            $table->foreignId('case_id')->constrained('cases')->onDelete('cascade');

            // Key fields
            $table->date('charge_file_dated');
            $table->string('high_court_case_number')->nullable();

            // Court outcome summary
            $table->enum('verdict', ['guilty', 'not_guilty', 'dismissed', 'withdrawn', 'other'])->nullable();
            
            // Judgment details
            $table->date('judgment_delivered_date')->nullable();
            $table->enum('court_outcome', ['win', 'lose'])->nullable();
            $table->text('decision_principle_established')->nullable();

            // Record tracking
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->softDeletes();
            $table->timestamps();

            // Performance indexing
            $table->index(['case_id', 'charge_file_dated']);
            $table->index('court_outcome');
            $table->index('verdict');
        });

        // Create Appeal Details Table
        Schema::create('appeal_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('case_id')->constrained('cases')->onDelete('cascade');
            $table->string('appeal_case_number')->nullable();
            $table->date('appeal_filing_date')->nullable();
             // Court outcome summary
             $table->enum('verdict', ['guilty', 'not_guilty', 'dismissed', 'withdrawn', 'other'])->nullable();
             $table->string('filing_date_source'); 
                    // Judgment details
            $table->date('judgment_delivered_date')->nullable();
            $table->enum('court_outcome', ['win', 'lose'])->nullable();
            $table->text('decision_principle_established')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->softDeletes();
            $table->timestamps();

              // Performance indexing
              $table->index(['case_id', 'appeal_filing_date']);
              $table->index('court_outcome');
              $table->index('verdict');
        });


        Schema::create('court_of_appeals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('case_id')->constrained('cases')->onDelete('cascade');
            $table->string('appeal_case_number')->nullable();
            $table->date('appeal_filing_date');
            $table->string('filing_date_source');
            $table->date('judgment_delivered_date')->nullable();
            $table->enum('court_outcome', ['win', 'lose', 'remand'])->nullable();
            $table->text('decision_principle_established')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by');
            $table->timestamps();
            $table->softDeletes();
        });
        
        

        Schema::create('case_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('case_id')->unique()->constrained('cases')->onDelete('cascade'); // Ensures only one review per case
            $table->enum('evidence_status', [
                'pending_review',
                'sufficient_evidence',
                'insufficient_evidence',
                'returned_to_police'
            ])->default('pending_review');
            $table->text('offence_particulars')->nullable(); // <-- Added this line
            $table->datetime('review_date');
            $table->date('date_file_closed')->nullable();
            $table->foreignId('reason_for_closure_id')->nullable()->constrained('reasons_for_closure');
            $table->foreignId('new_lawyer_id')->nullable()->constrained('users');
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
            
            // Make this nullable for initial allocation
            $table->foreignId('from_lawyer_id')->nullable()->constrained('users');
            
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
        Schema::dropIfExists('court_cases');
        Schema::dropIfExists('court_hearings');
        Schema::dropIfExists('cases');
        Schema::dropIfExists('courts_of_appeal');
        Schema::dropIfExists('reasons_for_closure');
        Schema::dropIfExists('islands');
        Schema::dropIfExists('offence_categories');
    }
}