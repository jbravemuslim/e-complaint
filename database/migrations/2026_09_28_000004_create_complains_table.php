<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('complains', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->text('keluhan');
            $table->string('lampiran')->nullable();
            $table->date('tgl_keluhan');
            $table->foreignId('tujuan_id')->nullable()->constrained('tujuans')->nullOnDelete();
            $table->foreignId('jenis_id')->nullable()->constrained('jenis')->nullOnDelete();
            $table->enum('status', ['proses', 'selesai'])->default('proses');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('complains');
    }
};
