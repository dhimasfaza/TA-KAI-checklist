<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
{
    Schema::table('inspection_items', function (Blueprint $t) {
        $t->string('photo_path_1')->nullable()->after('photo_path');
        $t->string('photo_path_2')->nullable()->after('photo_path_1');
    });
}

public function down(): void
{
    Schema::table('inspection_items', function (Blueprint $t) {
        $t->dropColumn(['photo_path_1','photo_path_2']);
    });
}
};
