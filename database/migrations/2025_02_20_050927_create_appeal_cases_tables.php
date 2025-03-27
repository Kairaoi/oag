<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppealCasesTables extends Migration
{
    public function up()
    {
        // Appeal Types table
        Schema::create('appeal_types', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., 'Conviction Appeal', 'Sentence Appeal', 'Ruling Appeal'
            $table->string('code'); // e.g., 'CA', 'SA', 'RA'
            $table->text('description')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });

        // Appeal Grounds table
        Schema::create('appeal_grounds', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., 'Excessive Sentence', 'Error of Law'
            $table->text('description')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });

        // Appeal Cases table
        Schema::create('appeal_cases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('original_case_id')->constrained('civil_cases');
            $table->foreignId('appeal_type_id')->constrained('appeal_types');
            
            // Appeal case number
            $table->string('appeal_number');
            $table->integer('number');
            $table->integer('year');
            
            // Party information
            $table->string('appellant'); // The party making the appeal
            $table->string('respondent');
            
            // Court decision
            $table->enum('decision_status', ['Pending', 'Won', 'Lost', 'Partially Won']);
            $table->text('decision_reasoning')->nullable();
            $table->text('legal_principles')->nullable();
            $table->date('decision_date')->nullable();
            
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });

        // Appeal Grounds Junction table
        Schema::create('appeal_case_grounds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appeal_case_id')->constrained('appeal_cases');
            $table->foreignId('appeal_ground_id')->constrained('appeal_grounds');
            $table->text('ground_details')->nullable();
            $table->timestamps();
        });

        // Appeal Counsel table
        Schema::create('appeal_counsels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appeal_case_id')->constrained('appeal_cases');
            $table->foreignId('user_id')->constrained('users');
            $table->boolean('is_original_counsel'); // To track if counsel is same as original case
            $table->enum('party_representing', ['Appellant', 'Respondent']);
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('appeal_counsels');
        Schema::dropIfExists('appeal_case_grounds');
        Schema::dropIfExists('appeal_cases');
        Schema::dropIfExists('appeal_grounds');
        Schema::dropIfExists('appeal_types');
    }
}