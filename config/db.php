<?php

return (YII_ENV=="dev") ? [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=localhost;dbname=lagrande_parser',
    'username' => 'root',
    'password' => '2c4uk915',
    'charset' => 'utf8'
] : [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=localhost;dbname=parserbtp',
    'username' => 'u_parserbtp',
    'password' => '7LLzCpJg',
    'charset' => 'utf8'
];
