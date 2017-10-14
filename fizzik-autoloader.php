<?php

require_once __DIR__ . '/vendor/autoload.php';

$mapping = array(
    'Fizzik\Database\MongoDBDatabase' => __DIR__ . '/Fizzik/Database/MongoDBDatabase.php',
    'Fizzik\Database\MySqlDatabase' => __DIR__ . '/Fizzik/Database/MySqlDatabase.php',
    'Fizzik\Database\RedisDatabase' => __DIR__ . '/Fizzik/Database/RedisDatabase.php',
    'Fizzik\Utility\FileHandling' => __DIR__ . '/Fizzik/Utility/FileHandling.php',
    'Fizzik\Utility\SleepHandler' => __DIR__ . '/Fizzik/Utility/SleepHandler.php',
);

spl_autoload_register(function ($classname) use ($mapping) {
    if (isset($mapping[$classname])) {
        require $mapping[$classname];
    }
});