<?php
/**
 *
 * Configure URL routes
 * see: http://www.slimframework.com/docs/objects/router.html
 *
 */
$app->get('/', 'SlimExample\Action\HomeAction:dispatch')
    ->setName('homepage');

// Other routes go here
