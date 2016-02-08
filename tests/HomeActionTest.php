<?php


namespace USF\IdM\PeopleSoftAuthenticator\Action;

class HomeActionTest extends \PHPUnit_Framework_TestCase
{
    private $app;

    public function setup()
    {

        $config = new \USF\IdM\UsfConfig(__DIR__ . '/config');

        $app = new \Slim\App(['settings' => $config->slimSettings]);
        // Set up dependencies
        include __DIR__ . '/../app/dependencies.php';
        // Register middleware
        include __DIR__ . '/../app/middleware.php';
        // Register routes
        include __DIR__ . '/../app/routes.php';
        $this->app = $app;
    }

    public function setRequest($method = 'GET', $uri = '/', $queryString = '')
    {
        // Prepare request and response objects
        $env = \Slim\Http\Environment::mock([
            'SCRIPT_NAME' => '/index.php',
            'REQUEST_URI' => $uri,
            'REQUEST_METHOD' => $method,
            'QUERY_STRING' => $queryString
        ]);
        $uri = \Slim\Http\Uri::createFromEnvironment($env);
        $headers = \Slim\Http\Headers::createFromEnvironment($env);
        $cookies = (array) new \Slim\Collection();
        $serverParams = (array) new \Slim\Collection($env->all());
        $body = new \Slim\Http\Body(fopen('php://temp', 'r+'));
        return new \Slim\Http\Request('GET', $uri, $headers, $cookies, $serverParams, $body);
    }

    public function testHomeAction()
    {
        $req = $this->setRequest('GET', '/');
        $res = new \Slim\Http\Response;

        // Invoke app
        $app = $this->app;
        $resOut = $app($req, $res);
        $this->assertInstanceOf('\Psr\Http\Message\ResponseInterface', $resOut);
        $this->assertContains('PeopleSoft Authentication', (string) $res->getBody());
        $this->assertContains('SelfService?instance=phpunit">GEMS Self-Service', (string) $res->getBody());
        $this->assertContains('GEMS?instance=phpunit">GEMS', (string) $res->getBody());
    }

    public function testHomeActionInstance()
    {
        $req = $this->setRequest('GET', '/', 'instance=foo');
        $res = new \Slim\Http\Response;

        // Invoke app
        $app = $this->app;
        $resOut = $app($req, $res);
        $this->assertInstanceOf('\Psr\Http\Message\ResponseInterface', $resOut);
        $this->assertContains('PeopleSoft Authentication', (string) $res->getBody());
        $this->assertContains('SelfService?instance=foo">GEMS Self-Service', (string) $res->getBody());
        $this->assertContains('GEMS?instance=foo">GEMS', (string) $res->getBody());
    }
}
