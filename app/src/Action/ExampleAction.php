<?php

namespace USF\IdM\SlimSkeleton\Action;

use Slim\Views\Twig;
use Slim\Collection;
use Psr\Log\LoggerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use USF\IdM\SlimSkeleton\Service\ExampleService;

/**
 * Main page
 *
 * @category USF-IT
 * @package slimSkeleton
 * @author Eric Pierce <epierce@usf.edu>
 * @license http://www.opensource.org/licenses/MIT MIT
 * @link https://github.com/USF-IT/slim-skeleton
 */
final class ExampleAction
{
    private $view;
    private $logger;
    private $settings;
    private $service;

    /**
     * Class constructor
     *
     * @param Twig            $view      View object
     * @param LoggerInterface $logger    Log object
     * @param Collection      $settings  Slim Settings
     * @param ExampleService  $myService Example Service
     */
    public function __construct(Twig $view, LoggerInterface $logger, Collection $settings, ExampleService $myService)
    {
        $this->view = $view;
        $this->logger = $logger;
        $this->settings = $settings;
        $this->service = $myService;
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
        $netid = $request->getHeaderLine('AUTH_PRINCIPAL');
        $eppa = $request->getHeaderLine('AUTH_ATTR_EDUPERSONPRIMARYAFFILIATION');


        $view_attr = [
            'page_title' => 'SlimSkeleton | Example',  // This will used in the <title> element on the page
            'username' => $netid,
            'eppa' => $eppa
        ];

        return $this->view->render($response, 'example.html', $view_attr);
    }

    /**
     * Run Controller
     *
     * @param ServerRequestInterface $request  PSR7 Request object
     * @param ResponseInterface      $response PSR7 Response object
     * @param array                  $args     Request arguments
     * @return ResponseInterface
     */
    public function getMD5fromWS(Request $request, Response $response, $args)
    {
        $netid = $request->getHeaderLine('AUTH_PRINCIPAL');
        $eppa = $request->getHeaderLine('AUTH_ATTR_EDUPERSONPRIMARYAFFILIATION');

        // Read data from POST data
        $parsedBody = $request->getParsedBody();

        $results = $this->service->getMD5fromWS($parsedBody['searchTerm']);

        $view_attr = [
            'page_title' => 'SlimSkeleton | Example',  // This will used in the <title> element on the page
            'username' => $netid,
            'eppa' => $eppa,
            'input' => $parsedBody['searchTerm'],
            'result' => $results
        ];

        return $this->view->render($response, 'example.html', $view_attr);
    }
}
