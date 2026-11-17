<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tutup_buku_shift', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->unsignedTinyInteger('shift'); // 1 atau 2
            $table->foreignId('id_kasir')->constrained('pengguna')->cascadeOnDelete();
            $table->decimal('saldo_awal', 14, 2)->default(0);
            $table->decimal('total_pendapatan', 14, 2);
            $table->decimal('total_tunai', 14, 2);
            $table->decimal('total_qris', 14, 2);
            $table->decimal('jumlah_diserahkan', 14, 2);
            $table->decimal('saldo_lanjutan', 14, 2);
            $table->string('keterangan', 255);
            $table->string('status', 20)->default('menunggu'); // menunggu, disetujui
            $table->foreignId('id_manajer')->nullable()->constrained('pengguna')->nullOnDelete();
            $table->timestamp('disetujui_pada')->nullable();
            $table->timestamps();
            $table->unique(['tanggal', 'shift', 'id_kasir']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tutup_buku_shift');
    }
};
