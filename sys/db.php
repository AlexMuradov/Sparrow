<?php

Namespace Sys;
Use mysqli;

Class Db {
    
    public function connect($h, $u, $p, $db) {
        global $mysqli;
        $mysqli = new mysqli($h, $u, $p, $db);

        if ($mysqli->connect_errno) {
            echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
        }
        $mysqli->query("SET NAMES utf8");
    }

    public function disconnect($h = false, $db = false) {
        global $mysqli;
        $mysqli->close();
    }

    public function exists($table, $column, $value=false) {
        global $mysqli;
        if($value) {
            $result = $mysqli->query("
            Select 
                $column
            From
                $table
            Where
                $column = '$value'");
        } else {
            $result = $mysqli->query("
            Select 
                ID
            From
                $table 
                $column");
        }
        $exists = $result->num_rows;
        if ($exists > 0) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function exists_flex($table, $column = "ID", $filter) {
        global $mysqli;

            $result = $mysqli->query("
            Select 
                ID
            From
                $table
                $filter
            ");

        $exists = $result->num_rows;

        if ($exists > 0) {
            return TRUE;
        } else {
            return FALSE;
        }

    }

    public function select($table, $data, $filter, $join = FALSE) {
        global $mysqli;

        $cols = implode(",", $data);
        $_join = "";
        
        if($join) {
            
            foreach($join as $k => $v) {
                /*if(is_array($v[1])) { // multi mapping join
                    $_join .= " LEFT JOIN " . $v[0] . " " . $k ." ON ";
                    foreach($v[1] as $kk => $vv) {
                        "$kk" = 
                    }
                } else { // single mapping joing*/
                    $_join .= " LEFT JOIN " . $v[0] . " " . $k ." ON ".$k."." . $v[1] . "=" . $v[2];
                //}
            }
        }

        $o = $mysqli->query("
        Select 
        $cols 
        From 
        $table tbl $_join 
        $filter");

        if(is_array($data)) {
            $r = $o->fetch_all(MYSQLI_ASSOC);
        } else {
            $r = $o->fetch_row();
        }
        $n = $o->num_rows;

        if ($n) {
            return $r;
        } else {
            return FALSE;
        }

    }

    public function execute($data) {
        global $mysqli;

        $mysqli->query($data);
    }

    public function id() {
        global $mysqli;
        return $mysqli->insert_id;
    }

    public function select_flex($data) {
        global $mysqli;

        $o = $mysqli->query($data);

       //if(is_array($data)) {
            $r = $o->fetch_all(MYSQLI_ASSOC);
        //} else {
        //    $r = $o->fetch_row();
       //}
        $n = $o->num_rows;

        if ($n) {
            return $r;
        } else {
            return FALSE;
        }
    }

    public function select_debug($table, $data, $filter, $join = FALSE) {
        global $mysqli;

        $cols = implode(",", $data);
        $_join = "";
        
        if($join) {
            
            foreach($join as $k => $v) {
                $_join .= " LEFT JOIN " . $v[0] . " " . $k ." ON ".$k."." . $v[1] . "=" . $v[2];
            }
        }

        $o = "
        Select 
        $cols 
        From 
        $table tbl $_join 
        $filter";

        print $o;
        exit();
    }

    public function insert($table, $data) {
        global $mysqli;

        $cols = implode(",", array_keys($data));
        $vals = "'" . implode("','", $data) . "'";
        if($mysqli->query("
        Insert Into 
            $table ($cols)
        Values ($vals)
        ") === TRUE) return TRUE; else return FALSE;
    }

    public function update($table, $data, $filter = "") {
        global $mysqli;

        $string = "";
        foreach ($data as $k => $v) {
            if ($v == "" && !is_numeric($v)) {
                $string .= "$k = NULL,";
            } else {
                $string .= "$k = '$v',";
            }   
        }
        $string = substr($string, 0, -1);
        if($mysqli->query("
        Update $table SET 
        $string 
        $filter
        ") === TRUE) return TRUE; else return FALSE;

    }

    public function delete($table, $filter = "") {
        global $mysqli;

        $mysqli->query("
        Delete From $table 
        $filter
        ");
    }

}

?>