<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifikasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_barang')->constrained('barang');
            $table->enum('jenis', ['stok_kosong', 'stok_menipis', 'kedaluwarsa']);
            $table->string('pesan', 255);
            $table->integer('rekomendasi_jumlah')->nullable();
            $table->boolean('status_baca')->default(false);
            $table->dateTime('tanggal');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifikasi');
    }
};
