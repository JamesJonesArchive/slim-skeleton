<?php

namespace epierce\slimSkeleton\Action;

use Slim\Views\Twig;
use Slim\Collection;
use Psr\Log\LoggerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use epierce\slimSkeleton\Service\ExampleService;

/**
 * Main page
 *
 * @category epierce
 * @package slim-skeleton
 * @author Eric Pierce <epierce@usf.edu>
 * @license http://www.opensource.org/licenses/MIT MIT
 * @link https://github.com/epierce/slim-skeleton
 */
final class HomeAction
{
    private $view;
    private $logger;
    private $settings;
    private $myService;

    /**
     * Class constructor
     *
     * @param Twig            $view      View object
     * @param LoggerInterface $logger    Log object
     * @param Collection      $settings  Slim Settings
     * @param ExampleService  $myService Example Service
     */
    public function __construct(Twig $view, LoggerInterface $logger, Collection $settings, ExampleService $myService )
    {
        $this->view = $view;
        $this->logger = $logger;
        $this->settings = $settings;
        $this->myService = $myService;
    }

    /**
     * Run Controller
     *
     * @param ServerRequestInterface $request  PSR7 Request object
     * @param ResponseInterface      $response PSR7 Response object
     * @param array                  $args     Request arguments
     * @return ResponseInterface
     */
    public function dispatch(Request $request, Response $response, $args)
    {
        // Read a parameter from the GET/POST parameter
        $queryFoo = $request->getQueryParams()['foo'] ?? '';

        // Call services and/or do controller logic here

        // Setup data that will be passed to the Twig template
        $view_attrs = [
            'queryFoo' => $queryFoo
        ];

        $this->view->render($response, 'home.html', $view_attrs);
        return $response;
    }
}
