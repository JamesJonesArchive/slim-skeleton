<?php
/**
 *
 * Configure URL routes
 * see: http://www.slimframework.com/docs/objects/router.html
 *
 */

// Application Home Page
$app->get('/', 'SlimSkeleton\Action\HomeAction:dispatch')
    ->setName('homepage');

// Display Search form
$app->get('/example', 'SlimSkeleton\Action\ExampleAction:dispatch')
    ->setName('exampleFormDisplay');

// Display Results
$app->post('/example', 'SlimSkeleton\Action\ExampleAction:getMD5fromWS')
    ->setName('exampleResults');
