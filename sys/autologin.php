<?php

Namespace Sys;

Class Autologin {

    public function __construct() {
        global $connection;
        global $lng;
        global $userID;

        if(isset($_COOKIE['XX_DEVICE_ID']) && !isset($_SESSION['UID'])) {
            
            $deviceID = $_COOKIE['XX_DEVICE_ID'];
            $hashID = $_COOKIE['XX_HASH'];
            $userIP = $_SERVER['REMOTE_ADDR'];
            if (array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER)) {
                $array = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                $userIP = array_pop($array);
            }

            $deviceRecordExists = $connection->exists_flex(
                "AccountSecurityLogs", 
                FALSE, 
                "WHERE deviceID = '$deviceID' AND hashID = '$hashID'"
            );

            $SavedLogin = $connection->select(
                "AccountSecurityLogs", 
                array("userID", "IP"), 
                "WHERE deviceID = '$deviceID' AND hashID = '$hashID'"
            );

            if($deviceRecordExists) {
                if($userIP == $SavedLogin[0]['IP']) {
                    $_SESSION['UID'] = $SavedLogin[0]['userID'];
                    $userID = $_SESSION['UID'];
                }
            } else {
                setcookie('XX_DEVICE_ID', "null", time() + 1, "/");
            }
        }
    }
}
?>