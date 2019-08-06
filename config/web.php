<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => '2c4uk915',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass'   => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets'    => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        'urlManager' => [
            'enablePrettyUrl'     => true,
            'showScriptName'      => false,
            'enableStrictParsing' => false,
            'rules' => [
                '/'      => '/product/list',
                '/count' => '/product/count',
                [
                    'pattern' => 'products/queue_<queue_id:\d+>/<offset:\d+>/<limit:\d+>',
                    'route'   => 'product/list',
                    'defaults'=> [
                        'queue_id' => 0,
                        'offset'   => 0,
                        'limit'    => 10
                    ]
                ],
                [
                    'pattern' => 'products/<offset:\d+>/<limit:\d+>',
                    'route'   => 'product/list',
                    'defaults'=> [
                        'offset'   => 0,
                        'limit'    => 10
                    ]
                ],
                [
                    'pattern' => 'count/queue_<queue_id:\d+>',
                    'route'   => 'product/count',
                    'defaults'=> [
                        'queue_id' => 0,
                    ]
                ],
                [
                    'pattern' => 'product/<id:\d+>',
                    'route'   => 'product/view',
                    'defaults'=> [
                        'id' => 0
                    ]
                ],
                [
                    'pattern' => 'product/delete/<id:\d+>',
                    'route'   => 'product/delete',
                    'defaults'=> [
                        'id' => 0
                    ]
                ],
                [
                    'pattern' => 'queue/delete/<id:\d+>',
                    'route'   => 'queue/delete',
                    'defaults'=> [
                        'id' => 0
                    ]
                ],
                [
                    'pattern' => 'queue/<id:\d+>',
                    'route'   => 'queue/view',
                    'defaults'=> [
                        'id' => 0
                    ]
                ],
            ]
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => ['127.0.0.1', '::1', '192.168.0.*', '192.168.178.20'],
    ];
}


return $config;
