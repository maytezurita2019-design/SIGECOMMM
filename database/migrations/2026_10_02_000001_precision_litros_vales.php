<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('vales_combustible', function (Blueprint $table) {
            $table->decimal('cantidad_litros', 21, 13)->nullable()->change();
        });
    }

    public function down(): void
    {
        throw new RuntimeException('Reducir la precisión eliminaría decimales de los litros registrados.');
    }
};
