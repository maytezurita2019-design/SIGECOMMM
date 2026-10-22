<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE vales_combustible
            ALTER COLUMN kilometraje TYPE VARCHAR(50)
            USING kilometraje::VARCHAR
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE vales_combustible
            ALTER COLUMN kilometraje TYPE BIGINT
            USING NULLIF(kilometraje, '')::BIGINT
        ");
    }
};