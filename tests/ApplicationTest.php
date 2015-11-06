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
}
