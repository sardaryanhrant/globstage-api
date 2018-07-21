<?php

$db = require __DIR__.'/db.php';
$keys = require __DIR__.'/keys.php';
$params = require __DIR__.'/params.php';
$container = require __DIR__.'/container.php';

return [
    'id'         => 'vetais-api',
    'basePath'   => dirname(__DIR__),
    'bootstrap'  => ['log'],
    'params'     => $params,
    'modules'    => [
        'v1' => [
            'class' => 'app\modules\v1\Module',
        ],
    ],
    'components' => [
        'request'    => [
            'enableCsrfCookie' => false,
            'parsers'          => [
                'application/json' => 'yii\web\JsonParser',
            ],
        ],
        'log'        => [
            'traceLevel'    => 3,
            'flushInterval' => 1,
            'targets'       => [
                'file' => [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                    'logFile' => '@app/runtime/logs/main.log',
                ],
            ],
        ],
        'urlManager' => [
            'enablePrettyUrl'     => true,
            'enableStrictParsing' => true,
            'showScriptName'      => false,
            'rules'               => [
                'test-scheme' => 'test/scheme',
                'test-rels' => 'test/relations',
                [
                    'class'         => 'yii\rest\UrlRule',
                    'controller'    => 'v1/user',
                    'extraPatterns' => [
                        'POST token'     => 'token',
                        'OPTIONS token'     => 'options',

                        'OPTIONS token' => 'options',
                        'GET {id}/getfriends' => 'getfriends',

                        'PUT '=>'updateuser',
                        'OPTIONS updateuser'=>'options',

                        'PUT addfriend/{id}'     => 'addfriend',
                        'OPTIONS addfriend/{id}'  => 'options',

                        'DELETE deletefriend/{id}'  => 'deletefriend',
                        'OPTIONS deletefriend/{id}' => 'options',

                        'PUT addchatlist/{id}'  => 'addchatlist',
                        'OPTIONS addchatlist/{id}' => 'options',

                        'DELETE removeuserfromchatlist'  => 'removeuserfromchatlist',
                        'OPTIONS removeuserfromchatlist' => 'options',

                        'PUT adduserblacklist'  => 'adduserblacklist',
                        'OPTIONS adduserblacklist' => 'options',    

                        'PUT updatecontact'  => 'updatecontact',
                        'OPTIONS updatecontact' => 'options',  
                        
                        'PUT updatepersonalinfo'  => 'updatepersonalinfo',
                        'OPTIONS updatepersonalinfo' => 'options', 

                        'GET getchatlist'  => 'getchatlist',
                        'OPTIONS getchatlist' => 'options', 

                        
                    ],
                ],
                [
                    'class'         => 'yii\rest\UrlRule',
                    'controller'    => 'v1/messages',
                    'extraPatterns' => [
                        'POST'     => 'createmessage',
                        'OPTIONS'     => 'options',

                        // 'DELETE'   => 'deletemessage',
                        // 'OPTIONS'  => 'options',

                        'GET getmessagebyuserid/{id}'   => 'getmessagebyuserid',
                        'OPTIONS getmessagebyuserid/{id}'  => 'options',
                    ]
                ],
                [
                    'class'         => 'yii\rest\UrlRule',
                    'controller'    => 'v1/files',
                    'extraPatterns' => [
                        'POST'     => 'upload',
                        'OPTIONS'     => 'options',
                    ]
                ],
                [
                    'class'         => 'yii\rest\UrlRule',
                    'controller'    => 'v1/groups',
                    'extraPatterns' => [
                        'POST'     => 'creategroup',
                        'OPTIONS'     => 'options',

                        'DELETE deletegroup/{id}'     => 'deletegroup',
                        'OPTIONS'     => 'options',

                        'GET getowngroups'     => 'getowngroups',
                        'OPTIONS getowngroups' => 'options',

                        // 'PUT'     => 'updategroup',
                        // 'OPTIONS' => 'options',
                    ]
                ],
                [
                    'class'         => 'yii\rest\UrlRule',
                    'controller'    => 'v1/posts',
                    'extraPatterns' => [
                        'POST'     => 'createpost',
                        'OPTIONS'     => 'options',

                        'GET {id}'     => 'getpostsbywallid',
                        'OPTIONS {id}'     => 'options',

                        'GET getnews'     => 'getnews',
                        'OPTIONS getnews'     => 'options',

                        // 'DELETE {id}'     => 'deletepost',
                        // 'OPTIONS {id}'     => 'options',

                    ]
                ],
                [
                    'class'         => 'yii\rest\UrlRule',
                    'controller'    => 'v1/comments',
                    'extraPatterns' => [
                        'POST'     => 'createcomment',
                        'OPTIONS'     => 'options',

                    ]
                ],
//                ['class' => 'yii\rest\UrlRule', 'controller' => $routes]
                [
                    'class' => 'app\common\components\entity\EntityUrlRule',
                    'pluralize' => false,
                    'extraPatterns' => [
                        'GET view/{id}' => 'view',
                        // 'GET {id}' => 'viewusers',
                        'POST sms' => 'sms',                       
                        'GET relationships/{entity}' => 'getrel',
                        'GET {id}/relationships/{entity}' => 'getrel',
                        'GET {id}/{entity}' => 'getrel',
                        'POST {id}/{entity}' => 'addrel',
                        'DELETE {parent_id}/{entity}/{id}' => 'delrel',
                    ]
                ]
            ],
        ],
        'user'       => [
            'identityClass' => 'app\common\models\UserModel',
            'enableSession' => false,
        ],
        'db'         => $db,
        'jwt'        => [
            'class'          => 'app\common\components\Jwt',
            'privateKeyFile' => $keys['privateKeyFile'],
            'publicKeyFile'  => $keys['publicKeyFile'],
        ],
        'formatter' => [
            'class' => 'yii\i18n\Formatter',
            'dateFormat' => 'php:Y-m-d',
        ],
        'response'   => [
            'format' => yii\web\Response::FORMAT_JSON,     
        ],
        'errorHandler' => [
            'class' => 'app\common\components\ErrorHandler',
        ],
        'fileService' => [
            'class' => 'app\common\components\FileService',
            'availableTypes' => ['pdf', 'xls', 'xlsx', 'doc', 'docx', 'txt', 'jpg', 'png', 'zip', 'rar'],
            'repository' => [
                'class' => app\common\components\media\MediaRepositoryInterface::class,
                'path' => '/upload/file',
                'depth' => 3
            ]
        ],
    ],
    'container' => $container
];