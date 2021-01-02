<?php

Namespace API_Example\v1;

Class Aggregator {
    
    public function __construct() {
        global $urlvars;
        extract($urlvars);
        global $http_request;
        global $userID;
        global $lng;
        
        // Example with 1 Model
        $http_request->api(
            "CreateAccount",
            "Src\Model\Finance\Test2",
            [
                $userID,
                $http_request->webvar("AccNumber", _POST, "int", "CreateAccount"),
                $http_request->webvar("HolderName", _POST, "string", "CreateAccount")
            ],
            _JSON,
            _POST
        );

    }
}

?>