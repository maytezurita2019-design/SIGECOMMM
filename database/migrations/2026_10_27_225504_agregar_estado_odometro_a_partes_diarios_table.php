<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('partes_diarios', function (Blueprint $table) {

            $table->boolean('odometro_funciona')
                ->nullable()
                ->default(null);

        });
    }

    public function down(): void
    {
        Schema::table('partes_diarios', function (Blueprint $table) {

            $table->dropColumn('odometro_funciona');

        });
    }
};