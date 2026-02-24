<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('inspections', function (Blueprint $t) {
            // nominal rupiah tanpa desimal
            $t->unsignedBigInteger('assets_value')->nullable()->after('assets_count');
        });
    }

    public function down(): void
    {
        Schema::table('inspections', function (Blueprint $t) {
            $t->dropColumn('assets_value');
        });
    }
};
