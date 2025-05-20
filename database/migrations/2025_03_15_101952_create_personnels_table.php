<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('personnels', function (Blueprint $table) {
            $table->id();
            $table->string('itemNo');
            $table->string('position');
            $table->string('salaryGrade');
            $table->string('authorizedSalary');
            $table->string('actualSalary');
            $table->string('step');
            $table->string('code');
            $table->string('type');
            $table->string('level');
            $table->string('lastName');
            $table->string('firstName');
            $table->string('middleName')->nullable();
            $table->date('dob');
            $table->date('originalAppointment');
            $table->date('lastPromotion')->nullable();
            $table->string('status');
            $table->string('office');
            $table->foreign('office')->references('code')->on('offices')->onDelete('cascade');
            $table->foreignId('employeeId')->nullable()->constrained()->onDelete('set null');
            $table->foreign('itemNo')->references('itemNo')->on('positions')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('personnels');
    }
};