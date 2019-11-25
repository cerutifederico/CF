<?php

require_once 'class_db.php';
require_once 'sanitize.php';

class sagSetting {

    var $db_conn;

    public function sagSetting() {
        $this->db_conn = new MySQLConn();
    }

    // TESTED: OK
    public function getProvinces() {
        $query = "SELECT provinces.* FROM provinces ORDER BY provinces.name ASC";
        $res = $this->db_conn->query($query);
        return $res;
    }
    
    
    public function getCountries() {
        $query = "SELECT countries.* FROM countries ORDER BY countries.name ASC";
        $res = $this->db_conn->query($query);
        return $res;
    }
    
    public function getCountryByAlpha2($alpha_2) {
        $alpha_2 = $this->db_conn->escapestr($alpha_2);
        $query = "SELECT countries.* FROM countries WHERE countries.alpha_2 = '" . $alpha_2 . "'";
        $res = $this->db_conn->query($query);
        return $res[0];
    }       
}
?>