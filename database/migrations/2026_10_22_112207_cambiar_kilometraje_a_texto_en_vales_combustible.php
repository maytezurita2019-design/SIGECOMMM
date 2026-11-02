<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::connection()->getDriverName() !== 'pgsql') {
            return;
        }

        DB::statement("
            ALTER TABLE vales_combustible
            ALTER COLUMN kilometraje TYPE VARCHAR(50)
            USING kilometraje::VARCHAR
        ");
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() !== 'pgsql') {
            return;
        }

        DB::statement("
            ALTER TABLE vales_combustible
            ALTER COLUMN kilometraje TYPE BIGINT
            USING NULLIF(kilometraje, '')::BIGINT
        ");
    }
};