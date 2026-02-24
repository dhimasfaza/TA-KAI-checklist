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
    Schema::create('inspections', function (Blueprint $t) {
        $t->id();
        $t->foreignId('checklist_id')->constrained();
        $t->foreignId('location_id')->constrained();
        $t->string('inspector_name'); // sederhana dulu, belum pakai users
        $t->date('visited_at');
        $t->string('status')->default('draft');
        $t->text('overall_note')->nullable();
        $t->decimal('overall_score',5,2)->nullable();
        $t->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inspections');
    }
};
