<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use RuntimeException;

class ImportVehicleAssignmentDetails extends Command
{
    protected $signature = 'sigec:importar-detalles-vehiculos
        {--aplicar : Guardar los detalles que superen todas las validaciones}
        {--archivo= : Ruta del Excel; por defecto usa storage/app/importaciones}';

    protected $description = 'Simula o importa detalles institucionales validados para vehículos existentes';

    private const ARCHIVO_PREDETERMINADO = 'importaciones/Lista_detalles_vehiculos_SIGECOM_para_Codex.xlsx';

    public function handle(): int
    {
        $path = $this->option('archivo')
            ? $this->option('archivo')
            : storage_path('app/' . self::ARCHIVO_PREDETERMINADO);

        if (!is_file($path) || !is_readable($path)) {
            $this->error("No se puede leer el archivo Excel: {$path}");

            return self::FAILURE;
        }

        if ($this->option('aplicar') && !$this->columnasDestinoExisten()) {
            $this->error(
                'No se puede aplicar la importación: ejecute primero, con autorización, '
                . 'la migración que agrega las columnas de detalle.'
            );

            return self::FAILURE;
        }

        $records = $this->leerRegistros($path);
        $vehicles = DB::table('vehiculos')
            ->select([
                'id',
                'codigo',
                'placa',
                'tipo',
                ...($this->columnasDestinoExisten()
                    ? ['detalle_asignacion_predeterminado']
                    : []),
            ])
            ->get();

        $result = $this->comparar($records, $vehicles);

        if ($this->option('aplicar')) {
            $result = $this->aplicarDetalles($result);
        }

        $this->mostrarResultado($result, (bool) $this->option('aplicar'));

        return $result['conflictivos'] > 0 ? self::FAILURE : self::SUCCESS;
    }

    private function columnasDestinoExisten(): bool
    {
        return Schema::hasColumn('vehiculos', 'detalle_asignacion_predeterminado')
            && Schema::hasColumn('vales_combustible', 'detalle_asignacion');
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function leerRegistros(string $path): array
    {
        $workbook = IOFactory::load($path);
        $sheet = $workbook->getSheetByName('Lista para Codex');

        if (!$sheet instanceof Worksheet) {
            throw new RuntimeException(
                'El libro no contiene la hoja requerida "Lista para Codex".'
            );
        }

        $rows = $sheet->toArray(null, true, true, false);
        $headers = array_map(fn ($header) => $this->normalizarEncabezado((string) $header), array_shift($rows) ?? []);
        $columns = array_flip($headers);

        $required = [
            'codigo_sigecom',
            'placa_referencia',
            'tipo',
            'detalle_institucional_propuesto',
            'origen',
            'accion',
        ];

        foreach ($required as $header) {
            if (!array_key_exists($header, $columns)) {
                throw new RuntimeException(
                    "Falta la columna requerida en el Excel: {$header}."
                );
            }
        }

        $records = [];

        foreach ($rows as $offset => $row) {
            if (count(array_filter($row, fn ($value) => trim((string) $value) !== '')) === 0) {
                continue;
            }

            $records[] = [
                'fila' => $offset + 2,
                'codigo' => trim((string) ($row[$columns['codigo_sigecom']] ?? '')),
                'placa' => trim((string) ($row[$columns['placa_referencia']] ?? '')),
                'tipo' => trim((string) ($row[$columns['tipo']] ?? '')),
                'detalle' => (string) ($row[$columns['detalle_institucional_propuesto']] ?? ''),
                'origen' => trim((string) ($row[$columns['origen']] ?? '')),
                'accion' => trim((string) ($row[$columns['accion']] ?? '')),
            ];
        }

        return $records;
    }

    private function normalizarEncabezado(string $header): string
    {
        $ascii = strtr(mb_strtolower(trim($header)), [
            'á' => 'a',
            'é' => 'e',
            'í' => 'i',
            'ó' => 'o',
            'ú' => 'u',
            'ü' => 'u',
            'ñ' => 'n',
        ]);

        return trim((string) preg_replace('/[^a-z0-9]+/', '_', $ascii), '_');
    }

    private function normalizarIdentificador(string $value): string
    {
        return preg_replace('/[^A-Z0-9]/', '', mb_strtoupper(trim($value)));
    }

    /**
     * @param  array<int, array<string, mixed>>  $records
     * @param  \Illuminate\Support\Collection<int, object>  $vehicles
     * @return array<string, mixed>
     */
    private function comparar(array $records, $vehicles): array
    {
        $vehiclesByCode = [];
        $vehiclesByPlate = [];

        foreach ($vehicles as $vehicle) {
            $vehiclesByCode[$this->normalizarIdentificador($vehicle->codigo)][] = $vehicle;
            $vehiclesByPlate[$this->normalizarIdentificador($vehicle->placa)][] = $vehicle;
        }

        $recordsByCode = [];
        $recordsByPlate = [];

        foreach ($records as $index => $record) {
            $recordsByCode[$this->normalizarIdentificador($record['codigo'])][] = $index;
            $recordsByPlate[$this->normalizarIdentificador($record['placa'])][] = $index;
        }

        $result = [
            'validos' => [],
            'pendientes' => [],
            'conflictos' => [],
            'observaciones' => [],
            'sin_detalle' => 0,
            'detalles_repetidos' => [],
            'coincidencias' => 0,
            'conflictivos' => 0,
        ];
        $detailsByText = [];

        foreach ($records as $index => $record) {
            $codeKey = $this->normalizarIdentificador($record['codigo']);
            $plateKey = $this->normalizarIdentificador($record['placa']);
            $detail = $record['detalle'];

            if (trim($detail) === '') {
                $result['sin_detalle']++;
                $result['pendientes'][] = $this->clasificar(
                    $record,
                    null,
                    'Detalle vacío.'
                );
                continue;
            }

            $detailsByText[$detail][] = $record['codigo'];

            $reason = null;

            if (
                $codeKey === ''
                || $plateKey === ''
                || count($recordsByCode[$codeKey] ?? []) !== 1
                || count($recordsByPlate[$plateKey] ?? []) !== 1
            ) {
                $reason = 'Código o placa vacíos o duplicados en el Excel.';
            } elseif (
                count($vehiclesByCode[$codeKey] ?? []) !== 1
                || count($vehiclesByPlate[$plateKey] ?? []) !== 1
            ) {
                $reason = 'Código o placa sin coincidencia única en vehiculos.';
            } else {
                $vehicleByCode = $vehiclesByCode[$codeKey][0];
                $vehicleByPlate = $vehiclesByPlate[$plateKey][0];

                if ($vehicleByCode->id !== $vehicleByPlate->id) {
                    $reason = 'El código y la placa apuntan a vehículos distintos.';
                } else {
                    $result['coincidencias']++;

                    if (
                        $this->normalizarIdentificador($record['tipo']) !== ''
                        && $this->normalizarIdentificador($record['tipo'])
                            !== $this->normalizarIdentificador((string) $vehicleByCode->tipo)
                    ) {
                        $result['observaciones'][] = $this->clasificar(
                            $record,
                            $vehicleByCode,
                            'El tipo difiere; solo se usa como observación y no se modifica.'
                        );
                    }

                    $action = $this->normalizarEncabezado($record['accion']);

                    if (preg_match('/confirmar_texto|descripcion_ambigua|revisar_detalle/', $action)) {
                        $result['pendientes'][] = $this->clasificar(
                            $record,
                            $vehicleByCode,
                            'El Excel requiere confirmación del texto del detalle.'
                        );
                        continue;
                    }

                    $existing = (string) ($vehicleByCode->detalle_asignacion_predeterminado ?? '');

                    if (trim($existing) !== '' && $existing !== $detail) {
                        $reason = 'Ya existe un detalle predeterminado diferente.';
                    } else {
                        $result['validos'][] = $this->clasificar(
                            $record,
                            $vehicleByCode,
                            $existing === '' ? 'Coinciden código y placa.' : 'El detalle ya coincide.'
                        );
                        continue;
                    }
                }
            }

            $result['conflictos'][] = $this->clasificar($record, null, $reason);
        }

        foreach ($detailsByText as $detail => $codes) {
            if (count($codes) > 1) {
                $result['detalles_repetidos'][] = [
                    'detalle' => $detail,
                    'codigos' => $codes,
                    'cantidad' => count($codes),
                ];
            }
        }

        $result['conflictivos'] = count($result['conflictos']);

        return $result;
    }

    /**
     * @return array<string, mixed>
     */
    private function clasificar(array $record, ?object $vehicle, string $reason): array
    {
        return [
            'fila' => $record['fila'],
            'codigo' => $record['codigo'],
            'placa_excel' => $record['placa'],
            'placa_bd' => $vehicle->placa ?? null,
            'tipo_excel' => $record['tipo'],
            'tipo_bd' => $vehicle->tipo ?? null,
            'detalle' => $record['detalle'],
            'origen' => $record['origen'],
            'razon' => $reason,
            'vehiculo_id' => $vehicle->id ?? null,
        ];
    }

    /**
     * @param  array<string, mixed>  $result
     * @return array<string, mixed>
     */
    private function aplicarDetalles(array $result): array
    {
        return DB::transaction(function () use ($result) {
            $remaining = [];

            foreach ($result['validos'] as $record) {
                $vehicle = DB::table('vehiculos')
                    ->where('id', $record['vehiculo_id'])
                    ->lockForUpdate()
                    ->first(['id', 'detalle_asignacion_predeterminado']);

                if (!$vehicle) {
                    $result['conflictos'][] = $this->clasificar(
                        $record,
                        null,
                        'El vehículo dejó de existir antes de aplicar.'
                    );
                    continue;
                }

                $current = (string) $vehicle->detalle_asignacion_predeterminado;

                if (trim($current) !== '' && $current !== $record['detalle']) {
                    $result['conflictos'][] = $this->clasificar(
                        $record,
                        $vehicle,
                        'El detalle cambió y ahora es diferente; no se sobrescribió.'
                    );
                    continue;
                }

                if (trim($current) === '') {
                    DB::table('vehiculos')
                        ->where('id', $vehicle->id)
                        ->update([
                            'detalle_asignacion_predeterminado' => $record['detalle'],
                        ]);
                    $record['razon'] = 'Actualizado.';
                } else {
                    $record['razon'] = 'Ya coincidía; sin cambios.';
                }

                $remaining[] = $record;
            }

            $result['validos'] = $remaining;
            $result['conflictivos'] = count($result['conflictos']);

            return $result;
        });
    }

    /**
     * @param  array<string, mixed>  $result
     */
    private function mostrarResultado(array $result, bool $aplicado): void
    {
        $this->info($aplicado ? 'Resultado de la importación:' : 'Simulación; no se modificó la base de datos.');
        $this->line('Coincidencias por código y placa: ' . $result['coincidencias']);
        $this->line('Detalles válidos: ' . count($result['validos']));
        $this->line('Pendientes de revisión: ' . count($result['pendientes']));
        $this->line('Conflictos: ' . $result['conflictivos']);
        $this->line('Filas sin detalle: ' . $result['sin_detalle']);
        $this->line('Diferencias de tipo (informativas): ' . count($result['observaciones']));
        $this->line('Grupos de detalles repetidos: ' . count($result['detalles_repetidos']));

        foreach ([
            'Pendientes' => $result['pendientes'],
            'Conflictos' => $result['conflictos'],
            'Diferencias informativas' => $result['observaciones'],
        ] as $label => $items) {
            if ($items === []) {
                continue;
            }

            $this->newLine();
            $this->warn($label . ':');

            foreach ($items as $item) {
                $this->line(sprintf(
                    'Fila %d | %s | placa Excel %s%s%s | %s',
                    $item['fila'],
                    $item['codigo'] !== '' ? $item['codigo'] : '(sin código)',
                    $item['placa_excel'] !== '' ? $item['placa_excel'] : '(vacía)',
                    $item['placa_bd'] ? ' / BD ' . $item['placa_bd'] : '',
                    $label === 'Diferencias informativas'
                        ? ' | tipo Excel ' . ($item['tipo_excel'] ?: '(vacío)')
                            . ' / BD ' . ($item['tipo_bd'] ?: '(vacío)')
                        : '',
                    $item['razon']
                ));
            }
        }

        if ($result['detalles_repetidos'] !== []) {
            $this->newLine();
            $this->comment('Detalles iguales asignados a varios códigos (informativo; no se consideran conflicto):');

            foreach ($result['detalles_repetidos'] as $duplicate) {
                $this->line(sprintf(
                    '%d filas: %s',
                    $duplicate['cantidad'],
                    implode(', ', $duplicate['codigos'])
                ));
            }
        }
    }
}
