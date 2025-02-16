<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('passport_applications', function (Blueprint $table) {
            $table->index('created_by');
            $table->unique('workflow_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('passport_applications', function (Blueprint $table) {
            $table->dropIndex('passport_applications_created_by_index');
            $table->dropUnique('passport_applications_workflow_id_unique');
        });
    }
};
