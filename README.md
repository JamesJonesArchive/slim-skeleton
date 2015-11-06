# Slim Framework 2 Skeleton Application

This is based on the [Slim-Skeleton](https://github.com/slimphp/Slim-Skeleton) project.

Use this skeleton application to quickly setup and start working on a new Slim Framework 2 application. This application
uses the Slim 2 and Slim-Views repositories. It also uses Sensio Labs' [Twig](http://twig.sensiolabs.org) template library.

This skeleton application was built for Composer. This makes setting up a new Slim Framework application quick and easy.

## Install Composer

## Install Grunt

## Install Bower

## Install the Application

After you install Composer, run this command from the directory in which you want to install your new Slim Framework application.

    php composer.phar create-project epierce/slim-skeleton [my-app-name]

Replace <code>[my-app-name]</code> with the desired directory name for your new application. You'll want to:
* Point your virtual host document root to your new application's `public/` directory.
* Ensure `logs/` and `templates/cache` are web writeable.

### Install Node.js packages

    npm install

### Install Bower packages

    bower install

### Use Grunt to run the PHP server

    grunt serve

That's it! Now go build something cool.
