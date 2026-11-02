<?php

require __DIR__ . '/../../vendor/autoload.php';

use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\WebDriverSelect;
use Facebook\WebDriver\WebDriverWait;

$host = 'http://localhost:9515';

// Contraseña del administrador tomada desde PowerShell.
// No se guarda la contraseña real dentro del archivo.
$passwordAdmin = getenv('SIGECOM_TEST_PASSWORD');

if (!$passwordAdmin) {
    echo "ERROR: No se definio SIGECOM_TEST_PASSWORD.\n";
    echo "Ejecute primero:\n";
    echo '$env:SIGECOM_TEST_PASSWORD="SU_CONTRASENA"' . "\n";
    exit(1);
}

// Datos del usuario que creará Selenium.
// El correo cambia en cada ejecución para evitar duplicados.
$codigo = date('YmdHis');

$nombrePrueba   = 'Prueba';
$apellidoPrueba = 'Selenium';
$telefonoPrueba = '70000000';
$correoPrueba   = "selenium.{$codigo}@prueba.test";
$passwordPrueba = 'Selenium12345';

$options = new ChromeOptions();

$capabilities = DesiredCapabilities::chrome();
$capabilities->setCapability(
    ChromeOptions::CAPABILITY,
    $options
);

$driver = null;

echo "====================================================\n";
echo " SELENIUM - MODULO SEGURIDAD / USUARIOS\n";
echo "====================================================\n\n";

try {

    // -------------------------------------------------
    // 1. ABRIR CHROME
    // -------------------------------------------------

    echo "[1] Abriendo Google Chrome...\n";

    $driver = RemoteWebDriver::create(
        $host,
        $capabilities
    );

    $driver->manage()->window()->maximize();

    $wait = new WebDriverWait($driver, 10);

    // -------------------------------------------------
    // 2. ABRIR LOGIN
    // -------------------------------------------------

    echo "[2] Abriendo inicio de sesion...\n";

    $driver->get(
        'http://127.0.0.1:8000/login'
    );

    // -------------------------------------------------
    // 3. INGRESAR CORREO
    // -------------------------------------------------

    echo "[3] Ingresando correo del administrador...\n";

    $correo = $wait->until(
        WebDriverExpectedCondition::presenceOfElementLocated(
            WebDriverBy::name('correo')
        )
    );

    $correo->sendKeys('admin@gmail.com');

    // -------------------------------------------------
    // 4. INGRESAR CONTRASEÑA
    // -------------------------------------------------

    echo "[4] Ingresando contrasena...\n";

    $driver->findElement(
        WebDriverBy::name('contrasena')
    )->sendKeys($passwordAdmin);

    // -------------------------------------------------
    // 5. INICIAR SESION
    // -------------------------------------------------

    echo "[5] Iniciando sesion...\n";

    $botonLogin = $wait->until(
        WebDriverExpectedCondition::presenceOfElementLocated(
            WebDriverBy::cssSelector(
                'button[type="submit"]'
            )
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

    // Esperar hasta abandonar el login.

    $wait->until(
        WebDriverExpectedCondition::not(
            WebDriverExpectedCondition::urlContains('/login')
        )
    );

    echo "    OK - Inicio de sesion correcto.\n";

    // -------------------------------------------------
    // 6. ABRIR FORMULARIO NUEVO USUARIO
    // -------------------------------------------------

    echo "[6] Abriendo formulario Nuevo usuario...\n";

    $driver->get(
        'http://127.0.0.1:8000/usuarios/create'
    );

    $wait->until(
        WebDriverExpectedCondition::presenceOfElementLocated(
            WebDriverBy::name('nombre')
        )
    );

    echo "    OK - Formulario cargado.\n";

    // -------------------------------------------------
    // 7. LLENAR NOMBRE
    // -------------------------------------------------

    echo "[7] Ingresando nombre...\n";

    $driver->findElement(
        WebDriverBy::name('nombre')
    )->sendKeys($nombrePrueba);

    // -------------------------------------------------
    // 8. LLENAR APELLIDO
    // -------------------------------------------------

    echo "[8] Ingresando apellido...\n";

    $driver->findElement(
        WebDriverBy::name('apellido')
    )->sendKeys($apellidoPrueba);

    // -------------------------------------------------
    // 9. LLENAR TELEFONO
    // -------------------------------------------------

    echo "[9] Ingresando telefono...\n";

    $driver->findElement(
        WebDriverBy::name('telefono')
    )->sendKeys($telefonoPrueba);

    // -------------------------------------------------
    // 10. LLENAR CORREO
    // -------------------------------------------------

    echo "[10] Ingresando correo de prueba...\n";

    $driver->findElement(
        WebDriverBy::name('correo')
    )->sendKeys($correoPrueba);

    // -------------------------------------------------
    // 11. SELECCIONAR ROL AUXILIAR
    // -------------------------------------------------

    echo "[11] Seleccionando rol Auxiliar...\n";

    $selectRol = new WebDriverSelect(
        $driver->findElement(
            WebDriverBy::name('rol_id')
        )
    );

    $selectRol->selectByVisibleText('AUXILIAR');

    // -------------------------------------------------
    // 12. SELECCIONAR ESTADO ACTIVO
    // -------------------------------------------------

    echo "[12] Seleccionando estado Activo...\n";

    $selectEstado = new WebDriverSelect(
        $driver->findElement(
            WebDriverBy::name('estado')
        )
    );

    $selectEstado->selectByValue('1');

    // -------------------------------------------------
    // 13. CONTRASEÑA DEL NUEVO USUARIO
    // -------------------------------------------------

    echo "[13] Ingresando contrasena del usuario...\n";

    $driver->findElement(
        WebDriverBy::name('contrasena')
    )->sendKeys($passwordPrueba);

    // -------------------------------------------------
    // 14. CONFIRMAR CONTRASEÑA
    // -------------------------------------------------

    echo "[14] Confirmando contrasena...\n";

    $driver->findElement(
        WebDriverBy::name('contrasena_confirmation')
    )->sendKeys($passwordPrueba);

    // Dejamos un momento para observar el formulario.
    sleep(2);

    // -------------------------------------------------
    // 15. GUARDAR
    // -------------------------------------------------

    echo "[15] Presionando Guardar...\n";

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

    // -------------------------------------------------
    // 16. ESPERAR RESPUESTA
    // -------------------------------------------------

    sleep(2);

    echo "[16] Verificando registro...\n";

    // -------------------------------------------------
    // 17. COMPROBAR QUE EL USUARIO EXISTE
    // -------------------------------------------------

    $contenidoPagina = $driver->getPageSource();

    if (str_contains(
        strtolower($contenidoPagina),
        strtolower($correoPrueba)
    )) {

        echo "\n";
        echo "====================================================\n";
        echo " PRUEBA EXITOSA\n";
        echo "====================================================\n";
        echo "El usuario fue registrado correctamente.\n";
        echo "Nombre: {$nombrePrueba} {$apellidoPrueba}\n";
        echo "Correo: {$correoPrueba}\n";
        echo "Rol: AUXILIAR\n";
        echo "Estado: Activo\n";

    } else {

        echo "\n";
        echo "====================================================\n";
        echo " PRUEBA NO CONFIRMADA\n";
        echo "====================================================\n";
        echo "Selenium envio el formulario, pero no encontro\n";
        echo "el correo creado en la pagina resultante.\n";
        echo "\n";
        echo "URL actual: " . $driver->getCurrentURL() . "\n";
    }

    // Mantener abierto para observar el resultado.
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

    sleep(8);

} finally {

    if ($driver !== null) {
        $driver->quit();
    }

    echo "\nPrueba finalizada.\n";
}