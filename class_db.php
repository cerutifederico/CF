<?php

class MySQLConn {
    
    var $conn;

    public function MySQLConn() {
        $this->conn = @mysql_connect("62.149.150.219", "Sql777975", "qx3nr518fv", true);
        if (!$this->conn) {
            die(mysql_error($this->conn));
        } else {
            $db_selected = @mysql_select_db("Sql777975_4", $this->conn);
            $sql = "SET NAMES 'utf8'";
            @mysql_query($sql, $this->conn);
            if (!$db_selected) {
                die(mysql_error($this->conn));
            }
        }
    }

    public function connect() {
        $this->conn = @mysql_connect(SERVER, USERNAME, PASSWORD, true);
        if (!$this->conn) {
            new EventLog(2, 'MySQL', mysql_error($this->conn));
            die('Check the log for more details.');
        } else {
            $db_selected = @mysql_select_db(DATABASE, $this->conn);
            $sql = "SET NAMES 'utf8'";
            @mysql_query($sql, $this->conn);
            if (!$db_selected) {
                die('Check the log for more details.');
            }
        }
    }

    public function rem_connect($ip, $username, $password) {
        $this->conn = @mysql_connect($ip, $username, $password);
        if (!$this->conn) {
            die('Check the log for more details.');
        } else {
            $db_selected = @mysql_select_db(DATABASE, $this->conn);
            $sql = "SET NAMES 'utf8'";
            @mysql_query($sql, $this->conn);
            if (!$db_selected) {
                die('Check the log for more details.');
            }
        }
    }

    public function Login($username, $password) {
        $username = mysql_escape_string($username);
        $password = sha1($password);
        $query = "SELECT * FROM utenti WHERE username = '" . $username . "' AND password = '" . $password . "'";
        $res = mysql_query($query, $this->conn);
        if (!$res) {
            echo mysql_error($res);
            return 0;
        } else {
            $line = @mysql_fetch_array($res, MYSQL_ASSOC);
            return $line;
        }
    }

    public function escapestr($str) {
        if (get_magic_quotes_gpc()) {
            $str = stripslashes($str);
        } else {
            $str = $str;
        }
        return @mysql_real_escape_string($str);
    }

    public function countaffectrow($query) {
        $res = @mysql_query($query, $this->conn);
        if (!$res) {
            die('Check the log for more details.');
        } else {
            return @mysql_num_rows($res);
        }
    }

    public function countrow() {
        return @mysql_affected_rows($this->conn);
    }

    public function lastInsertID() {
        return @mysql_insert_id($this->conn);
    }

    public function insert($query) {
        $res = @mysql_query($query, $this->conn);
        if (!$res) {
            echo mysql_error($this->conn);
        } else {
            return @mysql_insert_id($this->conn);
        }
    }

    public function update($query) {
        $res = @mysql_query($query, $this->conn);
        if (!$res) {
            die('Check the log for more details.');
        } else {
            return @mysql_affected_rows($this->conn);
        }
    }

    public function query($query) {
        $res = @mysql_query($query, $this->conn);
        if (!$res) {
            die('Check the log for more details.');
        } else {
            $got = array();
            while ($line = @mysql_fetch_array($res, MYSQL_ASSOC)) {
                array_push($got, $line);
            }
            return $got;
        }
    }

    public function getIdByUser($username) {
        $query = "SELECT id FROM users WHERE username = '" . $username . "'";
        $res = @mysql_query($query, $this->conn);
        if (!$res) {
            new EventLog(2, 'MySQL', mysql_error($this->conn));
            die('Check the log for more details.');
        } else {
            $line = @mysql_fetch_array($res, MYSQL_ASSOC);
            return $line['id'];
        }
    }

    public function close() {
        //PHP B id=30525
        //@mysql_close($this->conn);
    }

}

?>
