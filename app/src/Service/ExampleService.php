<?php
/**
 * Example Service
 *
 * @category USF-IT
 * @package slimSkeleton
 * @author Eric Pierce <epierce@usf.edu>
 * @license http://www.opensource.org/licenses/MIT MIT
 * @link https://github.com/USF-IT/slim-skeleton
 */
namespace USF\IdM\SlimSkeleton\Service;

use Psr\Log\LoggerInterface;
use Slim\Collection;
use GuzzleHttp\Client;

/**
 *
 *
 * @category USF-IT
 * @package slimSkeleton
 * @author Eric Pierce <epierce@usf.edu>
 * @license http://www.opensource.org/licenses/MIT MIT
 * @link https://github.com/USF-IT/slim-skeleton
 */
class ExampleService
{
    private $logger;
    private $settings;

    public function __construct(LoggerInterface $logger, Collection $settings )
    {
        $this->logger = $logger;
        $this->settings = $settings;

        // Pull config data from the settings object
        $serviceConfig = $settings['example_config'];

        // If you need to configure your service object, do it here and  add it as a
        // private property to the class.
    }

    /**
     * Get the MD5 hash of the string from a public webservice
     *
     * @param $input_text
     * @return mixed
     */
    public function getMD5fromWS($input_string)
    {
        $client = new Client();
        $data = json_decode($client->get('http://md5.jsontest.com/?text='.$input_string)->getBody(), true);

        return $data['md5'];
    }
}
