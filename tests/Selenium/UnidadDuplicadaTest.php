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
| PA-UNI-02
|--------------------------------------------------------------------------
| Módulo: Parque Automotor
| Submódulo: Unidades
| Prueba: Rechazar unidad con sigla duplicada
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

$codigo = date('His');

$nombre1 = 'UNIDAD DUPLICADO A ' . $codigo;
$nombre2 = 'UNIDAD DUPLICADO B ' . $codigo;

$siglaDuplicada = 'UD' . $codigo;

$tipo = 'DIRECCION';

$descripcion1 =
    'Primera unidad creada para prueba de duplicidad';

$descripcion2 =
    'Intento de registro con sigla duplicada';

$options = new ChromeOptions();

$capabilities = DesiredCapabilities::chrome();

$capabilities->setCapability(
    ChromeOptions::CAPABILITY,
    $options
);

$driver = null;

echo "\n";
echo "====================================================\n";
echo " PA-UNI-02 - UNIDAD DUPLICADA\n";
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

    $wait = new WebDriverWait(
        $driver,
        10
    );

    /*
    |--------------------------------------------------------------------------
    | 2. LOGIN
    |--------------------------------------------------------------------------
    */

    echo "[2] Iniciando sesion...\n";

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
    | FUNCIÓN REGISTRAR UNIDAD
    |--------------------------------------------------------------------------
    */

    $registrarUnidad = function (
        $nombre,
        $sigla,
        $descripcion
    ) use (
        $driver,
        $wait,
        $urlSistema,
        $tipo
    ) {

        $driver->get(
            $urlSistema . '/unidades/create'
        );

        $wait->until(
            WebDriverExpectedCondition::presenceOfElementLocated(
                WebDriverBy::name('nombre')
            )
        );

        $driver->findElement(
            WebDriverBy::name('nombre')
        )->sendKeys($nombre);

        $driver->findElement(
            WebDriverBy::name('sigla')
        )->sendKeys($sigla);

        $driver->findElement(
            WebDriverBy::name('tipo')
        )->sendKeys($tipo);

        $driver->findElement(
            WebDriverBy::name('descripcion')
        )->sendKeys($descripcion);

        $estado = new WebDriverSelect(
            $driver->findElement(
                WebDriverBy::name('estado')
            )
        );

        $estado->selectByValue('1');

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
    | 3. PRIMER REGISTRO
    |--------------------------------------------------------------------------
    */

    echo "[3] Registrando primera unidad...\n";

    echo "    Nombre: {$nombre1}\n";
    echo "    Sigla: {$siglaDuplicada}\n";

    $registrarUnidad(
        $nombre1,
        $siglaDuplicada,
        $descripcion1
    );

    /*
    | El primer registro debe redirigir al listado.
    */

    $urlPrimerRegistro = $driver->getCurrentURL();

    if (str_contains($urlPrimerRegistro, '/unidades/create')) {

        throw new Exception(
            'La primera unidad no pudo registrarse.'
        );
    }

    echo "    OK - Primera unidad registrada correctamente.\n";

    /*
    |--------------------------------------------------------------------------
    | 4. SEGUNDO REGISTRO CON MISMA SIGLA
    |--------------------------------------------------------------------------
    */

    echo "\n[4] Intentando registrar unidad duplicada...\n";

    echo "    Nombre diferente: {$nombre2}\n";
    echo "    Sigla repetida: {$siglaDuplicada}\n";

    $registrarUnidad(
        $nombre2,
        $siglaDuplicada,
        $descripcion2
    );

    /*
    |--------------------------------------------------------------------------
    | 5. VERIFICAR RESULTADO
    |--------------------------------------------------------------------------
    */

    echo "[5] Verificando que SIGECOM rechace el duplicado...\n";

    $urlActual = $driver->getCurrentURL();

    $contenidoPagina = mb_strtolower(
        $driver->getPageSource()
    );

    /*
    | Laravel, cuando falla la validación, vuelve al formulario.
    */
    $permaneceEnFormulario = str_contains(
        $urlActual,
        '/unidades/create'
    );

    /*
    | Buscamos también el mensaje de validación directamente
    | en todo el HTML, sin depender de .alert-danger.
    */
    $mensajeDuplicado =
        str_contains(
            $contenidoPagina,
            'ya existe una unidad con esta sigla'
        )
        ||
        str_contains(
            $contenidoPagina,
            'sigla has already been taken'
        )
        ||
        str_contains(
            $contenidoPagina,
            'sigla ya ha sido registrado'
        );

    /*
    |--------------------------------------------------------------------------
    | 6. RESULTADO FINAL
    |--------------------------------------------------------------------------
    */

    if ($permaneceEnFormulario) {

        echo "\n";
        echo "====================================================\n";
        echo " PRUEBA APROBADA\n";
        echo "====================================================\n\n";

        echo "SIGECOM rechazo correctamente la unidad duplicada.\n\n";

        echo "Primera unidad:\n";
        echo "  Nombre: {$nombre1}\n";
        echo "  Sigla: {$siglaDuplicada}\n\n";

        echo "Segundo intento:\n";
        echo "  Nombre: {$nombre2}\n";
        echo "  Sigla repetida: {$siglaDuplicada}\n\n";

        echo "Resultado: REGISTRO RECHAZADO\n";

        if ($mensajeDuplicado) {
            echo "Validacion de sigla duplicada detectada correctamente.\n";
        }

        echo "\nURL final: {$urlActual}\n";

    } else {

        echo "\n";
        echo "====================================================\n";
        echo " PRUEBA FALLIDA\n";
        echo "====================================================\n\n";

        echo "El sistema salio del formulario despues de intentar\n";
        echo "registrar la sigla duplicada.\n\n";

        echo "Sigla utilizada: {$siglaDuplicada}\n";
        echo "URL final: {$urlActual}\n";
    }

    /*
    |--------------------------------------------------------------------------
    | MOSTRAR RESULTADO
    |--------------------------------------------------------------------------
    */

    sleep(10);

} catch (Exception $e) {

    echo "\n";
    echo "====================================================\n";
    echo " ERROR EN LA PRUEBA\n";
    echo "====================================================\n\n";

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