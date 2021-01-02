<?php

Namespace Src\Controllers;
Use Sys as System;
Use Localization as Localization;

Class Home {
    
    public function __construct() {
        global $urlvars;
        extract($urlvars);
        global $http_request;
        global $Language;
        global $userID;
        global $lng;
        global $connection;
        global $_Controller;

        // Calling View class with current Controller
        $this->_view = new System\View($_Controller);

        // Example of using Model
        $data = $http_request->api(
            "GetNotifications",
            "Src\Model\Misc\Notifications",
            [
                $http_request->webvar("Variable1", _GET, "string", "GetNotifications"),
                $http_request->webvar("OtherVariable2", _GET, "int", "GetNotifications"),
                $http_request->webvar("AnotherVariable3", _GET, "date", "GetNotifications")
            ],
            FALSE, // FALSE or _JSON. Later will stop further script execution and retrun JSON Serialized output.
            _GET
        );

        // Passing parameters to the front
        $ProgramLocalVars = [
            "Lng" => $lng,
            "Vars" => $Language->Import(["LngHome", "LngFooter"]),
            "userID" => $userID,
            "DataFromModel" => $data
        ];

        // Creating Front-End variables
        $this->_view->CreateLocalVars($ProgramLocalVars);
        // Creating JS File (e.g. home.js)
        $this->_view->CreateLocalScript($_Controller);

        // Reading title from Localization and passing it to templater ( e.g. can be used as {{title}} )
        $Titles = $Language->Import("LngTitles");
        $this->_view->set("title", $Titles[$_Controller]);
        
        // Reading from Buffer.
        $this->_view->output();

    }

}

?>