<?php

Namespace Sys;

Class View {

    protected $_viewfile;
    protected $_viewname;
    protected $_modelcachefile;
    protected $_viewcachefile;
    protected $_data = array();
    protected $_vars = array();
    
    public function __construct($view) {

        $this->_viewname = $view;
        $this->_viewfile = HOME . XX . 'views' . XX . $view . '.html';
        $this->_viewcachefile = HOME . XX . 'views' . XX . 'cache' . XX . $view . '.view.cache.php';
        $this->_modelcachefile = HOME . XX . 'src/model/cache' . XX . $view .'.model.cache.json';
    }

    public function reconstruct() {
        
        $file = file_get_contents($this->_viewfile);
        preg_match_all("~{{(.+?)}}~i", $file, $match);
        if(!isset($match[0][0])) $clean = $file;
        
        foreach ($match[0] as $tags) {
            $phpvar = substr($tags, 2, -2);
            $clean = str_replace($tags, '<?php echo $' . $phpvar . '; ?>', $file);
            $file = $clean;
        }

        $fp = fopen($this->_viewcachefile, 'w');
        fwrite($fp, $clean);
        fclose($fp);

    }

    public function Protected() {
        global $lng;
        if(!isset($_SESSION['UID'])) {
            if(isset($_SESSION['redirect'])) {
                unset($_SESSION['redirect']);
            }
            $_SESSION['redirect'] = $_SERVER['REQUEST_URI'];
            header("Location: /$lng/auth.signin");
            exit();
        } 
    }

    public function set($key, $value) {
        $this->_vars[$key] = $value;
    }

    
    public function CreateLocalVars($programvars, $controller = false) {

        $jslocalizationvars = "";

        if($controller) {
            foreach($controller as $key => $val) {
                $this->_vars[$key] = $controller[$key];
            }

            foreach($controller as $key => $val) {
                $jslocalizationvars .= "var " . $key . " = '" . $controller[$key] . "'; ";
            }
        }
        
        foreach($programvars as $key => $val) {
            if(is_array($val)) {
                $jslocalizationvars .= "var " . $key . " = " . json_encode($val,JSON_UNESCAPED_UNICODE) . "; ";
            } else {
                $jslocalizationvars .= "var " . $key . " = '" . $val . "'; ";
            }  
        }
        $this->_vars['JsLocalizationVars'] = $jslocalizationvars;

    }


    public function CreateLocalVars_OLD($controller,$programvars) {
        global $$controller;

        foreach($$controller as $key => $val) {
            $this->_vars[$key] = $$controller[$key];
        }

        $jslocalizationvars = "";
        foreach($$controller as $key => $val) {
            $jslocalizationvars .= "var " . $key . " = '" . $$controller[$key] . "'; ";
        }
        
        foreach($programvars as $key => $val) {
            if(is_array($val)) {
                $jslocalizationvars .= "var " . $key . " = " . json_encode($val,JSON_UNESCAPED_UNICODE) . "; ";
            } else {
                $jslocalizationvars .= "var " . $key . " = '" . $val . "'; ";
            }  
        }
        $this->_vars['JsLocalizationVars'] = $jslocalizationvars;

    }

    public function CreateLocalScript($controller) {
        global $_maintanance;
        global $_maintananceServer;


        $LocalScript = HOME . XX . "views" . XX . "scripts" . XX . $controller . ".js";
        if(file_exists($LocalScript)) {
            $this->_vars['JsLocalizationScript'] = file_get_contents($LocalScript);
        } else {
            $this->_vars['JsLocalizationScript'] = "";
        }

    }

    public function get($key) {
        return $this->_vars[$key];
    }

    public function webvar($key, $method, $type = false, $empty = false) {
        global $urlvars;
    
        $var = "_Input" . ucfirst($key);

        if($type) {
            if($type != gettype($urlvars[$key])) {
                header("Location: /error.html");
            }
        }
        if($empty) {
            if(empty($urlvars[$key])) {
                header("Location: /error.html");
            }
        }
        if ($method == "get") {
            if(isset($urlvars[$key])) { $$var = $urlvars[$key]; } else { $$var = ""; }
        } else {
            if(isset($_POST[$key])) { $$var = $_POST[$key]; } else { $$var = ""; }
        }
        return $$var;
    }

    public function output($mode = "DEV") {
        global $result;
        global $lng;

        if ($mode == "DEV") {
            $this->reconstruct();
        }
        ob_start();
        $this->_vars["lang"] = $lng;
        extract($this->_vars);
        include($this->_viewcachefile);
        $output = ob_get_contents();
        ob_end_clean();
        echo $output;
    }
 }

?>