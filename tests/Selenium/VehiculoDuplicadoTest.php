<?php

require __DIR__ . '/../../vendor/autoload.php';

use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\WebDriverSelect;
use Facebook\WebDriver\WebDriverWait;

/*
|--------------------------------------------------------------------------
| PA-VEH-02
|--------------------------------------------------------------------------
| Módulo: Parque Automotor
| Submódulo: Vehículos
| Prueba: Registrar vehículo con placa duplicada
|
| Objetivo:
| Verificar que SIGECOM no permita registrar dos vehículos
| con la misma placa.
|--------------------------------------------------------------------------
*/

$host = 'http://localhost:9515';
$urlSistema = 'http://127.0.0.1:8000';

$correoAdmin = 'admin@gmail.com';
$passwordAdmin = getenv('SIGECOM_TEST_PASSWORD');

if (!$passwordAdmin) {
    echo "ERROR: No se definio SIGECOM_TEST_PASSWORD.\n";
    exit(1);
}

/*
|--------------------------------------------------------------------------
| DATOS DE PRUEBA
|--------------------------------------------------------------------------
*/

$codigoUnico = date('His');

$codigo1 = 'DUPA' . $codigoUnico;
$codigo2 = 'DUPB' . $codigoUnico;

/*
| La placa será exactamente la misma en ambos registros.
*/

$placaDuplicada = 'DUP-' . $codigoUnico;

$tipo = 'CAMIONETA';
$industria = 'JAPONESA';
$marca = 'TOYOTA';
$color = 'BLANCO';

$options = new ChromeOptions();

$capabilities = DesiredCapabilities::chrome();

$capabilities->setCapability(
    ChromeOptions::CAPABILITY,
    $options
);

$driver = null;

echo "\n";
echo "====================================================\n";
echo " PA-VEH-02 - VEHICULO DUPLICADO\n";
echo "====================================================\n\n";

try {

    /*
    |--------------------------------------------------------------------------
    | 1. ABRIR CHROME
    |--------------------------------------------------------------------------
    */

    echo "[1] Abriendo Google Chrome...\n";

    $driver = RemoteWebDriver::create(
        $host,
        $capabilities
    );

    $driver->manage()
        ->window()
        ->maximize();

    $wait = new WebDriverWait($driver, 10);

    /*
    |--------------------------------------------------------------------------
    | 2. LOGIN
    |--------------------------------------------------------------------------
    */

    echo "[2] Iniciando sesion en SIGECOM...\n";

    $driver->get(
        $urlSistema . '/login'
    );

    $wait->until(
        WebDriverExpectedCondition::presenceOfElementLocated(
            WebDriverBy::name('correo')
        )
    )->sendKeys($correoAdmin);

    $driver->findElement(
        WebDriverBy::name('contrasena')
    )->sendKeys($passwordAdmin);

    $botonLogin = $driver->findElement(
        WebDriverBy::cssSelector(
            'button[type="submit"]'
        )
    );

    $driver->executeScript(
        "arguments[0].scrollIntoView({block:'center'});",
        [$botonLogin]
    );

    sleep(1);

    $driver->executeScript(
        "arguments[0].click();",
        [$botonLogin]
    );

    $wait->until(
        WebDriverExpectedCondition::not(
            WebDriverExpectedCondition::urlContains('/login')
        )
    );

    echo "    OK - Inicio de sesion correcto.\n";

    /*
    |--------------------------------------------------------------------------
    | FUNCIÓN PARA LLENAR VEHÍCULO
    |--------------------------------------------------------------------------
    */

    $llenarVehiculo = function (
        $codigo,
        $placa
    ) use (
        $driver,
        $wait,
        $urlSistema,
        $tipo,
        $industria,
        $marca,
        $color
    ) {

        $driver->get(
            $urlSistema . '/vehiculos/create'
        );

        $wait->until(
            WebDriverExpectedCondition::presenceOfElementLocated(
                WebDriverBy::name('codigo')
            )
        );

        /*
        | Código
        */

        $driver->findElement(
            WebDriverBy::name('codigo')
        )->sendKeys($codigo);

        /*
        | Placa
        */

        $driver->findElement(
            WebDriverBy::name('placa')
        )->sendKeys($placa);

        /*
        | Tipo
        */

        $driver->findElement(
            WebDriverBy::name('tipo')
        )->sendKeys($tipo);

        /*
        | Industria
        */

        $driver->findElement(
            WebDriverBy::name('industria')
        )->sendKeys($industria);

        /*
        | Marca
        */

        $driver->findElement(
            WebDriverBy::name('marca')
        )->sendKeys($marca);

        /*
        | Color
        */

        $driver->findElement(
            WebDriverBy::name('color')
        )->sendKeys($color);

        /*
        | Unidad
        */

        $selectUnidad = new WebDriverSelect(
            $driver->findElement(
                WebDriverBy::name('unidad_id')
            )
        );

        $opcionesUnidad = $selectUnidad->getOptions();

        if (count($opcionesUnidad) <= 1) {
            throw new Exception(
                'No existen unidades registradas.'
            );
        }

        $selectUnidad->selectByIndex(1);

        /*
        | Apertura Programática
        */

        $selectApertura = new WebDriverSelect(
            $driver->findElement(
                WebDriverBy::name(
                    'apertura_programatica_id'
                )
            )
        );

        $opcionesApertura =
            $selectApertura->getOptions();

        if (count($opcionesApertura) <= 1) {
            throw new Exception(
                'No existen Aperturas Programaticas.'
            );
        }

        $selectApertura->selectByIndex(1);

        /*
        | Estado activo
        */

        $selectEstado = new WebDriverSelect(
            $driver->findElement(
                WebDriverBy::name('estado')
            )
        );

        $selectEstado->selectByValue('1');

        /*
        | Guardar
        */

        $botonGuardar = $driver->findElement(
            WebDriverBy::cssSelector(
                'button.btn-primary'
            )
        );

        $driver->executeScript(
            "arguments[0].scrollIntoView({block:'center'});",
            [$botonGuardar]
        );

        sleep(1);

        $driver->executeScript(
            "arguments[0].click();",
            [$botonGuardar]
        );

        sleep(3);
    };

    /*
    |--------------------------------------------------------------------------
    | 3. REGISTRAR PRIMER VEHÍCULO
    |--------------------------------------------------------------------------
    */

    echo "[3] Registrando primer vehiculo...\n";
    echo "    Codigo: {$codigo1}\n";
    echo "    Placa: {$placaDuplicada}\n";

    $llenarVehiculo(
        $codigo1,
        $placaDuplicada
    );

    /*
    | Comprobamos que el primer vehículo realmente
    | haya sido registrado.
    */

    $erroresPrimerRegistro = $driver->findElements(
        WebDriverBy::cssSelector('.alert-danger')
    );

    if (count($erroresPrimerRegistro) > 0) {

        throw new Exception(
            "No se pudo crear el primer vehiculo.\n" .
            $erroresPrimerRegistro[0]->getText()
        );
    }

    echo "    OK - Primer vehiculo registrado.\n";

    /*
    |--------------------------------------------------------------------------
    | 4. INTENTAR REGISTRAR OTRO CON LA MISMA PLACA
    |--------------------------------------------------------------------------
    */

    echo "\n[4] Intentando registrar otro vehiculo...\n";
    echo "    Codigo diferente: {$codigo2}\n";
    echo "    Placa repetida: {$placaDuplicada}\n";

    $llenarVehiculo(
        $codigo2,
        $placaDuplicada
    );

    /*
    |--------------------------------------------------------------------------
    | 5. BUSCAR MENSAJE DE VALIDACIÓN
    |--------------------------------------------------------------------------
    */

    echo "[5] Verificando rechazo de placa duplicada...\n";

    $errores = $driver->findElements(
        WebDriverBy::cssSelector('.alert-danger')
    );

    /*
    |--------------------------------------------------------------------------
    | 6. RESULTADO
    |--------------------------------------------------------------------------
    */

    if (count($errores) > 0) {

        $mensajeError = trim(
            $errores[0]->getText()
        );

        echo "\n";
        echo "====================================================\n";
        echo " PRUEBA APROBADA\n";
        echo "====================================================\n";

        echo "SIGECOM rechazo correctamente el vehiculo duplicado.\n";
        echo "Placa duplicada: {$placaDuplicada}\n";

        echo "\nMensaje mostrado por SIGECOM:\n";
        echo $mensajeError . "\n";

    } else {

        echo "\n";
        echo "====================================================\n";
        echo " PRUEBA FALLIDA\n";
        echo "====================================================\n";

        echo "SIGECOM no mostro un error al intentar\n";
        echo "registrar la placa duplicada.\n";

        echo "\nPlaca utilizada: {$placaDuplicada}\n";

        echo "URL actual: ";
        echo $driver->getCurrentURL();
        echo "\n";
    }

    /*
    |--------------------------------------------------------------------------
    | DEJAR RESULTADO VISIBLE
    |--------------------------------------------------------------------------
    */

    sleep(10);

} catch (Exception $e) {

    echo "\n";
    echo "====================================================\n";
    echo " ERROR EN LA PRUEBA\n";
    echo "====================================================\n";

    echo $e->getMessage() . "\n";

    if ($driver !== null) {

        echo "\nURL actual: ";
        echo $driver->getCurrentURL();
        echo "\n";
    }

    sleep(5);

} finally {

    if ($driver !== null) {
        $driver->quit();
    }

    echo "\nPrueba finalizada.\n";
}