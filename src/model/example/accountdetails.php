<?php

Namespace Src\Model\Example;

Class AccountDetails {
    
    // Simple Select
    public function getPersonalInfo ($AccountID) {
        global $connection;
    
        $table = "AccountPersonalInfo";
        $filter = "WHERE tbl.AccountID =" . $AccountID;
        $data = array(
            ['*']
        );
        return $connection->select($table, $data, $filter);
    }

    // Select with JOIN
    public function getPersonalInfoCard ($AccountID) {
        global $connection;
    
        $join = [
            "jn1" => ["Table2", "ID", "tbl.AccountID"] // "prefix" => ["Joined table", "Mapping Field1", "Mapping Field2"]
        ];
        $table = "Table1";
        $filter = "WHERE tbl.AccountID =" . $AccountID;
        $data = [
            "tbl.DisplayName",
            "jn1.Field2",
            "jn1.Field3",
            "jn1.Field4"
        ];
        
        return $connection->select($table, $data, $filter, $join);
    }

    // Update Function
    public function UpdatePassword($newPass, $AccountID) {
        global $connection;

        $crypt = sha1($newPass);
        $data = array(
            "Password" => $crypt
        );
        $connection->update("AccountSecurity", $data, "WHERE ID = " . $AccountID);
        return true;
    }

}

?>
