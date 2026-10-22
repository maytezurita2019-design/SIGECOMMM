<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vales_combustible', function (Blueprint $table) {
            $table->foreignId('periodo_id')
                ->nullable()
                ->change();
        });
    }

    public function down(): void
    {
        Schema::table('vales_combustible', function (Blueprint $table) {
            $table->foreignId('periodo_id')
                ->nullable(false)
                ->change();
        });
    }
};