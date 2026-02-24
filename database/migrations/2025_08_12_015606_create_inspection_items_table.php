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
    Schema::create('inspection_items', function (Blueprint $t) {
        $t->id();
        $t->foreignId('inspection_id')->constrained()->cascadeOnDelete();
        $t->foreignId('checklist_item_id')->constrained()->cascadeOnDelete();
        $t->unsignedTinyInteger('rating')->nullable(); // 1..5
        $t->text('note')->nullable();
        $t->string('photo_path')->nullable();
        $t->timestamps();
        $t->unique(['inspection_id','checklist_item_id']);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inspection_items');
    }
};
