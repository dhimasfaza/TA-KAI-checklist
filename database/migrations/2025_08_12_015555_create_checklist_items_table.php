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
    Schema::create('checklist_items', function (Blueprint $t) {
        $t->id();
        $t->foreignId('checklist_category_id')->constrained()->cascadeOnDelete();
        $t->string('title');
        $t->text('hint')->nullable();
        $t->decimal('weight',5,2)->default(1.00);
        $t->unsignedInteger('sort_order')->default(1);
        $t->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checklist_items');
    }
};
