<?php

namespace epierce;

use USF\IdM\Testing\SlimTestClient;
use USF\IdM\Testing\SlimTestCase;

class ApplicationTest extends SlimTestCase
{
    private $testingClass = '\epierce\Application';

    public function testGet()
    {
        $client = new SlimTestClient();
        $client->setupRequest('get', '/');

        // Create a Slim instance
        $app = $this->getSlimInstance(TEST_CONFIG_DIR, $this->testingClass);

        $client->runRequest($app);

        $this->assertEquals(200, $client->response->status());
        $this->assertContains('<h1>Slim-Skeleton</h1>', $client->response->body());
    }

    public function testLoggedInUser()
    {
        $client = new SlimTestClient();
        $client->setupRequest('get', '/');

        // Create a Slim instance
        $app = $this->getSlimInstance(TEST_CONFIG_DIR, $this->testingClass);
        $app->environment['principal.name'] = 'test_user';

        $client->runRequest($app);

        $this->assertEquals(200, $client->response->status());
        $this->assertContains('test_user', $client->response->body());
    }

    public function testDeniedURL()
    {
        $client = new SlimTestClient();
        $client->setupRequest('get', '/denied');

        // Create a Slim instance
        $app = $this->getSlimInstance(TEST_CONFIG_DIR, $this->testingClass);
        $app->environment['principal.name'] = 'test_user';

        $client->runRequest($app);

        $this->assertEquals(403, $client->response->status());
        $this->assertEquals(
            '{"status":"fail","data":{"status":403,"statusText":"Forbidden","description":"Resource \/denied using GET method is forbidden"}}',
            $client->response->body()
        );
    }

    public function testNotFound()
    {
        $client = new SlimTestClient();
        $client->setupRequest('get', '/missing');

        // Create a Slim instance
        $app = $this->getSlimInstance(TEST_CONFIG_DIR, $this->testingClass);
        ob_end_clean(); //Needed to clean up output buffer
        $client->runRequest($app);
        ob_end_clean(); //Needed to clean up output buffer

        $this->assertEquals(404, $client->response->status());
        $this->assertEquals(
            '{"status":"fail","data":{"status":404,"statusText":"Not Found","description":"Resource \/missing using GET method does not exist."}}',
            $client->response->body()
        );
    }
}
