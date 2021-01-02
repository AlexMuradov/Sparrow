<?php
 
Class Init {

    public function __construct() {

        // Autoloader
        spl_autoload_register(function ($class) {
            
            $class = str_replace('\\','/',$class);

            if(file_exists(HOME . XX . strtolower($class) . '.php')) {
                
                require_once HOME . XX . strtolower($class) . '.php';
                
            }

    });

    }

}

?>