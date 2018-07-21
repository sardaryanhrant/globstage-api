<?php
// $db = require __DIR__ . '/db.php';
// test database! Important not to run tests on production or development databases
// $db['dsn'] = 'mysql:host=localhost;dbname=yii2_basic';
return [
    'class' => 'yii\db\Connection',
    'dsn' => 'pgsql:host=127.0.0.1;port=5432;dbname=vetais_test',
    'username' => 'postgres',
    'password' => 'root',
    'charset'=>'UTF8'
];
