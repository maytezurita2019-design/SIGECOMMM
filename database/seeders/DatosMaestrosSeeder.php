<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FuenteFinanciamiento;
use App\Models\OrganismoFinanciador;
use App\Models\AperturaProgramatica;
use App\Models\Unidad;
use App\Models\Vehiculo;

class DatosMaestrosSeeder extends Seeder
{
    public function run(): void
    {
        $fuentes = [];
        foreach (['20', '41'] as $codigo) {
            $fuentes[$codigo] = FuenteFinanciamiento::updateOrCreate(
                ['codigo' => $codigo],
                ['nombre' => 'FF '.$codigo, 'estado' => true]
            );
        }

        $organismos = [];
        foreach (['113', '119', '210'] as $codigo) {
            $organismos[$codigo] = OrganismoFinanciador::updateOrCreate(
                ['codigo' => $codigo],
                ['nombre' => 'OF '.$codigo, 'estado' => true]
            );
        }

        $unidades = [];
        $nombresUnidades = [
            'DIRECCION DE MEDIO AMBIENTE Y MADRE TIERRA',
            'FUNCIONAMIENTO ALCALDIA MUNICIPAL',
            'SERVICIOS Y EQUIPAMIENTO DE EDUCACIÓN',
            'UNIDAD DE GESTION DE RIESGOS',
            'UNIDAD DE PARQUES, JARDINES Y SERVICIOS FORESTALES',
            'UNIDAD DE SEGURIDAD CIUDADANA',
            'UNIDAD DE SERVICIOS ELECTRICOS Y SEMAFORIZACION',
            'UNIDAD DE TRANSPORTES Y MAQUINARIAS',
        ];

        foreach ($nombresUnidades as $nombre) {
            $unidades[$nombre] = Unidad::updateOrCreate(
                ['nombre' => $nombre],
                ['estado' => true]
            );
        }

        $aperturas = [];
        $datosAperturas = [
            ['000 0 001', '20', '210'],
            ['000 0 001', '41', '113'],
            ['130 0 010', '20', '210'],
            ['131 0 010', '20', '210'],
            ['160 0 024', '20', '210'],
            ['210 0 001', '41', '113'],
            ['270 0 004', '41', '113'],
            ['310 0 012', '41', '113'],
            ['331 0 016', '41', '113'],
            ['331 0 016', '41', '119'],
        ];

        foreach ($datosAperturas as [$codigo, $ff, $of]) {
            $clave = $codigo.'|'.$ff.'|'.$of;
            $aperturas[$clave] = AperturaProgramatica::updateOrCreate(
                [
                    'codigo' => $codigo,
                    'fuente_financiamiento_id' => $fuentes[$ff]->id,
                    'organismo_financiador_id' => $organismos[$of]->id,
                ],
                ['estado' => true]
            );
        }

        $vehiculos = [
            ['B-01', '2731NXF', 'BUS COASTER', 'FUNCIONAMIENTO ALCALDIA MUNICIPAL', '000 0 001', '41', '113'],
            ['C-01', '942GLY', 'CAMIONETA', 'FUNCIONAMIENTO ALCALDIA MUNICIPAL', '000 0 001', '20', '210'],
            ['C-04', '1794HIL', 'CAMIONETA', 'FUNCIONAMIENTO ALCALDIA MUNICIPAL', '000 0 001', '20', '210'],
            ['C-05', '1882FIH', 'CAMIONETA', 'FUNCIONAMIENTO ALCALDIA MUNICIPAL', '000 0 001', '20', '210'],
            ['C-06', '2264FHC', 'CAMIONETA', 'FUNCIONAMIENTO ALCALDIA MUNICIPAL', '000 0 001', '41', '113'],
            ['C-07', '2263XUT', 'CAMIONETA', 'UNIDAD DE SERVICIOS ELECTRICOS Y SEMAFORIZACION', '160 0 024', '20', '210'],
            ['C-08', '2295SUT', 'CAMIONETA', 'UNIDAD DE TRANSPORTES Y MAQUINARIAS', '270 0 004', '41', '113'],
            ['C-09', '2295YTU', 'CAMIONETA', 'FUNCIONAMIENTO ALCALDIA MUNICIPAL', '000 0 001', '20', '210'],
            ['C-10', '2295YUA', 'CAMIONETA', 'FUNCIONAMIENTO ALCALDIA MUNICIPAL', '000 0 001', '20', '210'],
            ['C-11', '2838DLK', 'CAMIONETA', 'FUNCIONAMIENTO ALCALDIA MUNICIPAL', '000 0 001', '20', '210'],
            ['C-12', '2522LIH', 'CAMIONETA', 'FUNCIONAMIENTO ALCALDIA MUNICIPAL', '000 0 001', '20', '210'],
            ['C-13', '2522LKL', 'CAMIONETA', 'FUNCIONAMIENTO ALCALDIA MUNICIPAL', '000 0 001', '41', '113'],
            ['C-15', '2859BGB', 'CAMIONETA', 'FUNCIONAMIENTO ALCALDIA MUNICIPAL', '000 0 001', '41', '113'],
            ['C-16', '2877BLX', 'CAMIONETA', 'FUNCIONAMIENTO ALCALDIA MUNICIPAL', '000 0 001', '41', '113'],
            ['C-17', '2909YTD', 'CAMIONETA', 'UNIDAD DE TRANSPORTES Y MAQUINARIAS', '270 0 004', '41', '113'],
            ['C-18', '2909YUG', 'CAMIONETA', 'FUNCIONAMIENTO ALCALDIA MUNICIPAL', '000 0 001', '41', '113'],
            ['C-19', '2987CSE', 'CAMIONETA', 'FUNCIONAMIENTO ALCALDIA MUNICIPAL', '000 0 001', '41', '113'],
            ['C-20', '3012ATS', 'CAMIONETA', 'FUNCIONAMIENTO ALCALDIA MUNICIPAL', '000 0 001', '41', '113'],
            ['C-21', '3028HND', 'CAMIONETA', 'FUNCIONAMIENTO ALCALDIA MUNICIPAL', '000 0 001', '41', '113'],
            ['C-22', '3028HPG', 'CAMIONETA', 'FUNCIONAMIENTO ALCALDIA MUNICIPAL', '000 0 001', '41', '113'],
            ['C-23', '3109XLC', 'CAMIONETA', 'FUNCIONAMIENTO ALCALDIA MUNICIPAL', '000 0 001', '41', '113'],
            ['C-24', '3115TTT', 'CAMIONETA', 'FUNCIONAMIENTO ALCALDIA MUNICIPAL', '000 0 001', '41', '113'],
            ['C-25', '3784ULY', 'CAMIONETA', 'FUNCIONAMIENTO ALCALDIA MUNICIPAL', '000 0 001', '41', '113'],
            ['C-26', '3807XLK', 'CAMIONETA', 'FUNCIONAMIENTO ALCALDIA MUNICIPAL', '000 0 001', '41', '113'],
            ['C-27', '3807XID', 'CAMIONETA', 'FUNCIONAMIENTO ALCALDIA MUNICIPAL', '000 0 001', '41', '113'],
            ['C-28', '3847KAH', 'CAMIONETA', 'FUNCIONAMIENTO ALCALDIA MUNICIPAL', '000 0 001', '41', '113'],
            ['C-29', '3992NII', 'CAMIONETA', 'FUNCIONAMIENTO ALCALDIA MUNICIPAL', '000 0 001', '41', '113'],
            ['C-30', '4016PNF', 'CAMIONETA', 'FUNCIONAMIENTO ALCALDIA MUNICIPAL', '000 0 001', '41', '113'],
            ['C-32', '4034GND', 'CAMIONETA', 'FUNCIONAMIENTO ALCALDIA MUNICIPAL', '000 0 001', '41', '113'],
            ['C-37', '4421FYF', 'CAMIONETA', 'FUNCIONAMIENTO ALCALDIA MUNICIPAL', '000 0 001', '41', '113'],
            ['C-38', '4533DHT', 'CAMIONETA', 'FUNCIONAMIENTO ALCALDIA MUNICIPAL', '000 0 001', '41', '113'],
            ['C-40', '5642XRT', 'CAMIONETA', 'FUNCIONAMIENTO ALCALDIA MUNICIPAL', '000 0 001', '41', '113'],
            ['C-42', '5781DLF', 'CAMIONETA', 'FUNCIONAMIENTO ALCALDIA MUNICIPAL', '000 0 001', '41', '113'],
            ['C-44', '6283KTH', 'CAMIONETA', 'FUNCIONAMIENTO ALCALDIA MUNICIPAL', '000 0 001', '41', '113'],
            ['C-45', '6278GRF', 'CAMIONETA', 'FUNCIONAMIENTO ALCALDIA MUNICIPAL', '000 0 001', '41', '113'],
            ['CAR-01', '1188CLP', 'FURGONETA (CARRY)', 'FUNCIONAMIENTO ALCALDIA MUNICIPAL', '000 0 001', '41', '113'],
            ['CAR-02', '1188FCL', 'FURGONETA (CARRY)', 'UNIDAD DE GESTION DE RIESGOS', '310 0 012', '41', '113'],
            ['CAR-03', '2568ZZR', 'FURGONETA (CARRY)', 'FUNCIONAMIENTO ALCALDIA MUNICIPAL', '000 0 001', '41', '113'],
            ['M-08', '1961FXP', 'MOTOCICLETA', 'FUNCIONAMIENTO ALCALDIA MUNICIPAL', '000 0 001', '41', '113'],
            ['M-16', '1511CRU', 'MOTOCICLETA', 'FUNCIONAMIENTO ALCALDIA MUNICIPAL', '000 0 001', '41', '113'],
            ['M-17', '1961FHB', 'MOTOCICLETA', 'FUNCIONAMIENTO ALCALDIA MUNICIPAL', '000 0 001', '41', '113'],
            ['M-19', '1961GBD', 'MOTOCICLETA', 'FUNCIONAMIENTO ALCALDIA MUNICIPAL', '000 0 001', '20', '210'],
            ['M-21', '1961FDI', 'MOTOCICLETA', 'SERVICIOS Y EQUIPAMIENTO DE EDUCACIÓN', '210 0 001', '41', '113'],
            ['M-22', '1961FAY', 'MOTOCICLETA', 'FUNCIONAMIENTO ALCALDIA MUNICIPAL', '000 0 001', '41', '113'],
            ['M-23', '1961FCF', 'MOTOCICLETA', 'FUNCIONAMIENTO ALCALDIA MUNICIPAL', '000 0 001', '41', '113'],
            ['M-24', '1961GTH', 'MOTOCICLETA', 'FUNCIONAMIENTO ALCALDIA MUNICIPAL', '000 0 001', '41', '113'],
            ['M-27', '2941LKS', 'MOTOCICLETA', 'FUNCIONAMIENTO ALCALDIA MUNICIPAL', '000 0 001', '41', '113'],
            ['M-33', '2941LDT', 'MOTOCICLETA', 'FUNCIONAMIENTO ALCALDIA MUNICIPAL', '000 0 001', '41', '113'],
            ['M-34', '2776ADE', 'MOTOCICLETA', 'FUNCIONAMIENTO ALCALDIA MUNICIPAL', '000 0 001', '41', '113'],
            ['M-35', '2648NNE', 'MOTOCICLETA', 'UNIDAD DE GESTION DE RIESGOS', '310 0 012', '41', '113'],
            ['M-36', '2648NPH', 'MOTOCICLETA', 'FUNCIONAMIENTO ALCALDIA MUNICIPAL', '000 0 001', '41', '113'],
            ['M-46', '3572BHD', 'MOTOCICLETA', 'FUNCIONAMIENTO ALCALDIA MUNICIPAL', '000 0 001', '41', '113'],
            ['M-49', '3668ALK', 'MOTOCICLETA', 'FUNCIONAMIENTO ALCALDIA MUNICIPAL', '000 0 001', '41', '113'],
            ['M-50', '3668AKG', 'MOTOCICLETA', 'DIRECCION DE MEDIO AMBIENTE Y MADRE TIERRA', '130 0 010', '20', '210'],
            ['M-51', '3668AGU', 'MOTOCICLETA', 'UNIDAD DE TRANSPORTES Y MAQUINARIAS', '270 0 004', '41', '113'],
            ['M-52', '3668AHA', 'MOTOCICLETA', 'FUNCIONAMIENTO ALCALDIA MUNICIPAL', '000 0 001', '41', '113'],
            ['M-53', '3668ADH', 'MOTOCICLETA', 'FUNCIONAMIENTO ALCALDIA MUNICIPAL', '000 0 001', '41', '113'],
            ['M-54', '3668ABB', 'MOTOCICLETA', 'FUNCIONAMIENTO ALCALDIA MUNICIPAL', '000 0 001', '41', '113'],
            ['M-55', '3668ACE', 'MOTOCICLETA', 'UNIDAD DE GESTION DE RIESGOS', '310 0 012', '41', '113'],
            ['M-59', '3668AID', 'MOTOCICLETA', 'FUNCIONAMIENTO ALCALDIA MUNICIPAL', '000 0 001', '41', '113'],
            ['M-61', '3689DHK', 'MOTOCICLETA', 'FUNCIONAMIENTO ALCALDIA MUNICIPAL', '000 0 001', '20', '210'],
            ['M-62', '3689END', 'MOTOCICLETA', 'FUNCIONAMIENTO ALCALDIA MUNICIPAL', '000 0 001', '41', '113'],
            ['M-69', '3896ZID', 'MOTOCICLETA', 'UNIDAD DE PARQUES, JARDINES Y SERVICIOS FORESTALES', '131 0 010', '20', '210'],
            ['M-71', '4670RPB', 'MOTOCICLETA', 'UNIDAD DE SERVICIOS ELECTRICOS Y SEMAFORIZACION', '160 0 024', '20', '210'],
            ['M-72', '5540ARA', 'MOTOCICLETA', 'FUNCIONAMIENTO ALCALDIA MUNICIPAL', '000 0 001', '20', '210'],
            ['M-73', '5789ESX', 'MOTOCICLETA', 'FUNCIONAMIENTO ALCALDIA MUNICIPAL', '000 0 001', '41', '113'],
            ['M-74', '5790FFR', 'MOTOCICLETA', 'FUNCIONAMIENTO ALCALDIA MUNICIPAL', '000 0 001', '41', '113'],
            ['M-78', '6132GDI', 'MOTOCICLETA', 'FUNCIONAMIENTO ALCALDIA MUNICIPAL', '000 0 001', '41', '113'],
            ['M-80', '6125ZPD', 'MOTOCICLETA', 'FUNCIONAMIENTO ALCALDIA MUNICIPAL', '000 0 001', '41', '113'],
            ['M-81', '6125ZRG', 'MOTOCICLETA', 'FUNCIONAMIENTO ALCALDIA MUNICIPAL', '000 0 001', '41', '113'],
            ['M-85', '6125XDP', 'MOTOCICLETA', 'UNIDAD DE SERVICIOS ELECTRICOS Y SEMAFORIZACION', '160 0 024', '20', '210'],
            ['M-89', '6569FED', 'MOTOCICLETA', 'UNIDAD DE SERVICIOS ELECTRICOS Y SEMAFORIZACION', '160 0 024', '20', '210'],
            ['M-90', '6569FFG', 'MOTOCICLETA', 'SERVICIOS Y EQUIPAMIENTO DE EDUCACIÓN', '210 0 001', '41', '113'],
            ['M-91', '6605EKS', 'MOTOCICLETA', 'FUNCIONAMIENTO ALCALDIA MUNICIPAL', '000 0 001', '20', '210'],
            ['M-92', '6606PYY', 'MOTOCICLETA', 'UNIDAD DE TRANSPORTES Y MAQUINARIAS', '270 0 004', '41', '113'],
            ['M-93', '6773YIB', 'MOTOCICLETA', 'FUNCIONAMIENTO ALCALDIA MUNICIPAL', '000 0 001', '41', '113'],
            ['V-04', '1193AST', 'VAGONETA', 'FUNCIONAMIENTO ALCALDIA MUNICIPAL', '000 0 001', '41', '113'],
            ['V-09', '3445UGG', 'VAGONETA', 'FUNCIONAMIENTO ALCALDIA MUNICIPAL', '000 0 001', '41', '113'],
            ['V-13', '5744TSS', 'VAGONETA', 'FUNCIONAMIENTO ALCALDIA MUNICIPAL', '000 0 001', '41', '113'],
            ['V-14', '3876YNP', 'VAGONETA', 'FUNCIONAMIENTO ALCALDIA MUNICIPAL', '000 0 001', '41', '113'],
            ['VD-09', '2109KYX', 'VAGONETA', 'FUNCIONAMIENTO ALCALDIA MUNICIPAL', '000 0 001', '41', '113'],
            ['VD-10', '2211RNR', 'VAGONETA', 'FUNCIONAMIENTO ALCALDIA MUNICIPAL', '000 0 001', '41', '113'],
            ['VD-12', '5325DNI', 'VAGONETA', 'FUNCIONAMIENTO ALCALDIA MUNICIPAL', '000 0 001', '41', '113'],
            ['VD-14', '2511CKF', 'VAGONETA', 'FUNCIONAMIENTO ALCALDIA MUNICIPAL', '000 0 001', '41', '113'],
            ['VD-15', '4795GYN', 'VAGONETA', 'UNIDAD DE TRANSPORTES Y MAQUINARIAS', '270 0 004', '41', '113'],
            ['VD-16', '4124SIT', 'VAGONETA', 'FUNCIONAMIENTO ALCALDIA MUNICIPAL', '000 0 001', '20', '210'],
            ['VD-18', '4103CTY', 'VAGONETA', 'UNIDAD DE PARQUES, JARDINES Y SERVICIOS FORESTALES', '131 0 010', '20', '210'],
            ['VD-22', '4556RBI', 'VAGONETA', 'FUNCIONAMIENTO ALCALDIA MUNICIPAL', '000 0 001', '41', '113'],
            ['VD-23', '1708FTS', 'VAGONETA', 'UNIDAD DE TRANSPORTES Y MAQUINARIAS', '270 0 004', '41', '113'],
            ['VD-25', '2534TCX', 'VAGONETA', 'FUNCIONAMIENTO ALCALDIA MUNICIPAL', '000 0 001', '41', '113'],
            ['VD-27', '4148CHI', 'VAGONETA', 'FUNCIONAMIENTO ALCALDIA MUNICIPAL', '000 0 001', '41', '113'],
            ['AP-01', '2120CAG', 'AMBULACIA POLICIAL', 'UNIDAD DE SEGURIDAD CIUDADANA', '331 0 016', '41', '119'],
            ['AP-02', '5747EIG', 'AMBULACIA POLICIAL', 'UNIDAD DE SEGURIDAD CIUDADANA', '331 0 016', '41', '119'],
            ['CP-02', '2101TXC', 'CAMIONETA', 'UNIDAD DE SEGURIDAD CIUDADANA', '331 0 016', '41', '113'],
            ['CP-03', '2101TYF', 'CAMIONETA', 'UNIDAD DE SEGURIDAD CIUDADANA', '331 0 016', '41', '113'],
            ['CP-04', '2263ULT', 'CAMIONETA', 'UNIDAD DE SEGURIDAD CIUDADANA', '331 0 016', '41', '113'],
            ['CP-06', '2893DSC', 'CAMIONETA', 'UNIDAD DE SEGURIDAD CIUDADANA', '331 0 016', '41', '113'],
            ['CP-07', '2893DTF', 'CAMIONETA', 'UNIDAD DE SEGURIDAD CIUDADANA', '331 0 016', '41', '113'],
            ['CP-08', '2981BPP', 'CAMIONETA', 'UNIDAD DE SEGURIDAD CIUDADANA', '331 0 016', '41', '113'],
            ['CP-09', '3063KXF', 'CAMIONETA', 'UNIDAD DE SEGURIDAD CIUDADANA', '331 0 016', '41', '113'],
            ['CP-10', '3063KUC', 'CAMIONETA', 'UNIDAD DE SEGURIDAD CIUDADANA', '331 0 016', '41', '113'],
            ['CP-11', '3063KYI', 'CAMIONETA', 'UNIDAD DE SEGURIDAD CIUDADANA', '331 0 016', '41', '113'],
            ['CP-13', '3447DRK', 'CAMIONETA', 'UNIDAD DE SEGURIDAD CIUDADANA', '331 0 016', '41', '119'],
            ['CP-14', '3447DPG', 'CAMIONETA', 'UNIDAD DE SEGURIDAD CIUDADANA', '331 0 016', '41', '119'],
            ['CP-16', '3623RXL', 'CAMIONETA', 'UNIDAD DE SEGURIDAD CIUDADANA', '331 0 016', '41', '119'],
            ['CP-17', '3623RYR', 'CAMIONETA', 'UNIDAD DE SEGURIDAD CIUDADANA', '331 0 016', '41', '119'],
            ['CP-20', '4810CXS', 'CAMIONETA', 'UNIDAD DE SEGURIDAD CIUDADANA', '331 0 016', '41', '119'],
            ['CP-21', '5185XPU', 'CAMIONETA', 'UNIDAD DE SEGURIDAD CIUDADANA', '331 0 016', '41', '119'],
            ['CP-22', '5202TBF', 'CAMIONETA', 'UNIDAD DE SEGURIDAD CIUDADANA', '331 0 016', '41', '119'],
            ['CP-23', '6278GSI', 'CAMIONETA', 'UNIDAD DE SEGURIDAD CIUDADANA', '331 0 016', '41', '119'],
            ['CP-24', '6484PCD', 'VAGONETA', 'UNIDAD DE SEGURIDAD CIUDADANA', '331 0 016', '41', '119'],
            ['MP-01', '1961FGX', 'MOTOCICLETA', 'UNIDAD DE SEGURIDAD CIUDADANA', '331 0 016', '41', '119'],
            ['MP-21', '3171EXX', 'MOTOCICLETA', 'UNIDAD DE SEGURIDAD CIUDADANA', '331 0 016', '41', '119'],
            ['MP-26', '3171GSL', 'MOTOCICLETA', 'UNIDAD DE SEGURIDAD CIUDADANA', '331 0 016', '41', '119'],
            ['MP-27', '3171GRH', 'MOTOCICLETA', 'UNIDAD DE SEGURIDAD CIUDADANA', '331 0 016', '41', '119'],
            ['MP-34', '3310FEA', 'MOTOCICLETA', 'UNIDAD DE SEGURIDAD CIUDADANA', '331 0 016', '41', '119'],
            ['MP-37', '3310FBL', 'MOTOCICLETA', 'UNIDAD DE SEGURIDAD CIUDADANA', '331 0 016', '41', '119'],
            ['MP-38', '3310FAH', 'MOTOCICLETA', 'UNIDAD DE SEGURIDAD CIUDADANA', '331 0 016', '41', '119'],
            ['MP-41', '3533THH', 'MOTOCICLETA', 'UNIDAD DE SEGURIDAD CIUDADANA', '331 0 016', '41', '119'],
            ['MP-50', '3533TGE', 'MOTOCICLETA', 'UNIDAD DE SEGURIDAD CIUDADANA', '331 0 016', '41', '119'],
            ['MP-51', '3533TCN', 'MOTOCICLETA', 'UNIDAD DE SEGURIDAD CIUDADANA', '331 0 016', '41', '119'],
            ['MP-52', '3533TAF', 'MOTOCICLETA', 'UNIDAD DE SEGURIDAD CIUDADANA', '331 0 016', '41', '119'],
            ['MP-54', '4885RNL', 'MOTOCICLETA', 'UNIDAD DE SEGURIDAD CIUDADANA', '331 0 016', '41', '119'],
            ['MP-55', '4891ECP', 'MOTOCICLETA', 'UNIDAD DE SEGURIDAD CIUDADANA', '331 0 016', '41', '119'],
            ['MP-56', '4891EDT', 'MOTOCICLETA', 'UNIDAD DE SEGURIDAD CIUDADANA', '331 0 016', '41', '119'],
            ['MP-57', '4962DAH', 'MOTOCICLETA', 'UNIDAD DE SEGURIDAD CIUDADANA', '331 0 016', '41', '119'],
            ['MP-58', '4962DBL', 'MOTOCICLETA', 'UNIDAD DE SEGURIDAD CIUDADANA', '331 0 016', '41', '119'],
            ['MP-59', '4962DCR', 'MOTOCICLETA', 'UNIDAD DE SEGURIDAD CIUDADANA', '331 0 016', '41', '119'],
            ['MP-60', '5557XZX', 'MOTOCICLETA', 'UNIDAD DE SEGURIDAD CIUDADANA', '331 0 016', '41', '119'],
            ['MP-61', '5557YAY', 'MOTOCICLETA', 'UNIDAD DE SEGURIDAD CIUDADANA', '331 0 016', '41', '119'],
            ['MP-62', '5557XUI', 'MOTOCICLETA', 'UNIDAD DE SEGURIDAD CIUDADANA', '331 0 016', '41', '119'],
            ['MP-63', '5557XXN', 'MOTOCICLETA', 'UNIDAD DE SEGURIDAD CIUDADANA', '331 0 016', '41', '119'],
            ['MP-64', '5557XYS', 'MOTOCICLETA', 'UNIDAD DE SEGURIDAD CIUDADANA', '331 0 016', '41', '119'],
            ['MP-65', '5557YBC', 'MOTOCICLETA', 'UNIDAD DE SEGURIDAD CIUDADANA', '331 0 016', '41', '119'],
            ['MP-66', '5890GDN', 'MOTOCICLETA', 'UNIDAD DE SEGURIDAD CIUDADANA', '331 0 016', '41', '119'],
            ['MP-67', '6111CKU', 'MOTOCICLETA', 'UNIDAD DE SEGURIDAD CIUDADANA', '331 0 016', '41', '119'],
            ['MP-68', '6111CIR', 'MOTOCICLETA', 'UNIDAD DE SEGURIDAD CIUDADANA', '331 0 016', '41', '119'],
            ['MP-69', '6518HHA', 'MOTOCICLETA', 'UNIDAD DE SEGURIDAD CIUDADANA', '331 0 016', '41', '119'],
            ['MP-70', '6518HGU', 'MOTOCICLETA', 'UNIDAD DE SEGURIDAD CIUDADANA', '331 0 016', '41', '119'],
            ['MP-71', '6518HFR', 'MOTOCICLETA', 'UNIDAD DE SEGURIDAD CIUDADANA', '331 0 016', '41', '119'],
            ['MP-72', '6518HEL', 'MOTOCICLETA', 'UNIDAD DE SEGURIDAD CIUDADANA', '331 0 016', '41', '119'],
            ['VP-03', '5221YUH', 'VAGONETA POLICIAL', 'UNIDAD DE SEGURIDAD CIUDADANA', '331 0 016', '41', '119'],
            ['VP-04', '5677ZRD', 'VAGONETA POLICIAL', 'UNIDAD DE SEGURIDAD CIUDADANA', '331 0 016', '41', '119'],
        ];

        foreach ($vehiculos as [$codigo, $placa, $tipo, $unidad, $ap, $ff, $of]) {
            $claveAp = $ap.'|'.$ff.'|'.$of;
            Vehiculo::updateOrCreate(
                ['codigo' => $codigo],
                [
                    'placa' => $placa,
                    'tipo' => $tipo,
                    'unidad_id' => $unidades[$unidad]->id,
                    'apertura_programatica_id' => $aperturas[$claveAp]->id,
                    'estado' => true,
                ]
            );
        }
    }
}