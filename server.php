<?php

require_once(__DIR__ . '/bootstrap.php');


$http = new \fish\lib\HttpServer();
$http->setRoot(APP_PATH . "/application/www");
$http->setArgv($argv);
$http->setOptions(["log_file" => "/var/tmp/http_test.log"]);
$http->run();
    