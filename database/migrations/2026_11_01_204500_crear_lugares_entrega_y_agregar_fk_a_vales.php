<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lugares_entrega', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 150);
            $table->boolean('estado')->default(true);
            $table->timestamps();
        });

        DB::statement(
            'CREATE UNIQUE INDEX lugares_entrega_nombre_lower_unique ' .
            'ON lugares_entrega (LOWER(TRIM(nombre)))'
        );

        Schema::table('vales_combustible', function (Blueprint $table) {
            $table->foreignId('lugar_entrega_id')
                ->nullable()
                ->constrained('lugares_entrega')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('vales_combustible', function (Blueprint $table) {
            $table->dropForeign(['lugar_entrega_id']);
            $table->dropColumn('lugar_entrega_id');
        });

        DB::statement('DROP INDEX IF EXISTS lugares_entrega_nombre_lower_unique');
        Schema::dropIfExists('lugares_entrega');
    }
};
