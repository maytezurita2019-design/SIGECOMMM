<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void
    {
        $vales = DB::table('vales_combustible as v')->join('periodos as p', 'p.id', '=', 'v.periodo_id')->select('v.id', 'v.numero_vale', 'p.anio')->get();
        $vistos = [];
        foreach ($vales as $vale) {
            $clave = $vale->anio.'-'.(int) $vale->numero_vale;
            if (isset($vistos[$clave]) || $vale->numero_vale < 1 || $vale->numero_vale > 999999) {
                throw new RuntimeException('Hay números históricos duplicados en un año o fuera de seis dígitos. Deben revisarse antes de migrar; no se renumeraron vales.');
            }
            $vistos[$clave] = true;
        }
        Schema::create('numeraciones_vales', function (Blueprint $table) {
            $table->unsignedSmallInteger('anio')->primary();
            $table->unsignedInteger('ultimo_numero')->default(0);
        });
        foreach (DB::table('periodos')->distinct()->pluck('anio') as $anio) {
            DB::table('numeraciones_vales')->insert(['anio' => $anio, 'ultimo_numero' => $vales->where('anio', $anio)->max('numero_vale') ?? 0]);
        }
        Schema::table('vales_combustible', function (Blueprint $table) {
            $table->dropUnique(['periodo_id', 'numero_vale']);
            $table->string('numero_vale', 6)->change();
            $table->unsignedSmallInteger('anio')->nullable();
        });
        foreach ($vales as $vale) {
            DB::table('vales_combustible')->where('id', $vale->id)->update(['anio' => $vale->anio, 'numero_vale' => sprintf('%06d', $vale->numero_vale)]);
        }
        Schema::table('vales_combustible', function (Blueprint $table) {
            $table->unsignedSmallInteger('anio')->nullable(false)->change();
            $table->unique(['anio', 'numero_vale']);
        });
    }
    public function down(): void
    {
        throw new RuntimeException('La reversión requiere revisar la numeración anual antes de modificar los datos.');
    }
};
