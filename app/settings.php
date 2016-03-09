<?php
return [
    'config' => [
        'displayErrorDetails' => true, // set to false in production
        // Renderer settings
        'renderer' => [
            'template_path' => __DIR__ . '/../templates/',
        ],
        // Monolog settings
        'logger' => [
            'name' => 'slim-app',
            'path' => __DIR__ . '/../logs/app.log',
        ],

        'links' => [
            'github'        => 'https://github.com/php-school/learn-you-php',
            'twitter'       => 'https://twitter.com/PHPSchoolTeam',
            'slack'         => 'https://phpschool.herokuapp.com',
            'discussions'   => 'https://github.com/php-school/discussions',
            'workshop'      => 'https://github.com/php-school/php-workshop',
        ],

        'cacheDir' => __DIR__ . '/../cache',
    ],
    'settings.displayErrorDetails' => true,
];
