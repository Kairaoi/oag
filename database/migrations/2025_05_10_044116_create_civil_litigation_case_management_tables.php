<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCivilLitigationCaseManagementTables extends Migration
{
    public function up()
    {
        Schema::create('causes_of_action', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();  // Added softDeletes
        });

       


        Schema::create('case_pending_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();  // Added softDeletes
        });

        Schema::create('party_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();  // Added softDeletes
        });

        Schema::create('case_origin_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();  // Added softDeletes
        });

        Schema::create('civil2_cases', function (Blueprint $table) {
            $table->id();
            $table->string('case_file_no')->unique();
            $table->string('court_case_no')->nullable();
            $table->string('case_name');
            $table->date('date_received')->nullable();
            $table->date('date_opened');
            $table->date('date_closed')->nullable();
            $table->foreignId('court_type_id')->constrained('court_categories');
            $table->foreignId('cause_of_action_id')->constrained('causes_of_action');
            $table->foreignId('responsible_counsel_id')->constrained('users');
            // $table->foreignId('case_status_id')->constrained('case_statuses');
            // $table->foreignId('case_pending_status_id')->nullable()->constrained('case_pending_statuses');
            $table->foreignId('case_origin_type_id')->constrained('case_origin_types');

            // $table->text('case_description')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();  // Added softDeletes
        });

        Schema::create('case_statuses', function (Blueprint $table) {
            $table->id();

            // ADD THIS LINE before the foreign key constraint
            $table->foreignId('case_id')->constrained('civil2_cases')->onDelete('cascade');

            $table->date('status_date');
            $table->text('current_status');
            $table->text('action_required')->nullable();
            $table->text('monitoring_status')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('case_parties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('case_id')->constrained('civil_cases')->onDelete('cascade');
            $table->string('party_name');
            $table->foreignId('party_type_id')->constrained('party_types');
            
            $table->boolean('is_represented')->default(false);
            $table->foreignId('represented_by_user_id')->nullable()->constrained('users');
            $table->string('represented_by_external')->nullable(); // e.g., "Adv. Jane Doe"
        
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();  // Added softDeletes
        });
        
        Schema::create('case_status_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('case_id')->constrained('civil_cases')->onDelete('cascade');
            $table->foreignId('case_status_id')->constrained('case_statuses');
            $table->foreignId('case_pending_status_id')->nullable()->constrained('case_pending_statuses');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();  // Added softDeletes
        });

        Schema::create('case_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('case_id')->constrained('civil_cases')->onDelete('cascade');
            $table->string('activity_type');
            $table->date('activity_date');
            $table->text('description');
            $table->foreignId('performed_by')->constrained('users');
            $table->string('document_reference')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();  // Added softDeletes
        });

        Schema::create('case_closures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('case_id')->constrained('civil_cases')->onDelete('cascade');
            $table->date('memo_date');
            $table->boolean('sg_clearance')->default(false);
            $table->date('sg_clearance_date')->nullable();
            $table->boolean('ag_endorsement')->default(false);
            $table->date('ag_endorsement_date')->nullable();
            $table->boolean('file_archived')->default(false);
            $table->date('file_archived_date')->nullable();
            $table->foreignId('closed_by')->constrained('users');
            $table->text('closure_notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();  // Added softDeletes
        });

        Schema::create('quarterly_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('counsel_id')->constrained('users');
            $table->year('year');
            $table->tinyInteger('quarter');
            $table->date('submitted_date')->nullable();
            $table->boolean('is_submitted')->default(false);
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();  // Added softDeletes
        });

        Schema::create('quarterly_report_cases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quarterly_report_id')->constrained('quarterly_reports')->onDelete('cascade');
            $table->foreignId('case_id')->constrained('civil_cases');
            $table->string('other_counsel')->nullable();
            $table->text('current_status')->nullable();
            $table->text('required_work')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();  // Added softDeletes
        });

        Schema::create('casse_counsels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('civil2_case_id')->constrained('civil2_cases')->onDelete('cascade');

            // For internal or external counsel
            $table->unsignedBigInteger('counsel_id');
            $table->string('counsel_type'); // 'user' or 'external'

            $table->enum('role', ['plaintiff', 'defendant']); // role in the case

            $table->timestamps();
        });

        Schema::create('external_counsels', function (Blueprint $table) {
            $table->id();
            $table->string('name');               // External counsel's full name
            $table->string('email')->nullable(); // Optional contact info
            $table->string('phone')->nullable(); // Optional contact info
            $table->text('address')->nullable(); // Optional address
            $table->timestamps();
        });


    }

    public function down()
    {
        Schema::dropIfExists('external_counsels');
         Schema::dropIfExists('case_counsels');
        Schema::dropIfExists('quarterly_report_cases');
        Schema::dropIfExists('quarterly_reports');
        Schema::dropIfExists('case_closures');
        Schema::dropIfExists('case_activities');
        Schema::dropIfExists('case_status_history');
        Schema::dropIfExists('case_parties');
        Schema::dropIfExists('case_statuses');
        Schema::dropIfExists('civil2_cases');
        Schema::dropIfExists('case_origin_types');
        Schema::dropIfExists('party_types');
        Schema::dropIfExists('case_pending_statuses');     
        Schema::dropIfExists('causes_of_action');
    }
}
