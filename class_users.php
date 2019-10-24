
<?
require_once 'class_db.php'; //classe per la connessione al db
require_once 'sanitize.php'; //classe per il controllo delle variabili d'ingresso
require_once 'class_challenge.php'; //classe per la gestione delle sfide
require_once 'class_notification.php'; //classe per le notifiche via mail

class sagUser {

    var $db_conn;

    public function sagUser() {
        $this->db_conn = new MySQLConn();
    }

    public function getList() {
        $query = "SELECT users.* FROM users WHERE users.user_status <> -1 AND users.id <> 1 ORDER BY users.id DESC";
        $res = $this->db_conn->query($query);
        return $res;
    }

    public function getListUsersByTessera($tessera) {
        $tessera = $this->db_conn->escapestr($tessera);
        $query = "SELECT users.* FROM users WHERE users.user_status <> -1 AND users.id <> 1 AND users.is_" . $tessera . " = 1 ORDER BY users.id DESC";
        $res = $this->db_conn->query($query);
        return $res;
    }
    
    public function login($login_name, $login_password) {
        $login_name = $this->db_conn->escapestr($login_name);
        $login_password = sha1($login_password);
        $query = "SELECT * FROM users WHERE email = '" . $login_name . "' AND password = '" . $login_password . "' AND user_status = 1";
        $res = $this->db_conn->query($query);
        if ($res[0]['id'] > 0) {
            @session_start();
            $_SESSION['user_id'] = $res[0]['id'];
            $_SESSION['logged'] = 1;            
        } else {
            $res = "login_error";
        }
        return $res;
    }

    public function getDetail($user_id) {
        $user_id = sanitize($user_id, INT);
        $query = "SELECT users.*, abbr FROM users JOIN provinces ON id_province = provinces.id WHERE users.id = " . $user_id;
        $res = $this->db_conn->query($query);
        return $res[0];
    }
    
    public function getScores($user_id) {
        $user_id = sanitize($user_id, INT);
        $query = "SELECT * FROM charts WHERE id_user = " . $user_id;
        $res = $this->db_conn->query($query);
        return $res[0];
    }

    public function getUserByEmail($user_email) {
        $user_email = $this->db_conn->escapestr($user_email);
        $query = "SELECT * FROM users WHERE email = '" . $user_email . "'";
        $res = $this->db_conn->query($query);
        return $res[0];
    }

    public function setActivation($user_sha1_id) {
        $user_sha1_id = sanitize($user_sha1_id, PARANOID);
        $query = "UPDATE users SET user_status = 1 WHERE sha1(id) = '" . $user_sha1_id . "' ";
        $res = $this->db_conn->update($query);
        if ($res >= 0) {
            $query = "SELECT * FROM users WHERE user_status = 1 AND sha1(id) = '" . $user_sha1_id . "'";
            $res = $this->db_conn->query($query);
            @session_start();
            $_SESSION['user_id'] = $res[0]['id'];
            $_SESSION['logged'] = 1;
            $res = $res[0]['id'];
        }
        return $res;
    }

    public function remove($user_id) {
        $user_id = sanitize($user_id, INT);
        $query = "UPDATE users SET user_status = -1 WHERE id = '" . $user_id . "'";
        $res = $this->db_conn->update($query);
        return $res;
    }

    public function create($first_name, $last_name, $birthday, $gender, $nationality, $nation, $place_birthday, $province_birthday, $address, $city, $province, $codfiscale, $telephone, $is_blank, $is_fit, $is_uisp, $is_asc, $is_other, $email, $password) {
        $first_name = $this->db_conn->escapestr($first_name);
        $last_name = $this->db_conn->escapestr($last_name);
        $birthday = $this->db_conn->escapestr($birthday);
        $gender = $this->db_conn->escapestr($gender);
        $nationality = $this->db_conn->escapestr($nationality);
        $nation = $this->db_conn->escapestr($nation);
        $place_birthday = $this->db_conn->escapestr($place_birthday);
        $province_birthday = $this->db_conn->escapestr($province_birthday);
        $address = $this->db_conn->escapestr($address);
        $city = $this->db_conn->escapestr($city);
        $province = $this->db_conn->escapestr($province);
        $codfiscale = $this->db_conn->escapestr($codfiscale);
        $telephone = $this->db_conn->escapestr($telephone);
        $is_blank = $this->db_conn->escapestr($is_blank);
        $is_fit = $this->db_conn->escapestr($is_fit);
        $is_uisp = $this->db_conn->escapestr($is_uisp);
        $is_asc = $this->db_conn->escapestr($is_asc);
        $is_other = $this->db_conn->escapestr($is_other);
        $email = $this->db_conn->escapestr($email);
        $password = sha1($password);
        
        $query = "SELECT * FROM users WHERE email = '" . $email . "'";
        $res_user = $this->db_conn->query($query);

        if ($res_user[0]['id'] == 0) {
            $query = "INSERT INTO users(name,surname,birthday,place_birthday,id_province_birthday,gender,nationality,nation,address,city,id_province,codfisc,telephone,is_blank,is_fit,is_uisp,is_asc,is_other,email,password,user_status,can_challenge)VALUES(
                '" . $first_name . "',
                '" . $last_name . "',
                STR_TO_DATE('" . $birthday . "','%d/%m/%Y'),    
                '" . $place_birthday . "',
                '" . $province_birthday . "',
                '" . $gender . "',
                '" . $nationality . "',
                '" . $nation . "',
                '" . $address . "',
                '" . $city . "',
                '" . $province . "',
                '" . $codfiscale . "',
                '" . $telephone . "',
                '" . $is_blank . "',
                '" . $is_fit . "',
                '" . $is_uisp . "',
                '" . $is_asc . "',
                '" . $is_other . "',
                '" . $email . "',
                '" . $password . "',
                1,
                1)";
            $res = $this->db_conn->insert($query);
            if ($res > 0) {
                $not = new sagNotification();
                $not->sendVerificationMail($res);                
                $query = "INSERT INTO charts(id_user)VALUES('" . $res . "')";
                $this->db_conn->insert($query);               
                $query = "SELECT MAX(ranking) as ranking FROM users WHERE gender = '" . $gender . "'";
                $new_pos = $this->db_conn->query($query);
                $new_pos = $new_pos[0]['ranking'];
                $new_pos++;
                $query = "UPDATE users SET ranking = '" . $new_pos . "', best_ranking = '" . $new_pos . "'  WHERE id = '" . $res . "'";
                $this->db_conn->update($query);
            }
        } else {
            $res = "MAIL-EXISTS";
        }
        return $res;
    }
    
   
    public function update($user_id, $first_name, $last_name, $gender, $nationality, $nation, $place_birthday, $province_birthday, $birthday, $city, $codfiscale, $telephone,  $is_blank, $is_fit, $is_uisp, $is_asc,$is_other, $email, $address, $province, $availability, $racket, $court, $circolo_id, $nickname) {
        $user_id = sanitize($user_id, INT);
        $first_name = $this->db_conn->escapestr($first_name);
        $last_name = $this->db_conn->escapestr($last_name);
        $gender = $this->db_conn->escapestr($gender);
        $nationality = $this->db_conn->escapestr($nationality);
        $nation = $this->db_conn->escapestr($nation);
        $place_birthday = $this->db_conn->escapestr($place_birthday);
        $province_birthday = $this->db_conn->escapestr($province_birthday);
        $birthday = $this->db_conn->escapestr($birthday);
        $city = $this->db_conn->escapestr($city);
        $codfiscale = $this->db_conn->escapestr($codfiscale);
        $telephone = $this->db_conn->escapestr($telephone);
        $is_blank = $this->db_conn->escapestr($is_blank);
        $is_fit = $this->db_conn->escapestr($is_fit);
        $is_uisp = $this->db_conn->escapestr($is_uisp);
        $is_asc = $this->db_conn->escapestr($is_asc);
        $is_other = $this->db_conn->escapestr($is_other);
        $email = $this->db_conn->escapestr($email);
        $address = $this->db_conn->escapestr($address);
        $province = $this->db_conn->escapestr($province);
        $availability = $this->db_conn->escapestr($availability);
        $racket = $this->db_conn->escapestr($racket);
        $court = $this->db_conn->escapestr($court);
        $nickname = $this->db_conn->escapestr($nickname);
        $last_level = sanitize($last_level, INT);
        $circolo_id = sanitize($circolo_id, INT);
        $user_junior = sanitize($user_junior, INT);
        
       
        $query = "UPDATE users SET 
            name = '" . $first_name . "',
            surname = '" . $last_name . "',
            gender = '" . $gender . "',
            birthday = STR_TO_DATE('" . $birthday . "','%d/%m/%Y'),
            nationality = '" . $nationality . "',
            nation = '" . $nation . "',
            place_birthday = '" . $place_birthday . "',
            id_province_birthday = '" . $province_birthday . "',
            city = '" . $city . "',
            codfisc = '" . $codfiscale . "',
            telephone = '" . $telephone . "',
            is_blank = '" . $is_blank . "',
            is_fit = '" . $is_fit . "',
            is_uisp = '" . $is_uisp . "',
            is_asc = '" . $is_asc . "',
            is_other = '" . $is_other . "',
            email = '" . $email . "',
            address = '" . $address . "',
            id_province = '" . $province . "',
            availability = '" . $availability . "',
            racket = '" . $racket . "',
            court = '" . $court . "',
            id_circolo_member = '" . $circolo_id . "',
            nickname = '" . $nickname . "', WHERE id = " . $user_id;

        $res = $this->db_conn->update($query);
        return $res;
    }
  

    public function resetUserPassword($email) {
        $query = "SELECT COUNT(*) as conta FROM users WHERE email = '" . $email . "' AND user_status = 1";
        $res = $this->db_conn->query($query);
        if ($res[0]['conta'] == 0) {
            return "mailnotexists";
        } else {
            $generate = substr(sha1(date("Ymdhis")), 2, 6);
            $query = "UPDATE users SET password = '" . sha1($generate) . "' WHERE email = '" . $email . "' ";
            $res = $this->db_conn->update($query);
            if ($res > 0) {
                $not = new sagNotification();
                $not->sendResetPassword($email, $generate);
                return $res;
            } else {
                return 0;
            }
        }
    }

    public function changePassword($user_id, $password) {
        $user_id = sanitize($user_id, INT);        
        $password = sha1($password);
        $query = "UPDATE users SET password = '" . $password . "' WHERE id = " . $user_id;
        $res = $this->db_conn->update($query);
        return $res;
    }

    public function has_card($user_id) {
        $user_id = sanitize($user_id, INT);
        $query = "SELECT users.is_blank, users.is_fit, users.is_uisp, users.is_asc, users.is_other FROM users WHERE users.user_status = 1 AND users.id = " . $user_id . " LIMIT 1";
        $res = $this->db_conn->query($query);
        if ($res['is_blank'] == 1) {
            return FALSE;
        } elseif ($res['is_fit'] == 1 || $res['is_uisp'] == 1 || $res['is_asc'] == 1 || $res['is_other'] == 1) {
            return TRUE;
        }
    }

    public function has_fit($user_id) {
        $user_id = sanitize($user_id, INT);
        $query = "SELECT users.is_fit FROM users WHERE users.user_status = 1 AND users.id = '" . $user_id . "' LIMIT 1";
        $res = $this->db_conn->query($query);
        return $res['is_fit'];
    }

    public function has_uisp($user_id) {
        $user_id = sanitize($user_id, INT);
        $query = "SELECT users.is_uisp FROM users WHERE users.user_status = 1 AND users.id = '" . $user_id . "' LIMIT 1";
        $res = $this->db_conn->query($query);
        return $res['is_uisp'];
    }

    public function has_asc($user_id) {
        $user_id = sanitize($user_id, INT);
        $query = "SELECT users.is_asc FROM users WHERE users.user_status = 1 AND users.id = '" . $user_id . "' LIMIT 1";
        $res = $this->db_conn->query($query);
        return $res['is_asc'];
    }

    public function has_other($user_id) {
        $user_id = sanitize($user_id, INT);
        $query = "SELECT users.is_other FROM users WHERE users.user_status = 1 AND users.id = '" . $user_id . "' LIMIT 1";
        $res = $this->db_conn->query($query);
        return $res['is_other'];
    }

    public function updateBestRanking($user_id, $best_ranking) {
        $user_id = sanitize($user_id, INT);
        $best_ranking = sanitize($best_ranking, INT);
        $query = "UPDATE users SET best_ranking = '" . $best_ranking . "' WHERE id = " . $user_id;
        $res = $this->db_conn->update($query);
        return $res;
    }
    
    public function getListUsersNation(){
        $query = "SELECT countries.name, countries.alpha_2 FROM users JOIN countries ON users.nation = countries.alpha_2 WHERE users.user_status = 1 GROUP BY users.nation ORDER BY countries.name ASC ";
        $res = $this->db_conn->query($query);
        return $res;
    }
    
    public function getListUsersNationality(){
        $query = "SELECT countries.nationality, countries.alpha_2 FROM users JOIN countries ON users.nationality = countries.alpha_2 WHERE users.user_status = 1 GROUP BY users.nationality ORDER BY countries.nationality ASC ";
        $res = $this->db_conn->query($query);
        return $res;
    }
    
    
    public function updateBestLevel($user_id, $best_level) {
        $user_id = sanitize($user_id, INT);
        $best_level = sanitize($best_level, INT);
        $query = "UPDATE users SET best_level = '" . $best_level . "' WHERE id = " . $user_id;
        $res = $this->db_conn->update($query);
        return $res;
    }
    
    public function searchPlayer($name, $surname, $level, $session_user) {
        $name = $this->db_conn->escapestr($name);
        $surname = $this->db_conn->escapestr($surname);
        $level = $this->db_conn->escapestr($level);
        $session_user = sanitize($session_user, INT);
        $condition = array();

        $sort_by = "users.surname, users.name ASC";
        if (trim($name) != "") {
            array_push($condition, " users.name LIKE '%" . trim($name) . "%' ");
            $sort_by = "users.name ASC";
        }

        if (trim($surname) != "") {
            array_push($condition, " users.surname LIKE '%" . trim($surname) . "%' ");
            $sort_by = "users.surname ASC";
        }

        if (trim($level) != "") {
            array_push($condition, " (users.last_level) <= '" . trim($level) . "' ");
            $sort_by = "last_level DESC";
        }

        if (count($condition) > 0) {
            $query = "SELECT users.id as id, users.nationality, users.name, users.surname, users.user_image_path, users.can_challenge, users.last_level, users.level_group, (SELECT COUNT(*) FROM matches JOIN challenges ON challenges.id = id_ref AND type_ref = 'challenge' WHERE YEAR(date_match) = '" . date("Y") . "' AND ((player_1 = users.id AND player_2 = '" . $session_user . "' ) OR (player_1 = '" . $session_user . "' AND player_2 = users.id)) AND status_challenge > 0 ) as count_challenge FROM users WHERE users.user_status = 1 AND ( " . implode(" AND ", $condition) . " ) AND users.id <> '" . $session_user . "' ORDER BY " . $sort_by;
            $res = $this->db_conn->query($query);
            return $res;
        } else {
            return array();
        }
    }

   public function getLevelProgress($user_id) {
        $cha = new sagChallenge();
        $user_id = sanitize($user_id, INT);
        $query = "SELECT * FROM (SELECT * FROM users_vrg WHERE id_user = '" . $user_id . "' ORDER BY date_updated DESC LIMIT 20) as t ORDER BY t.date_updated, id";
        $list_vrg = array();
        $res = $this->db_conn->query($query);
        foreach ($res as $s) {
            $query = "SELECT * FROM users_vrg WHERE id_user <> " . $user_id . " AND id_type_of = '" . $s['id_type_of'] . "' AND type_of = '" . $s['type_of'] . "'";
            $res_1 = $this->db_conn->query($query);
            $detail = $this->getDetail($res_1[0]['id_user']);
            $record_vrg['my_vrg'] = $s['vrg'];
            $record_vrg['fighter_vrg'] = $res_1[0]['vrg'];
            $record_vrg['fighter_info'] = $res_1[0]['id_user'];
            $record_vrg['fighter_info_label'] = strtoupper($detail['name'] . "<br/>" . $detail['surname']) . "<br/>" . date("d/m/Y", strtotime($s['date_updated']));
            $record_vrg['date_updated'] = $s['date_updated'];
            array_push($list_vrg, $record_vrg);            
        }
        return $list_vrg;
    }

    public function getSUMPointChallenge($user_id, $month, $year) {
        $user_id = sanitize($user_id, INT);
        $month = $this->db_conn->escapestr($month);
        $year = $this->db_conn->escapestr($year);
        $query = "SELECT SUM(points) as points FROM users_point WHERE id_user = '" . $user_id . "' AND  YEAR(date_inserted) = '" . $year . "' AND MONTH(date_inserted) = '" . $month . "' AND type_of = 'challenge' ";
        $res = $this->db_conn->query($query);
        return $res[0]['points'];
    }
    
    public function getAllMatchesByStatus($user_id, $end_status) {
        $user_id = sanitize($user_id, INT);
        $end_status = sanitize($end_status, PARANOID);
        if ($end_status == "vittorie") {
            $cond = "(player_1 = " . $user_id . ") AND end_status IN ('V1','R2','N2') OR (player_2 = " . $user_id . ") AND end_status IN ('V2','R1','N1')";
        } elseif ($end_status == "pareggiate") {
            $cond = "(player_1 = " . $user_id . " OR player_2 = " . $user_id . ") AND end_status IN ('P0')";
        } elseif ($end_status == "sconfitte") {
            $cond = "(player_1 = " . $user_id . ") AND end_status IN ('V2','R1','N1') OR (player_2 = " . $user_id . ") AND end_status IN ('V1','R2','N2')";
        } elseif ($end_status == "tutte") {
            $cond = "(player_1 = " . $user_id . " OR player_2 = " . $user_id . ") AND end_status IN ('V1','V2','R1','R2','N2','N1','P0')";
        }
        $query = "SELECT * FROM matches WHERE " . $cond . " ORDER BY date_match DESC, time_match DESC";
        $res = $this->db_conn->query($query);
        return $res;
    }
       
    public function getLastMatches($user_id, $limit = 10) {
        $user_id = sanitize($user_id, INT);
        $query = 'SELECT * FROM matches WHERE (player_1 = ' . $user_id . ' OR player_2 = ' . $user_id . ' OR player_1_double = ' . $user_id . ' OR player_2_double = ' . $user_id . ') AND end_status IN (\'V1\',\'V2\',\'R1\',\'R2\',\'N2\',\'N1\',\'P0\') ORDER BY date_match DESC, time_match DESC LIMIT ' . $limit;
        $res = $this->db_conn->query($query);
        return $res;
    }
    
    
    public function getAllLastMatches($limit = 20) {
        $query = "SELECT * FROM matches WHERE end_status IN ('V1','V2') AND date_match <= '" . date("Y-m-d") . "' ORDER BY date_match DESC, time_match DESC LIMIT " . $limit;
        $res = $this->db_conn->query($query);
        return $res;
    }
}
?>
    
    
    
    
    
    
    
    
    
    
    
    
   
