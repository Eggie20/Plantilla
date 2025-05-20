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
        Schema::table('personnels', function (Blueprint $table) {
            $table->string('lastName')->nullable()->change();
            $table->string('firstName')->nullable()->change();
            $table->date('dob')->nullable()->change();
            $table->date('originalAppointment')->nullable()->change();
            $table->date('lastPromotion')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('personnels', function (Blueprint $table) {
            $table->string('lastName')->nullable(false)->change();
            $table->string('firstName')->nullable(false)->change();
            $table->date('dob')->nullable(false)->change();
            $table->date('originalAppointment')->nullable(false)->change();
            $table->date('lastPromotion')->nullable(false)->change();
        });
    }
};