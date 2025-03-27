<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourtOfAppealTables extends Migration
{
    public function up()
    {
        // Court of Appeal Cases table
        Schema::create('court_of_appeal_cases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('original_case_id')->constrained('civil_cases');
            $table->foreignId('appeal_case_id')->nullable()->constrained('appeal_cases');
            
            // COA specific number
            $table->string('coa_number');
            $table->integer('number');
            $table->integer('year');
            
            // Party Information
            $table->string('appellant_name');
            $table->string('respondent_name');
            $table->text('case_description')->nullable();
            
            // Case Status and Decision
            $table->enum('status', ['Pending', 'Heard', 'Reserved', 'Decided']);
            $table->enum('decision', ['Pending', 'Allowed', 'Dismissed', 'Partially Allowed'])->default('Pending');
            $table->text('decision_reasoning')->nullable();
            $table->text('legal_principles')->nullable();
            $table->date('hearing_date')->nullable();
            $table->date('decision_date')->nullable();
            
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });

        // Court of Appeal Counsel table
        Schema::create('coa_counsels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('court_of_appeal_case_id')->constrained('court_of_appeal_cases');
            $table->foreignId('user_id')->constrained('users');
            $table->enum('party_representing', ['Appellant', 'Respondent']);
            $table->boolean('is_previous_counsel'); // Track if counsel is from previous case
            $table->date('appointment_date');
            $table->text('notes')->nullable();
            
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });

        // Court of Appeal Issues table
        Schema::create('coa_appeal_issues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('court_of_appeal_case_id')->constrained('court_of_appeal_cases');
            $table->text('issue_description');
            $table->text('court_finding')->nullable();
            $table->boolean('is_resolved')->default(false);
            
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });

        // Court of Appeal Documents table
        Schema::create('coa_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('court_of_appeal_case_id')->constrained('court_of_appeal_cases');
            $table->string('document_type'); // e.g., 'Notice of Appeal', 'Written Submissions'
            $table->string('file_path')->nullable();
            $table->date('filing_date');
            $table->text('notes')->nullable();
            
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('coa_documents');
        Schema::dropIfExists('coa_appeal_issues');
        Schema::dropIfExists('coa_counsels');
        Schema::dropIfExists('court_of_appeal_cases');
    }
}