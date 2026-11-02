<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('series_correlativas', 'estado')) {
            Schema::table('series_correlativas', function (Blueprint $table): void {
                $table->string('estado', 20)->default('ACTIVA');
            });
        }
    }

    public function down(): void
    {
        throw new RuntimeException(
            'No se puede revertir el estado de las series correlativas: los estados configurados deben conservarse.'
        );
    }
};
