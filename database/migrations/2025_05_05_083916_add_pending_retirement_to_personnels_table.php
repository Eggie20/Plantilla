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
    Schema::table('personnels', function (Blueprint $table) {
        $table->boolean('pendingRetirement')->default(false);
        $table->date('retirement_date')->nullable();
    });
}

public function down()
{
    Schema::table('personnels', function (Blueprint $table) {
        $table->dropColumn(['pendingRetirement', 'retirement_date']);
    });
}
};
