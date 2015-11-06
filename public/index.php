<?php
error_reporting(-1);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
date_default_timezone_set('UTC');

try {
    // Initialize Composer autoloader
    if (!file_exists($autoload = '../vendor/autoload.php')) {
        throw new \Exception('Composer dependencies not installed.');
    }
    require_once $autoload;
    // Initialize Slim Framework
    if (!class_exists('\\Slim\\Slim')) {
        throw new \Exception(
            'Missing Slim from Composer dependencies.  Ensure slim/slim is in composer.json'
        );
    }
    // Create a config object
    $configDir = isset($_ENV['APP_CONFIG_DIR']) ? $_ENV['APP_CONFIG_DIR'] : '../config';

    $usfConfigObject = new \USF\IdM\UsfConfig($configDir);
    // Run application
    $app = new \epierce\Application($usfConfigObject);
    $app->run();
} catch (\Exception $e) {
    if (isset($app)) {
        $app->handleException($e);
    } else {
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode(
            [
                'status' => 'error',
                'data' => [
                    'status' => 500,
                    'statusText' => 'Internal Server Error',
                    'description' => $e->getMessage()
                ]
            ]
        );
    }
}
