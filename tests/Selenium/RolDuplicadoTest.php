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
| SEG-ROL-02
|--------------------------------------------------------------------------
| Módulo: Seguridad
| Submódulo: Roles
| Prueba: Registrar rol duplicado
|
| Objetivo:
| Verificar que SIGECOM no permita registrar un rol cuyo nombre
| ya se encuentra registrado en el sistema.
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
| DATO DUPLICADO
|--------------------------------------------------------------------------
| ADMINISTRADOR ya existe en la base de datos.
*/

$nombreRol = 'ADMINISTRADOR';

$descripcionRol =
    'Intento de registro duplicado mediante Selenium';

$options = new ChromeOptions();

$capabilities = DesiredCapabilities::chrome();

$capabilities->setCapability(
    ChromeOptions::CAPABILITY,
    $options
);

$driver = null;

echo "\n";
echo "====================================================\n";
echo " SEG-ROL-02 - REGISTRAR ROL DUPLICADO\n";
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
    | 2. ABRIR LOGIN
    |--------------------------------------------------------------------------
    */

    echo "[2] Abriendo SIGECOM...\n";

    $driver->get(
        $urlSistema . '/login'
    );

    /*
    |--------------------------------------------------------------------------
    | 3. INGRESAR CORREO
    |--------------------------------------------------------------------------
    */

    echo "[3] Ingresando correo...\n";

    $wait->until(
        WebDriverExpectedCondition::presenceOfElementLocated(
            WebDriverBy::name('correo')
        )
    )->sendKeys($correoAdmin);

    /*
    |--------------------------------------------------------------------------
    | 4. INGRESAR CONTRASEÑA
    |--------------------------------------------------------------------------
    */

    echo "[4] Ingresando contrasena...\n";

    $driver->findElement(
        WebDriverBy::name('contrasena')
    )->sendKeys($passwordAdmin);

    /*
    |--------------------------------------------------------------------------
    | 5. INICIAR SESIÓN
    |--------------------------------------------------------------------------
    */

    echo "[5] Iniciando sesion...\n";

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
    | 6. ABRIR NUEVO ROL
    |--------------------------------------------------------------------------
    */

    echo "[6] Abriendo formulario Nuevo rol...\n";

    $driver->get(
        $urlSistema . '/roles/create'
    );

    $wait->until(
        WebDriverExpectedCondition::presenceOfElementLocated(
            WebDriverBy::name('nombre')
        )
    );

    echo "    OK - Formulario cargado.\n";

    /*
    |--------------------------------------------------------------------------
    | 7. INGRESAR NOMBRE DUPLICADO
    |--------------------------------------------------------------------------
    */

    echo "[7] Ingresando rol existente: ADMINISTRADOR...\n";

    $driver->findElement(
        WebDriverBy::name('nombre')
    )->sendKeys($nombreRol);

    /*
    |--------------------------------------------------------------------------
    | 8. INGRESAR DESCRIPCIÓN
    |--------------------------------------------------------------------------
    */

    echo "[8] Ingresando descripcion...\n";

    $driver->findElement(
        WebDriverBy::name('descripcion')
    )->sendKeys($descripcionRol);

    /*
    |--------------------------------------------------------------------------
    | 9. SELECCIONAR ESTADO
    |--------------------------------------------------------------------------
    */

    echo "[9] Seleccionando estado Activo...\n";

    $estado = new WebDriverSelect(
        $driver->findElement(
            WebDriverBy::name('estado')
        )
    );

    $estado->selectByValue('1');

    sleep(2);

    /*
    |--------------------------------------------------------------------------
    | 10. INTENTAR GUARDAR
    |--------------------------------------------------------------------------
    */

    echo "[10] Intentando guardar rol duplicado...\n";

    $botonGuardar = $driver->findElement(
        WebDriverBy::cssSelector(
            'button[type="submit"]'
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
    | 11. ESPERAR RESPUESTA DEL SERVIDOR
    |--------------------------------------------------------------------------
    */

    sleep(2);

    echo "[11] Verificando rechazo del duplicado...\n";

    $contenido = strtolower(
        $driver->getPageSource()
    );

    $urlActual = $driver->getCurrentURL();

    /*
    |--------------------------------------------------------------------------
    | 12. COMPROBAR VALIDACIÓN
    |--------------------------------------------------------------------------
    |
    | El formulario de Roles muestra los errores de Laravel dentro de:
    |
    | @if ($errors->any())
    |     <div class="alert alert-danger">
    |
    | Por eso comprobamos si aparece alert-danger.
    */

    $errores = $driver->findElements(
        WebDriverBy::cssSelector('.alert-danger')
    );

    if (count($errores) > 0) {

        $mensajeError = trim(
            $errores[0]->getText()
        );

        echo "\n";
        echo "====================================================\n";
        echo " PRUEBA APROBADA\n";
        echo "====================================================\n";

        echo "SIGECOM rechazo correctamente el rol duplicado.\n";
        echo "Rol utilizado: {$nombreRol}\n";
        echo "\nMensaje mostrado por el sistema:\n";
        echo $mensajeError . "\n";

    } else {

        echo "\n";
        echo "====================================================\n";
        echo " PRUEBA FALLIDA\n";
        echo "====================================================\n";

        echo "No se encontro el mensaje de validacion esperado.\n";
        echo "SIGECOM podria haber permitido registrar\n";
        echo "el rol duplicado.\n";
        echo "\nURL actual: {$urlActual}\n";
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