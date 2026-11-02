<?php

require __DIR__ . '/../../vendor/autoload.php';

use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;

$host = 'http://localhost:9515';

$options = new ChromeOptions();

$capabilities = DesiredCapabilities::chrome();
$capabilities->setCapability(ChromeOptions::CAPABILITY, $options);

echo "Iniciando prueba de Selenium...\n";

$driver = RemoteWebDriver::create($host, $capabilities);

$driver->manage()->window()->maximize();

$driver->get('http://127.0.0.1:8000');

echo "SIGECOM abierto correctamente.\n";
echo "Titulo de la pagina: " . $driver->getTitle() . "\n";

sleep(10);

$driver->quit();

echo "Prueba finalizada correctamente.\n";