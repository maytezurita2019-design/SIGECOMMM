<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('partes_diarios', function (Blueprint $table) {

            $table->bigInteger('km_inicial')
                ->nullable();

            $table->bigInteger('km_final')
                ->nullable();

        });
    }

    public function down(): void
    {
        Schema::table('partes_diarios', function (Blueprint $table) {

            $table->dropColumn([
                'km_inicial',
                'km_final',
            ]);

        });
    }
};
