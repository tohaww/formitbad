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
        Schema::table('users', function (Blueprint $table) {
            $table->text('google_sheets_access_token')->nullable()->after('password');
            $table->text('google_sheets_refresh_token')->nullable()->after('google_sheets_access_token');
            $table->timestamp('google_sheets_token_expires_at')->nullable()->after('google_sheets_refresh_token');
            $table->string('google_sheets_email')->nullable()->after('google_sheets_token_expires_at');
            $table->string('google_sheets_name')->nullable()->after('google_sheets_email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'google_sheets_access_token',
                'google_sheets_refresh_token',
                'google_sheets_token_expires_at',
                'google_sheets_email',
                'google_sheets_name',
            ]);
        });
    }
};
