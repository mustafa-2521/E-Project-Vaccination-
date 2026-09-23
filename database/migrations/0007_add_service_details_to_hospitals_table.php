<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hospitals', function (Blueprint $table) {
            $table->decimal('consultation_fee', 10, 2)->default(0)->after('license_no');
            $table->decimal('vaccination_fee', 10, 2)->default(0)->after('consultation_fee');
            $table->string('opening_hours', 120)->nullable()->after('vaccination_fee');
            $table->string('facilities', 500)->nullable()->after('opening_hours');
        });
    }

    public function down(): void
    {
        Schema::table('hospitals', function (Blueprint $table) {
            $table->dropColumn([
                'consultation_fee',
                'vaccination_fee',
                'opening_hours',
                'facilities',
            ]);
        });
    }
};
