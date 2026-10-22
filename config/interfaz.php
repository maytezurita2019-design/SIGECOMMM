<?php

// Navegación provisional; no define tablas ni campos del dominio.
return [
    'seguridad' => ['titulo' => 'Seguridad y configuración', 'icono' => 'bi bi-shield-check', 'secciones' => [
        'usuarios' => 'Usuarios', 'roles' => 'Roles', 'permisos' => 'Permisos', 'auditoria' => 'Auditoría',
    ]],
    'catalogos' => ['titulo' => 'Catálogos y parámetros', 'icono' => 'bi bi-collection', 'secciones' => [
        'aperturas-programaticas' => 'Aperturas Programáticas', 'fuentes-financiamiento' => 'Fuentes de Financiamiento',
        'organismos-financiadores' => 'Organismos Financiadores', 'unidades' => 'Unidades / Dependencias',
        'tipos-vehiculo' => 'Tipos de Vehículo', 'tipos-combustible' => 'Tipos de Combustible',
        'estaciones-servicio' => 'Estaciones de Servicio', 'precios' => 'Precios / Costos Unitarios',
    ]],
    'parque-automotor' => ['titulo' => 'Parque automotor', 'icono' => 'bi bi-truck', 'secciones' => [
        'vehiculos' => 'Vehículos', 'conductores' => 'Conductores', 'asignaciones' => 'Asignaciones',
    ]],
    'combustible' => ['titulo' => 'Gestión y control de combustible', 'icono' => 'bi bi-fuel-pump', 'secciones' => [
        'solicitudes' => 'Solicitudes', 'vales' => 'Vales', 'cargas-combustible' => 'Cargas de Combustible',
        'facturas' => 'Facturas', 'kilometraje' => 'Kilometraje', 'partes-diarios' => 'Partes Diarios', 'policia' => 'Policía',
    ]],
    'reportes' => ['titulo' => 'Reportes, historial y seguimiento', 'icono' => 'bi bi-bar-chart-line', 'secciones' => [
        'gam' => 'Reportes GAM', 'policia' => 'Reportes Policía', 'vehiculo' => 'Reportes por Vehículo',
        'apertura-programatica' => 'Reportes por Apertura Programática', 'historial' => 'Historial',
        'pdf' => 'Exportación PDF', 'excel' => 'Exportación Excel',
    ]],
    'api-consulta' => ['titulo' => 'API REST de consulta', 'icono' => 'bi bi-braces', 'secciones' => [
        'presentacion' => 'Consulta · fase posterior',
    ]],
];
