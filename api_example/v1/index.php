<?php

require_once  dirname(__FILE__) . '/configuration.php';

require_once  dirname(__FILE__) . '/sys/__init__.php';

$initialize = new Init();

$http_request = new Sys\Request();

$connection = new Sys\Db();

$connection->connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

$Autologin = new Sys\Autologin();

$Aggregator = new API_Example\v1\Aggregator();

$connection->disconnect();

?>