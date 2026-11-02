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
| PA-VEH-01
|--------------------------------------------------------------------------
| Módulo: Parque Automotor
| Submódulo: Vehículos
| Prueba: Registrar vehículo válido
|
| Objetivo:
| Verificar que SIGECOM permita registrar correctamente un vehículo
| cuando se ingresan datos válidos.
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
| Se genera un código y una placa diferentes en cada ejecución para
| evitar duplicados.
*/

$codigoUnico = date('His');

$codigo = 'SEL' . $codigoUnico;
$placa = 'SEL-' . $codigoUnico;

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
echo " PA-VEH-01 - REGISTRAR VEHICULO VALIDO\n";
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
    | 2. ABRIR LOGIN
    |--------------------------------------------------------------------------
    */

    echo "[2] Abriendo SIGECOM...\n";

    $driver->get(
        $urlSistema . '/login'
    );

    /*
    |--------------------------------------------------------------------------
    | 3. INGRESAR CREDENCIALES
    |--------------------------------------------------------------------------
    */

    echo "[3] Ingresando credenciales...\n";

    $wait->until(
        WebDriverExpectedCondition::presenceOfElementLocated(
            WebDriverBy::name('correo')
        )
    )->sendKeys($correoAdmin);

    $driver->findElement(
        WebDriverBy::name('contrasena')
    )->sendKeys($passwordAdmin);

    /*
    |--------------------------------------------------------------------------
    | 4. INICIAR SESIÓN
    |--------------------------------------------------------------------------
    */

    echo "[4] Iniciando sesion...\n";

    $botonLogin = $wait->until(
        WebDriverExpectedCondition::presenceOfElementLocated(
            WebDriverBy::cssSelector('button[type="submit"]')
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
    | 5. ABRIR NUEVO VEHÍCULO
    |--------------------------------------------------------------------------
    */

    echo "[5] Abriendo formulario Nuevo vehiculo...\n";

    $driver->get(
        $urlSistema . '/vehiculos/create'
    );

    $wait->until(
        WebDriverExpectedCondition::presenceOfElementLocated(
            WebDriverBy::name('codigo')
        )
    );

    echo "    OK - Formulario cargado.\n";

    /*
    |--------------------------------------------------------------------------
    | 6. CÓDIGO
    |--------------------------------------------------------------------------
    */

    echo "[6] Ingresando codigo: {$codigo}\n";

    $driver->findElement(
        WebDriverBy::name('codigo')
    )->sendKeys($codigo);

    /*
    |--------------------------------------------------------------------------
    | 7. PLACA
    |--------------------------------------------------------------------------
    */

    echo "[7] Ingresando placa: {$placa}\n";

    $driver->findElement(
        WebDriverBy::name('placa')
    )->sendKeys($placa);

    /*
    |--------------------------------------------------------------------------
    | 8. DATOS DEL VEHÍCULO
    |--------------------------------------------------------------------------
    */

    echo "[8] Ingresando datos del vehiculo...\n";

    $driver->findElement(
        WebDriverBy::name('tipo')
    )->sendKeys($tipo);

    $driver->findElement(
        WebDriverBy::name('industria')
    )->sendKeys($industria);

    $driver->findElement(
        WebDriverBy::name('marca')
    )->sendKeys($marca);

    $driver->findElement(
        WebDriverBy::name('color')
    )->sendKeys($color);

    /*
    |--------------------------------------------------------------------------
    | 9. SELECCIONAR UNIDAD
    |--------------------------------------------------------------------------
    | La opción 0 es "Seleccione..."
    | Seleccionamos automáticamente la primera unidad real.
    */

    echo "[9] Seleccionando unidad...\n";

    $selectUnidad = new WebDriverSelect(
        $driver->findElement(
            WebDriverBy::name('unidad_id')
        )
    );

    $opcionesUnidad = $selectUnidad->getOptions();

    if (count($opcionesUnidad) <= 1) {
        throw new Exception(
            'No existen unidades registradas para realizar la prueba.'
        );
    }

    $selectUnidad->selectByIndex(1);

    $unidadSeleccionada =
        trim($selectUnidad->getFirstSelectedOption()->getText());

    echo "    Unidad: {$unidadSeleccionada}\n";

    /*
    |--------------------------------------------------------------------------
    | 10. SELECCIONAR APERTURA PROGRAMÁTICA
    |--------------------------------------------------------------------------
    */

    echo "[10] Seleccionando Apertura Programatica...\n";

    $selectApertura = new WebDriverSelect(
        $driver->findElement(
            WebDriverBy::name('apertura_programatica_id')
        )
    );

    $opcionesApertura = $selectApertura->getOptions();

    if (count($opcionesApertura) <= 1) {
        throw new Exception(
            'No existen Aperturas Programaticas para realizar la prueba.'
        );
    }

    $selectApertura->selectByIndex(1);

    $aperturaSeleccionada =
        trim($selectApertura->getFirstSelectedOption()->getText());

    echo "    Apertura seleccionada correctamente.\n";

    /*
    |--------------------------------------------------------------------------
    | 11. ESTADO
    |--------------------------------------------------------------------------
    */

    echo "[11] Seleccionando estado Activo...\n";

    $selectEstado = new WebDriverSelect(
        $driver->findElement(
            WebDriverBy::name('estado')
        )
    );

    $selectEstado->selectByValue('1');

    /*
    |--------------------------------------------------------------------------
    | 12. GUARDAR
    |--------------------------------------------------------------------------
    */

    echo "[12] Presionando Guardar...\n";

    $botonGuardar = $driver->findElement(
        WebDriverBy::cssSelector(
            'button.btn-primary'
        )
    );

    $driver->executeScript(
        "arguments[0].scrollIntoView({block:'center'});",
        [$botonGuardar]
    );

    sleep(2);

    $driver->executeScript(
        "arguments[0].click();",
        [$botonGuardar]
    );

    /*
    |--------------------------------------------------------------------------
    | 13. ESPERAR RESPUESTA
    |--------------------------------------------------------------------------
    */

    sleep(3);

    echo "[13] Verificando registro...\n";

    /*
    |--------------------------------------------------------------------------
    | 14. COMPROBAR RESULTADO
    |--------------------------------------------------------------------------
    */

    $contenido = strtolower(
        $driver->getPageSource()
    );

    $codigoEncontrado = str_contains(
        $contenido,
        strtolower($codigo)
    );

    $placaEncontrada = str_contains(
        $contenido,
        strtolower($placa)
    );

    if ($codigoEncontrado || $placaEncontrada) {

        echo "\n";
        echo "====================================================\n";
        echo " PRUEBA APROBADA\n";
        echo "====================================================\n";

        echo "El vehiculo fue registrado correctamente.\n\n";

        echo "Codigo: {$codigo}\n";
        echo "Placa: {$placa}\n";
        echo "Tipo: {$tipo}\n";
        echo "Industria: {$industria}\n";
        echo "Marca: {$marca}\n";
        echo "Color: {$color}\n";
        echo "Unidad: {$unidadSeleccionada}\n";
        echo "Estado: Activo\n";

    } else {

        echo "\n";
        echo "====================================================\n";
        echo " PRUEBA NO CONFIRMADA\n";
        echo "====================================================\n";

        echo "Selenium envio el formulario pero no encontro\n";
        echo "el codigo o la placa en la pagina resultante.\n";

        echo "\nURL actual: ";
        echo $driver->getCurrentURL();
        echo "\n";

        /*
        | Si existe un error de validación, lo mostramos.
        */

        $errores = $driver->findElements(
            WebDriverBy::cssSelector('.alert-danger')
        );

        if (count($errores) > 0) {
            echo "\nMensaje del sistema:\n";
            echo $errores[0]->getText();
            echo "\n";
        }
    }

    /*
    |--------------------------------------------------------------------------
    | DEJAR CHROME ABIERTO PARA EVIDENCIA
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