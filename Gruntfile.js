//Gruntfile
module.exports = function(grunt) {

    require('load-grunt-tasks')(grunt);

    //Initializing the configuration object
    grunt.initConfig({

        // Task configuration
        php: {
            dist: {
                options: {
                    hostname: '127.0.0.1',
                    port: 9000,
                    base: 'public',
                    keepalive: false,
                    open: false
                }
            }
        },
        browserSync: {
            dist: {
                bsFiles: {
                    src : [
                        'public/assets/stylesheets/*.css',
                        'public/assets/javascript/*.js',
                        'public/*.php',
                        'public/**/*.php',
                        'templates/*.twig'
                    ]
                },
                options: {
                    proxy: '<%= php.dist.options.hostname %>:<%= php.dist.options.port %>',
                    watchTask: true,
                    notify: true,
                    open: true,
                    logLevel: 'silent',
                    ghostMode: {
                        clicks: true,
                        scroll: true,
                        links: true,
                        forms: true
                    }
                }
            }
        },
        less: {
            development: {
                options: {
                    compress: true,  //minifying the result
                },
                files: {
                    //compiling base.less into base.css
                    "./public/assets/stylesheets/frontend.css":"./assets/stylesheets/base.less"
                }
            }
        },
        copy: {
            main: {
                files: [
                    // includes files within path
                    {
                        expand: true,
                        flatten: true,
                        src: ['./bower_components/bootstrap/fonts/**'],
                        dest: './public/assets/fonts',
                        filter: 'isFile'
                    }
                ],
            }
        },
        concat: {
            options: {
                separator: ';',
            },
            main_js: {
                src: [
                    './bower_components/jquery/dist/jquery.js',
                    './bower_components/bootstrap/dist/js/bootstrap.js',
                    './assets/javascript/main.js'
                ],
                dest: './public/assets/javascript/frontend.js'
            }
        },
        uglify: {
            options: {
                mangle: false  // Use if you want the names of your functions and variables unchanged
            },
            main_js: {
                files: {
                    './public/assets/javascript/frontend.js': './public/assets/javascript/frontend.js',
                }
            }
        },
        phpunit: {
            classes: {
                dir: 'tests/'   //location of the tests
            },
            options: {
                bin: 'vendor/bin/phpunit',
                colors: true
            }
        },
        watch: {
            main_js: {
                files: [
                    //watched files
                    './bower_components/jquery/dist/jquery.js',
                    './bower_components/bootstrap/dist/js/bootstrap.js',
                    './assets/javascript/frontend.js'
                ],
                tasks: ['concat:js_frontend','uglify:frontend'],     //tasks to run
                options: {
                    livereload: true                        //reloads the browser
                }
            },
            less: {
                files: ['./app/assets/stylesheets/*.less'],  //watched files
                tasks: ['less'],                          //tasks to run
                options: {
                    livereload: true                        //reloads the browser
                }
            },
            tests: {
                files: ['public/src/*.php'],  //the task will run only when you save files in this location
                tasks: ['phpunit']
            }
        }
    });


    // Task definition
    grunt.registerTask('default', ['watch']);
    grunt.registerTask('serve', [
        'php:dist',         // Start PHP Server
        'browserSync:dist', // Using the php instance as a proxy
        'watch'             // Any other watch tasks you want to run
    ]);
};
