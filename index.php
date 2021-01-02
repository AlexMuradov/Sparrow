<?php

require_once dirname(__FILE__) . '/configuration.php';

require_once dirname(__FILE__) . '/sys/__init__.php';

$initialize = new Init();

$http_request = new System\Request();

$connection = new Sys\Db();

$connection->connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

$Autologin = new Sys\Autologin();

if(file_exists(HOME . "/localization/" . strtolower($lng) . '.php')) 
{
    $langClass = "Localization\\" . strtolower($lng);

    $Language = new $langClass;

} else {

    $langClass = "Localization\\" . strtolower(DEFAULT_LANGUAGE);

    $Language = new $langClass;
}

$_Controller = str_replace(".", "/", $http_request->_Request[2]);

if(file_exists(HOME . '/src/controllers/' . strtolower($_Controller) . '.php')) 
{
    $c = "Src\Controllers\\" . str_replace(".", "\\", $http_request->_Request[2]);

    $j = new $c;
    
} else {

    $j = new Xx\Page\Home();
}

$connection->disconnect();

?>