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
        Schema::create('positions', function (Blueprint $table) {
            $table->id();
            $table->string('itemNo')->unique();
            $table->foreignId('office_id')->constrained('offices');
            $table->string('position');
            $table->string('salaryGrade');
            $table->integer('step')->nullable();
            $table->string('code')->nullable();
            $table->string('type')->default('M');
            $table->string('level')->nullable();
            $table->string('status')->default('Vacant');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('positions');
    }
};
