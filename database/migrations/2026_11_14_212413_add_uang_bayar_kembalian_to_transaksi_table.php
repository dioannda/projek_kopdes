<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('transaksi', function (Blueprint $table) {
            $table->decimal('uang_bayar', 14, 2)->nullable()->after('total');
            $table->decimal('kembalian', 14, 2)->nullable()->after('uang_bayar');
        });

        // Transaksi QRIS lama: bayar selalu sama dengan total, kembalian 0
        // (aturan QRIS statis). Transaksi tunai lama dibiarkan kosong
        // karena nilai bayarnya memang tidak pernah tersimpan.
        DB::statement("UPDATE transaksi SET uang_bayar = total, kembalian = 0 WHERE metode_pembayaran = 'qris'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaksi', function (Blueprint $table) {
            $table->dropColumn(['uang_bayar', 'kembalian']);
        });
    }
};
