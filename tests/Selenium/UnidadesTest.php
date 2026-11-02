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
| PA-UNI-01
|--------------------------------------------------------------------------
| Módulo: Parque Automotor
| Submódulo: Unidades
| Prueba: Registrar unidad válida
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

$codigo = date('His');

$nombreUnidad = 'UNIDAD SELENIUM ' . $codigo;
$siglaUnidad = 'US' . $codigo;
$tipoUnidad = 'DIRECCION';
$descripcionUnidad = 'Unidad registrada automaticamente mediante Selenium';

$options = new ChromeOptions();

$capabilities = DesiredCapabilities::chrome();

$capabilities->setCapability(
    ChromeOptions::CAPABILITY,
    $options
);

$driver = null;

echo "\n";
echo "====================================================\n";
echo " PA-UNI-01 - REGISTRAR UNIDAD VALIDA\n";
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

    echo "[2] Abriendo SIGECOM...\n";

    $driver->get(
        $urlSistema . '/login'
    );

    /*
    |--------------------------------------------------------------------------
    | 3. CREDENCIALES
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
    | 5. ABRIR NUEVA UNIDAD
    |--------------------------------------------------------------------------
    */

    echo "[5] Abriendo formulario Nueva unidad...\n";

    $driver->get(
        $urlSistema . '/unidades/create'
    );

    $wait->until(
        WebDriverExpectedCondition::presenceOfElementLocated(
            WebDriverBy::name('nombre')
        )
    );

    echo "    OK - Formulario cargado.\n";

    /*
    |--------------------------------------------------------------------------
    | 6. NOMBRE
    |--------------------------------------------------------------------------
    */

    echo "[6] Ingresando nombre...\n";

    $driver->findElement(
        WebDriverBy::name('nombre')
    )->sendKeys($nombreUnidad);

    /*
    |--------------------------------------------------------------------------
    | 7. SIGLA
    |--------------------------------------------------------------------------
    */

    echo "[7] Ingresando sigla...\n";

    $driver->findElement(
        WebDriverBy::name('sigla')
    )->sendKeys($siglaUnidad);

    /*
    |--------------------------------------------------------------------------
    | 8. TIPO
    |--------------------------------------------------------------------------
    */

    echo "[8] Ingresando tipo...\n";

    $driver->findElement(
        WebDriverBy::name('tipo')
    )->sendKeys($tipoUnidad);

    /*
    |--------------------------------------------------------------------------
    | 9. DESCRIPCIÓN
    |--------------------------------------------------------------------------
    */

    echo "[9] Ingresando descripcion...\n";

    $driver->findElement(
        WebDriverBy::name('descripcion')
    )->sendKeys($descripcionUnidad);

    /*
    |--------------------------------------------------------------------------
    | 10. ESTADO
    |--------------------------------------------------------------------------
    */

    echo "[10] Seleccionando estado Activo...\n";

    $estado = new WebDriverSelect(
        $driver->findElement(
            WebDriverBy::name('estado')
        )
    );

    $estado->selectByValue('1');

    /*
    |--------------------------------------------------------------------------
    | 11. GUARDAR
    |--------------------------------------------------------------------------
    */

    echo "[11] Presionando Guardar...\n";

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

    /*
    |--------------------------------------------------------------------------
    | 12. VERIFICAR
    |--------------------------------------------------------------------------
    */

    sleep(3);

    echo "[12] Verificando registro...\n";

    $contenido = strtolower(
        $driver->getPageSource()
    );

    if (
        str_contains(
            $contenido,
            strtolower($nombreUnidad)
        )
    ) {

        echo "\n";
        echo "====================================================\n";
        echo " PRUEBA APROBADA\n";
        echo "====================================================\n\n";

        echo "La unidad fue registrada correctamente.\n\n";

        echo "Nombre: {$nombreUnidad}\n";
        echo "Sigla: {$siglaUnidad}\n";
        echo "Tipo: {$tipoUnidad}\n";
        echo "Estado: Activo\n";

    } else {

        echo "\n";
        echo "====================================================\n";
        echo " PRUEBA NO CONFIRMADA\n";
        echo "====================================================\n\n";

        echo "No se encontro la unidad en la pagina resultante.\n";

        /*
        | Mostrar posibles errores de Laravel
        */

        $errores = $driver->findElements(
            WebDriverBy::cssSelector('.alert-danger')
        );

        if (count($errores) > 0) {

            echo "\nMensaje de SIGECOM:\n";
            echo $errores[0]->getText();
            echo "\n";
        }

        echo "\nURL actual: ";
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