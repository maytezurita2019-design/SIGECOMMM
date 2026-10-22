<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('numeraciones_vales', function (Blueprint $table) {
            $table->integer('anio')->primary();
            $table->integer('ultimo_numero')->default(0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('numeraciones_vales');
    }
};
