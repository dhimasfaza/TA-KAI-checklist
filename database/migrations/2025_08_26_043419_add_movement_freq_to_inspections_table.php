<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('inspections', function (Blueprint $t) {
            $t->unsignedInteger('movement_freq')->nullable()->after('closing_balance');
        });
    }

    public function down(): void
    {
        Schema::table('inspections', function (Blueprint $t) {
            $t->dropColumn('movement_freq');
        });
    }
};
