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
        Schema::create('pembayarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sewa_id')->constrained('sewas')->cascadeOnDelete();
            $table->integer('jumlah');
            $table->enum('metode_pembayaran', ['transfer_bank', 'e_wallet', 'tunai']);
            $table->string('bukti_pembayaran')->nullable();
            $table->enum('status', ['pending', 'diverifikasi', 'ditolak'])->default('pending');
            $table->date('tanggal_bayar');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayarans');
    }
};
