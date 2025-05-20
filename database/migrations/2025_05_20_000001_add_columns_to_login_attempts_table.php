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
        Schema::table('login_attempts', function (Blueprint $table) {
            // Add missing columns if they don't exist
            if (!Schema::hasColumn('login_attempts', 'attempts')) {
                $table->integer('attempts')->default(0);
            }
            if (!Schema::hasColumn('login_attempts', 'locked_until')) {
                $table->timestamp('locked_until')->nullable();
            }
            if (!Schema::hasColumn('login_attempts', 'last_attempt_at')) {
                $table->timestamp('last_attempt_at')->nullable();
            }
        });

        // Add indexes if they don't exist
        if (!Schema::hasIndex('login_attempts', 'login_attempts_username_ip_address_index')) {
            Schema::table('login_attempts', function (Blueprint $table) {
                $table->index(['username', 'ip_address']);
            });
        }
        if (!Schema::hasIndex('login_attempts', 'login_attempts_locked_until_index')) {
            Schema::table('login_attempts', function (Blueprint $table) {
                $table->index('locked_until');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('login_attempts', function (Blueprint $table) {
            // Drop indexes
            $table->dropIndex(['username', 'ip_address']);
            $table->dropIndex('locked_until');
            
            // Drop columns
            $table->dropColumn(['attempts', 'locked_until', 'last_attempt_at']);
        });
    }
};
