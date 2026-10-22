<?php

// Campos exclusivamente visuales y provisionales; no representan un esquema de base de datos.
return [
    'opciones' => [
        'vehiculo' => ['C-29 | 3992NII', 'C-30 | 4821ABC', 'M-12 | 2536XYZ'],
        'tipo_vehiculo' => ['Camioneta', 'Camión', 'Motocicleta'],
        'combustible' => ['Gasolina', 'Diésel'],
        'unidad' => ['Administración central', 'Obras públicas', 'Policía'],
        'apertura' => ['Administración central', 'Mantenimiento vial', 'Seguridad ciudadana'],
        'fuente' => ['Fuente de ejemplo A', 'Fuente de ejemplo B'],
        'organismo' => ['Organismo de ejemplo A', 'Organismo de ejemplo B'],
        'conductor' => ['Ana Pérez', 'Luis Rojas', 'María Flores'],
        'estacion' => ['Estación de ejemplo Norte', 'Estación de ejemplo Sur'],
        'rol' => ['Administrador', 'Consulta'],
        'vale' => ['V-001', 'V-002', 'V-003'],
    ],
    // Nombre del campo => [etiqueta, tipo u opciones].
    'campos' => [
        'usuarios' => ['nombre' => ['Nombre', 'text'], 'correo' => ['Correo electrónico', 'email'], 'rol' => ['Rol', 'rol']],
        'roles' => ['nombre' => ['Nombre', 'text'], 'descripcion' => ['Descripción', 'text']],
        'permisos' => ['nombre' => ['Nombre', 'text'], 'descripcion' => ['Descripción', 'text']],
        'aperturas-programaticas' => ['codigo' => ['Código', 'text'], 'nombre' => ['Descripción', 'text'], 'fuente' => ['Fuente de Financiamiento', 'fuente'], 'organismo' => ['Organismo Financiador', 'organismo']],
        'precios' => ['combustible' => ['Tipo de combustible', 'combustible'], 'estacion' => ['Estación de Servicio', 'estacion'], 'precio' => ['Costo unitario (Bs/L)', 'number'], 'fecha' => ['Vigente desde', 'date']],
        'vehiculos' => ['codigo' => ['Código', 'text'], 'placa' => ['Placa', 'text'], 'tipo_vehiculo' => ['Tipo de vehículo', 'tipo_vehiculo'], 'marca' => ['Marca', 'text'], 'industria' => ['Industria', 'text'], 'color' => ['Color', 'text'], 'unidad' => ['Unidad', 'unidad'], 'apertura' => ['Apertura Programática', 'apertura']],
        'conductores' => ['nombre' => ['Nombre completo', 'text'], 'licencia' => ['Licencia', 'text'], 'unidad' => ['Unidad', 'unidad']],
        'asignaciones' => ['vehiculo' => ['Vehículo', 'vehiculo'], 'conductor' => ['Conductor', 'conductor'], 'fecha' => ['Fecha', 'date']],
        'solicitudes' => ['vehiculo' => ['Vehículo', 'vehiculo'], 'fecha' => ['Fecha', 'date'], 'combustible' => ['Tipo de combustible', 'combustible'], 'litros' => ['Litros solicitados', 'number']],
        'vales' => ['codigo' => ['Número de vale', 'text'], 'vehiculo' => ['Vehículo', 'vehiculo'], 'fecha' => ['Fecha', 'date'], 'litros' => ['Litros autorizados', 'number']],
        'cargas-combustible' => ['vale' => ['Vale', 'vale'], 'vehiculo' => ['Vehículo', 'vehiculo'], 'fecha' => ['Fecha', 'date'], 'estacion' => ['Estación de Servicio', 'estacion'], 'combustible' => ['Tipo de combustible', 'combustible'], 'litros' => ['Litros cargados', 'number']],
        'facturas' => ['numero' => ['Número de factura', 'text'], 'fecha' => ['Fecha', 'date'], 'estacion' => ['Estación de Servicio', 'estacion'], 'monto' => ['Monto (Bs)', 'number']],
        'kilometraje' => ['vehiculo' => ['Vehículo', 'vehiculo'], 'fecha' => ['Fecha', 'date'], 'kilometraje' => ['Kilometraje', 'number']],
        'partes-diarios' => ['vehiculo' => ['Vehículo', 'vehiculo'], 'conductor' => ['Conductor', 'conductor'], 'fecha' => ['Fecha', 'date'], 'descripcion' => ['Detalle de actividad', 'text']],
    ],
    'ejemplos' => [
        'fuentes-financiamiento' => ['Fuente de ejemplo A', 'Fuente de ejemplo B'],
        'organismos-financiadores' => ['Organismo de ejemplo A', 'Organismo de ejemplo B'],
        'unidades' => ['Administración central', 'Obras públicas', 'Policía'],
        'tipos-vehiculo' => ['Camioneta', 'Camión', 'Motocicleta'],
        'tipos-combustible' => ['Gasolina', 'Diésel'],
        'estaciones-servicio' => ['Estación de ejemplo Norte', 'Estación de ejemplo Sur'],
    ],
];
