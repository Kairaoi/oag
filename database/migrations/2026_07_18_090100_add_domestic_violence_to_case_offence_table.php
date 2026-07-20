<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('case_offence', function (Blueprint $table) {
            $table->boolean('is_domestic_violence')->default(false)->after('category_id');
        });
    }

    public function down(): void
    {
        Schema::table('case_offence', function (Blueprint $table) {
            $table->dropColumn('is_domestic_violence');
        });
    }
};
