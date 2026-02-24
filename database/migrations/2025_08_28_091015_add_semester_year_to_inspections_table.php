<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inspections', function (Blueprint $t) {
            $t->unsignedTinyInteger('semester')->nullable()->after('inspector_name'); // 1 or 2
            $t->unsignedSmallInteger('year')->nullable()->after('semester');          // e.g. 2025
        });
    }

    public function down(): void
    {
        Schema::table('inspections', function (Blueprint $t) {
            $t->dropColumn(['semester','year']);
        });
    }
};
