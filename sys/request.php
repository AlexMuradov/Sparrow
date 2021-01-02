<?php

Namespace Sys;

Class Request {

    public $_Request;
    public function __construct() {
        global $urlvars;
        global $lng;
        global $userID;

        session_start();

        if(isset($_SESSION['UID'])) {
            $userID = $_SESSION['UID'];
        } else {
            $userID = "";
        }
        $this->_Request = array();
        $this->_Request = explode("/", $_SERVER['REQUEST_URI']);
        $lng = $this->_Request[1];

        // Language cookie preferances
        if(!$lng) {
            if(!in_array($lng, ALLOWED_LANGUAGES)) {
                $lng = DEFAULT_LANGUAGE;
            } elseif(isset($_COOKIE['XX_LNG'])) {
                $lng = $_COOKIE['XX_LNG'];
            } else {
                $lng = DEFAULT_LANGUAGE;
            }
        }

        if(isset($_COOKIE['XX_CCY'])) {
            (int)$ccy = $_COOKIE['XX_CCY'];
        } else {
            setcookie('XX_CCY', DEFAULT_CURRENCY, time() + (86400*30), "/");
            (int)$ccy = DEFAULT_CURRENCY;
        }

        // Default page preferance
        if(!isset($this->_Request[2])) { 
            $this->_Request[2] = DEFAULT_PAGE;
        }

        // Getting URL Variables
        unset ($this->_Request[0]);
        unset ($this->_Request[1]);
        
        $url_vars = array();

        foreach($this->_Request as $key => $r) {
            if(strpos($r, ':') !== false) {
                unset ($this->_Request[$key]);
                array_push($url_vars, explode(":",$r));
            }
        }

        $urlvars = array();

        foreach($url_vars as $k => $u) {
            unset($url_vars); // Clean input array ? -> Check
            // Checking if KEY is single variable or series in which case we create an array.
            if( array_key_exists($u[0],$urlvars) ) { // Check for KEY Exists -> IF YES -> Then this iteration trying to give us same KEY and we should create an array. 
                if(!is_array($urlvars[$u[0]])) {  // To create array we need to delete Original KEY and create Nested Key, but we need to do it only once.
                    $store = $urlvars[$u[0]];
                    unset($urlvars[$u[0]]);
                    $newKey = 0;
                    $urlvars[$u[0]][$newKey] = $store;
                }
                $newKey = $newKey + 1;
                $urlvars[$u[0]][$newKey] = $u[1]; // Adding value to Nested array
            } else {
                $urlvars[$u[0]] = $u[1]; // Adding value without array
            }
        }
    }

    public function validateDate($date, $format = 'Y-m-d')
    {
        $d = \DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }

    public function error() {
        header("Location: /error.html");
        exit;
    }

    public function defendVar($var, $type = false, $min = false, $max = false, $binder = false) {
        global $urlvars;
        
        $urlvars["api__Required"] = 1; // Enable default protected vars

        if(array_key_exists("api__" . $binder, $urlvars)) {
            if($urlvars["api__" . $binder]) {
                
                if(empty($var) && !is_numeric($var)) {
                    $this->error();
                }

                if($type == "float") { 
                    if(is_array($var)) {
                        foreach($var as $k => $v) {
                            $var[$k] = floatval($v);
                            if($max) { 
                                if($v < $min || $v > $max) {
                                    $this->error();
                                }
                            }
                        }
                    } else {
                        $var = floatval($var);
                        if($max) { 
                            if($var < $min || $var > $max) {
                                $this->error();
                            }
                        }
                    }
                }
        
                if($type == "int") { 
                    if(is_array($var)) {
                        foreach($var as $k => $v) {
                            $var[$k] = (int)$v;
                            if($max) { 
                                if($v < $min || $v > $max) {
                                    $this->error();
                                }
                            }
                        }
                    } else {
                        $var = (int)$var;
                        if($max) { 
                            if($var < $min || $var > $max) {
                                $this->error();
                            }
                        }
                    }
                }
                if($type == "date") {
                    if(!$this->validateDate($var)) { 
                        $this->error();
                    }
                }
            }
        }

        return $var;
    }
    
    public function webvar($key, $method, $type = false, $api = false, $min = false, $max = false) {
        global $urlvars;

        if ($api && 
            (
                (isset($urlvars["api__" . $api]) && $urlvars["api__" . $api] == TRUE)
                ||
                (isset($_POST["api__" . $api]) && $_POST["api__" . $api] == TRUE)
            )
        ) {
            $var = "_Input" . ucfirst($key);
            if ($method == "get") {
                if(isset($urlvars[$key])) { $$var = $urlvars[$key]; } else { $$var = FALSE; }
            } else {
                if(isset($_POST[$key])) { $$var = $_POST[$key]; } else { $$var = FALSE; }
            }
            if($type) {
                return $this->defendVar($$var, $type, $min, $max, $api);   
            }
            return $$var;
        }
        elseif (!$api) {
            $var = "_Input" . ucfirst($key);
            if ($method == "get") {
                if(isset($urlvars[$key])) { $$var = $urlvars[$key]; } else { $$var = FALSE; }
            } else {
                if(isset($_POST[$key])) { $$var = $_POST[$key]; } else { $$var = FALSE; }
            }
            if($type) {
                return $this->defendVar($$var, $type, $min, $max, $api);   
            }
            return $$var;
        }
        else {
            return FALSE;
        }
    }

    public function binder($t) {
        print $t;
        return $this;
    } 

    public function bind($binder, $key, $method = "get", $type = false, $empty = false, $min = false, $max = false) {
        global $urlvars;

        if($this->webvar("api__" . $binder, $method)) {

            $binder = $binder . "_data";
            global $$binder;

            if(!isset($$binder)) {
                $$binder = array();
            }

            $var = "_Input" . ucfirst($key);

            if ($method == "post") {
                if(isset($_POST[$key])) { $$var = $_POST[$key]; } else { $$var = ""; }
            } else {
                if(isset($urlvars[$key])) { $$var = $urlvars[$key]; } else { $$var = ""; }
            }

            $this->defendVar($$var, $type, $min, $max, $empty);
            array_push($$binder, $$var);
            return $$var;
        }

    }

    public function bind_static($binder, $key, $value, $method) {
        global $urlvars;

        if($this->webvar("api__" . $binder, $method)) {

            $binder = $binder . "_data";
            global $$binder;

            if(!isset($$binder)) {
                $$binder = array();
            }

            array_push($$binder, $$var);

        }

    }

    public function api($action, $model, $data, $exit = false, $method = "get", $enabler = false) {
        global $urlvars;
        
        if(is_array($action)) {

            if($this->webvar("api__" . $enabler, $method, "int")) { /* 
                Important: For large Service Facades (APIs)
                Change to direct check (e.g. $_POST) without Webvar Function to speed up execution
                */
                $i = 0;

                if($exit) {

                    foreach($action as $k => $v) {
                        $model_var = "model" . $i;
                        $$model_var = new $model[$i];
                        print json_encode(
                            call_user_func_array(array($model_var, $v), $data[$i]),
                            JSON_UNESCAPED_UNICODE
                        ); 

                        $i = $i + 1;
                    }

                } else {

                    foreach($action as $k => $v) {
                        $model_var = "model" . $i;
                        $$model_var = new $model[$i];

                        return call_user_func_array(array($model_var, $v), $data[$i]);
                        $i = $i + 1;
                    }

                }

            }

        } else {

            if($this->webvar("api__" . $action, $method, "int")) { /* 
                Important: For large Service Facades (APIs)
                Change to direct check (e.g. $_POST) without Webvar Function to speed up execution
                */
    
                if($exit) {
                    $model = new $model;
                    print json_encode(
                        call_user_func_array(array($model, $action), $data),
                        JSON_UNESCAPED_UNICODE
                    ); 
    
                    exit();
                } else {
                    $model = new $model;
                    return call_user_func_array(array($model, $action), $data);
    
                }
                           
            }    

        }

    }

}

?>