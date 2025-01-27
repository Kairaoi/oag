<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCivilCaseSystemTables extends Migration
{
    public function up()
    {
        Schema::create('court_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // 'High Court', 'Magistrate Court'
            $table->string('code'); // 'HC' for High Court, 'MC' for Magistrate Court
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('court_number_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('court_category_id')->constrained('court_categories');
            $table->string('prefix'); // 'Lit', 'HCCiv', 'MM', 'Bailan', 'Misapp', 'BaiCiv', 'Betland', etc.
            $table->string('description');
            $table->boolean('is_primary')->default(false); // To indicate if it's the main number
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('case_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('civil_cases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('court_category_id')->constrained('court_categories');
            $table->foreignId('case_type_id')->constrained('case_types');
            
            // Primary reference numbers
            $table->string('primary_number')->nullable(); // Main case number
            $table->integer('number')->nullable();        // The actual number
            $table->integer('year');                     // The year
            
            $table->text('case_name');
            $table->text('case_description')->nullable();
            $table->text('current_status');
            $table->date('status_date');
            $table->text('action_required');
            $table->enum('monitoring_status', ['Active', 'Pending', 'Closed']);
            $table->boolean('entered_by_sg_dsg')->default(false);
            
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('case_reference_numbers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('civil_case_id')->constrained('civil_cases');
            $table->foreignId('court_number_type_id')->constrained('court_number_types');
            $table->integer('number');
            $table->integer('year');
            $table->string('formatted_number'); // Full formatted number (e.g., "Lit 4/24")
            $table->boolean('is_primary')->default(false);
            $table->text('notes')->nullable(); // For any additional information
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('case_counsels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('civil_case_id')->constrained('civil_cases');
            $table->foreignId('user_id')->constrained('users');
            $table->enum('type', ['Plaintiff', 'Defendant']);
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('case_counsels');
        Schema::dropIfExists('case_reference_numbers');
        Schema::dropIfExists('civil_cases');
        Schema::dropIfExists('case_types');
        Schema::dropIfExists('court_number_types');
        Schema::dropIfExists('court_categories');
    }
}