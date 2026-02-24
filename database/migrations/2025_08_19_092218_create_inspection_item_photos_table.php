<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('inspection_item_photos', function (Blueprint $t) {
            $t->id();
            $t->foreignId('inspection_item_id')->constrained()->cascadeOnDelete();
            $t->string('photo_path'); // path di storage/public
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inspection_item_photos');
    }
};
