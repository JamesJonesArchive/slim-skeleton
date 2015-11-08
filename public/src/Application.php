<?php

namespace epierce;

use Slim\Slim;
use USF\IdM\UsfConfig;

class Application extends Slim
{
    public $config;
    public function __construct(UsfConfig $usfConfig)
    {
        //Set configuration
        $templateDir = $usfConfig->slimTemplateDir ? $usfConfig->slimTemplateDir : '../templates';

        // Slim initialization with custom template directory
        parent::__construct(['templates.path' => $templateDir]);

        $this->notFound(function () {
            $this->_handleNotFound();
        });

        $this->error(function (\Exception $e) {
            $this->_handleException($e);
        });

        //Set Application name
        $appName = $usfConfig->applicationName ? $usfConfig->applicationName : 'slim-skeleton';

        //Set configuration
        if (is_array($usfConfig->slimSettings)) {
            foreach (array_keys($usfConfig->slimSettings) as $var) {
                $this->config($var, $usfConfig->slimSettings[$var]);
            }
        } else {
            throw new \Exception('No Slim configuration data found!', 500);
        }

        // If a list of slim middleware was given instantiate them all
        if (is_array($usfConfig->slimMiddlewareObjects)) {
            foreach ($usfConfig->slimMiddlewareObjects as $slimMiddleware) {
                $this->add(new $slimMiddleware());
            }
        }

        // Create monolog logger and store logger in container as singleton
        if ($this->config('log.monolog')) {
            $this->container->singleton('log', function () {
                $log = new \Monolog\Logger($this->getName());
                $log->pushHandler(new \Monolog\Handler\StreamHandler($this->config('log.file_location'), \Monolog\Logger::DEBUG));

                return $log;
            });
        }

        // Configure Twig template parser
        if ($this->config('view.twig')) {
            // Prepare view
            $this->view(new \Slim\Views\Twig());
            $this->view->parserOptions = [
                'charset' => 'utf-8',
                'cache' => realpath($templateDir.'/cache'),
                'auto_reload' => $this->config('view.twig.auto_reload'),
                'strict_variables' => $this->config('view.twig.strict_variables'),
                'autoescape' => $this->config('view.twig.autoescape'),
            ];
            $this->view->parserExtensions = [new \Slim\Views\TwigExtension()];
        }

        // Create database resource as a singleton
        // $this->container->singleton('dataSource', function () {
        //     return new DataBaseClass(foo,bar);
        // });

        // Setup Authentication Providers
        // HMAC
        // $this->environment['auth.hmac.keyRegistry'] = $this->config('hmacRegistry');
        // if ($this->config->hmacTimeout) {
        //     $this->environment['auth.hmac.timeout'] = $this->config('hmacTimeout');
        // }
        //
        // CAS
        $this->environment['auth.config.cas'] = $this->config('casConfig');
        //
        // TokenAuth
        // $this->environment['auth.config.token'] = $this->config('tokenConfig');

        // Setup AuthN/AuthZ map
        $this->environment['auth.interceptUrlMap'] = $this->config('interceptUrlMap');

        /*
        * Log requests and results
        */
        $this->hook('slim.after', function () {
            $request = $this->request;
            $response = $this->response;
            $this->log->info(
                $request->getHostWithPort().' '.
                $request->getIp().' '.
                $request->getMethod().' '.
                $request->getPathInfo().' '.
                $response->getStatus().' '.
                strlen($request->getBody()).' '.
                strlen($response->getBody())
            );
        });

        // Define routes
        $this->get('/', function () {
            // Sample log message
            $this->log->info("Slim-Skeleton '/' route");
            // Render index view and include the logged username
            $this->render('index.twig', ['username' => $this->environment['principal.name']]);
        });
        $this->get('/denied', function () {
            // This URL is denied through the interceptUrlMap
        });
    }

    private function _handleNotFound()
    {
        throw new \Exception(
            'Resource '.$this->request->getResourceUri().' using '.$this->request->getMethod().' method does not exist.',
            404
        );
    }
    private function _handleException(\Exception $e)
    {
        $status = $e->getCode();
        $statusText = \Slim\Http\Response::getMessageForCode($status);
        if ($statusText === null) {
            $status = 500;
            $statusText = 'Internal Server Error';
        }
        $this->response->setStatus($status);
        $this->response->headers->set('Content-Type', 'application/json');
        // Return the error using JSend format: http://labs.omniti.com/labs/jsend
        $this->response->setBody(
            json_encode(
                [
                    'status' => ($status < 500) ? 'fail' : 'error',
                    'data' => [
                        'status' => $status,
                        'statusText' => preg_replace('/^[0-9]+ (.*)$/', '$1', $statusText),
                        'description' => $e->getMessage(),
                    ],
                ]
            )
        );
    }
}
