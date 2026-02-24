<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('inspections', function (Blueprint $t) {
            $t->string('sloc', 30)->nullable()->after('location_id');
        });
    }
    public function down(): void
    {
        Schema::table('inspections', function (Blueprint $t) {
            $t->dropColumn('sloc');
        });
    }
};
