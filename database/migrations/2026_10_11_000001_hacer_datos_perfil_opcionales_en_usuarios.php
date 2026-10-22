<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->string('nombre', 100)->nullable()->change();
            $table->string('apellido', 100)->nullable()->change();
            $table->string('ci', 30)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->string('nombre', 100)->nullable(false)->change();
            $table->string('apellido', 100)->nullable(false)->change();
            $table->string('ci', 30)->nullable(false)->change();
        });
    }
};
