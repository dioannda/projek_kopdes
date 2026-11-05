<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaksi', function (Blueprint $table) {
            $table->tinyInteger('shift')->default(1)->after('metode_pembayaran');
        });

        // Isi shift transaksi lama dari jam transaksinya:
        // sebelum pukul 14.00 = Shift 1, pukul 14.00 ke atas = Shift 2.
        DB::statement('UPDATE transaksi SET shift = CASE WHEN HOUR(tanggal) < 14 THEN 1 ELSE 2 END');
    }

    public function down(): void
    {
        Schema::table('transaksi', function (Blueprint $table) {
            $table->dropColumn('shift');
        });
    }
};
