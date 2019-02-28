<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'name' => 'Test',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'language'          =>  'ru',    // исходный язык для пользователя
    'sourceLanguage'    =>  'en',    // исходный язык, на котором изначально написаны фразы в приложении
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'OKgSUi3NNvu0ReVoj0yXsjR3zeQtEcF6',
        ],
//        rbac или роли доступа
        'authManager'=>
            [
                'class' => 'yii\rbac\DbManager',
//                'cache' => 'cache', //Включаем кеширование (включить когда будет стабильно работать приложение)
                'defaultRoles' => ['guest'],
            ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
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
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                'page/<view:[a-zA-Z0-9-]+>' => 'site/page',
                '' => 'site/index',
                'login' => 'site/login',
            ],
        ],

    ],

//  ### Настройка доступа к страницам, только авторизованным пользователям ###
    'as beforeRequest' => [
        'class' => yii\filters\AccessControl::className(),
        'except' => ['login'],
        'rules' => [
            [
                'actions' => ['login', 'error'],
                'allow' => true,
                'roles' => ['?'],   // гость
//                'ips' => ['192.168.0.*','127.0.0.*'], // авторизация по ip
            ],
            [
//                'actions' => ['logout', 'index', 'error'],
                'allow' => true,
                'roles' => ['@'],   // авторизованные пользователь
            ],
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
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
