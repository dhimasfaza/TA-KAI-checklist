<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('inspections', function (Blueprint $t) {
            // nominal rupiah, input manual user
            $t->unsignedBigInteger('opening_balance')->nullable()->after('sloc');       // Saldo awal (awal bulan)
            $t->unsignedBigInteger('income_total')->nullable()->after('opening_balance');  // Pemasukan
            $t->unsignedBigInteger('expense_total')->nullable()->after('income_total');    // Pengeluaran
            $t->unsignedBigInteger('closing_balance')->nullable()->after('expense_total'); // Saldo terakhir
        });
    }

    public function down(): void
    {
        Schema::table('inspections', function (Blueprint $t) {
            $t->dropColumn(['opening_balance','income_total','expense_total','closing_balance']);
        });
    }
};
