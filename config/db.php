<?php
// $db = require __DIR__ . '/db.php';
// test database! Important not to run tests on production or development databases
// $db['dsn'] = 'mysql:host=localhost;dbname=yii2_basic';
return [
    'class' => 'yii\db\Connection',
    'dsn' => 'pgsql:host=192.168.0.128;port=5432;dbname=globstage',
    'username' => 'globstage',
    'password' => 'globstage',
    'charset'=>'UTF8'
];
