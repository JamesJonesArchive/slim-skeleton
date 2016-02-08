<?php
/**
 *
 * Configure Dependency Injection
 * see: http://www.slimframework.com/docs/concepts/di.html
 */

$container = $app->getContainer();
$settings = $container->settings;

// -----------------------------------------------------------------------------
// Service providers
// -----------------------------------------------------------------------------

// Twig
$container['view'] = function ($c) use ($settings) {
    $view = new \Slim\Views\Twig($settings['view']['template_path'], $settings['view']['twig']);

    // Add extensions
    $view->addExtension(new Slim\Views\TwigExtension($c->router, $c->request->getUri()));
    $view->addExtension(new Twig_Extension_Debug());

    return $view;
};

// Monolog
$container['logger'] = function ($c) use ($settings) {
    $logger = new \Monolog\Logger($settings['logger']['name']);
    $logger->pushProcessor(new \Monolog\Processor\UidProcessor());
    $logger->pushHandler(new \Monolog\Handler\StreamHandler($settings['logger']['path'], \Monolog\Logger::DEBUG));
    return $logger;
};

// Example Service
$container['myService'] =  function ($c) {
    return new epierce\slimSkeleton\Service\exampleService($c->logger, $c->settings);
};

// -----------------------------------------------------------------------------
// Action factories
// -----------------------------------------------------------------------------

$container['PeopleSoftAuthenticator\Action\HomeAction'] = function ($c) {
    return new \USF\IdM\PeopleSoftAuthenticator\Action\HomeAction($c->view, $c->logger, $c->settings, $c->myService);
};
