<?php

require __DIR__ . '/../../vendor/autoload.php';

use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\WebDriverWait;

$host = 'http://localhost:9515';

$options = new ChromeOptions();

$capabilities = DesiredCapabilities::chrome();
$capabilities->setCapability(ChromeOptions::CAPABILITY, $options);

echo "========================================\n";
echo " PRUEBA AUTOMATIZADA - LOGIN SIGECOM\n";
echo "========================================\n\n";

$driver = null;

try {

    // 1. Abrir Chrome
    echo "[1] Abriendo Google Chrome...\n";

    $driver = RemoteWebDriver::create($host, $capabilities);

    $driver->manage()->window()->maximize();

    // 2. Entrar al sistema
    echo "[2] Ingresando a SIGECOM...\n";

    $driver->get('http://127.0.0.1:8000/login');

    sleep(2);

    // 3. Escribir correo
    echo "[3] Ingresando correo...\n";

    $driver->findElement(
        WebDriverBy::name('correo')
    )->sendKeys('admin@gmail.com');

    // 4. Escribir contraseña
    echo "[4] Ingresando contraseña...\n";

    $driver->findElement(
        WebDriverBy::name('contrasena')
    )->sendKeys('admin123');

    sleep(1);

   // 5. Presionar botón de inicio de sesión
echo "[5] Presionando Iniciar sesión...\n";

$wait = new WebDriverWait($driver, 10);

$botonLogin = $wait->until(
    WebDriverExpectedCondition::elementToBeClickable(
        WebDriverBy::cssSelector('button[type="submit"]')
    )
);

// Llevar el botón al centro de la pantalla
$driver->executeScript(
    "arguments[0].scrollIntoView({block: 'center'});",
    [$botonLogin]
);

sleep(1);

// Clic mediante JavaScript para evitar elementos superpuestos
$driver->executeScript(
    "arguments[0].click();",
    [$botonLogin]
);

    // 6. Esperar respuesta del sistema
    $wait = new WebDriverWait($driver, 10);

    $wait->until(
        WebDriverExpectedCondition::not(
            WebDriverExpectedCondition::urlContains('/login')
        )
    );

    // 7. Comprobar resultado
    $urlActual = $driver->getCurrentURL();

    echo "\nURL después del login: " . $urlActual . "\n";

    if (!str_contains($urlActual, '/login')) {

        echo "\n========================================\n";
        echo " PRUEBA EXITOSA\n";
        echo "========================================\n";
        echo "El administrador inició sesión correctamente.\n";

    } else {

        echo "\n========================================\n";
        echo " PRUEBA FALLIDA\n";
        echo "========================================\n";
        echo "El sistema permaneció en el inicio de sesión.\n";
    }

    // Mantener Chrome abierto para observar el resultado
    sleep(8);

} catch (Exception $e) {

    echo "\n========================================\n";
    echo " ERROR EN LA PRUEBA\n";
    echo "========================================\n";

    echo $e->getMessage() . "\n";

    sleep(5);

} finally {

    if ($driver !== null) {
        $driver->quit();
    }

    echo "\nPrueba finalizada.\n";
}