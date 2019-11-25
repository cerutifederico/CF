<?php

require_once 'class_db.php';
require_once 'sanitize.php';
require_once 'class_user.php';
require_once 'class_match.php';

class sagVRG {

    var $db_conn;

    public function sagVRG() {
        $this->db_conn = new MySQLConn();
    }

    public function generateChart($ranking_from, $ranking_to, $ranking_type, $nazione, $provincia, $level, $limit, $offset, $search = "", $circolo = "", $user_junior = 0) {
        $ranking_type = sanitize($ranking_type, PARANOID);
        $user_junior = sanitize($user_junior, INT);

        $condition = array();

        if (trim($nazione) != "") {
            array_push($condition, " nationality = '" . $nazione . "'");
        }

        if (trim($provincia) != "") {
            array_push($condition, " id_province = '" . $provincia . "'");
        }

        if (trim($search) != "") {
            $search = sanitize($search, PARANOID);
            array_push($condition, " surname LIKE '%" . $search . "%'");
        }        

        if (count($condition) > 0) {
            $cond_txt = "( " . implode(" AND ", $condition) . " ) ";
        } else {
            $cond_txt = "";
        }

        $query = "SELECT id_user, name, surname, nationality, last_level, level_group, date_registration, win, lose, draw, ranking, best_ranking, score_general as score FROM sag_charts JOIN sag_users ON sag_users.id = id_user WHERE sag_users.user_status = 1 ORDER BY score_general DESC,ranking ASC,name,surname";
        $res = $this->db_conn->query($query);
        return $res;
    }

    public function addPlayerToChart($user_id) {
        $user_id = sanitize($user_id, INT);
        $query = "INSERT INTO sag_charts(id_user)VALUES('" . $user_id . "')";
        $res = $this->db_conn->insert($query);
        return $res;
    }

    public function saveNewPoint($player_id, $point, $id_type_of, $type_of) {
        $query = "INSERT INTO sag_users_point(id_user,id_type_of, type_of, points, date_inserted)VALUES(
            '" . $player_id . "',
            '" . $id_type_of . "',
            '" . $type_of . "',
            '" . $point . "',
            '" . date("Y-m-d") . "')";
        $res = $this->db_conn->insert($query);
        return $res;
    }

    public function updateChart($player, $point, $type_of) {
        $query = "SELECT COUNT(*) as conta FROM sag_charts WHERE id_user = " . $player;
        $res = $this->db_conn->query($query);
        if ($res[0]['conta'] == 0) {
            $this->addPlayerToChart($player);
        }
        $query = "UPDATE sag_charts SET score_match = (SELECT SUM(points) FROM sag_users_point WHERE id_user = " . $player . " AND type_of = 'challenge') WHERE id_user = " . $player;
        $this->db_conn->update($query);

        $query = "UPDATE sag_charts SET score_general = score_match WHERE id_user = " . $player;
        $this->db_conn->update($query);
       
        $list = $this->generateChart(date("Y-m-d"), date("Y-m-d"), "", "", "", "", 100000000, 0);
        
        $x = 1;
        foreach ($list as $s) {
            $query = "UPDATE sag_users SET ranking = " . $x . " WHERE id = " . $s['id_user'];
            $this->db_conn->update($query);
            $x++;
        }
    }

    //DA QUI
    public function convertToVRG($source_type, $source_value) {
        
    }

    public function addUserVRG($user_id, $level_value) {
        $user_id = sanitize($user_id, INT);
        $level_value = sanitize($level_value, INT);
        $query = "INSERT INTO users_vrg(id_user,vrg,date_updated)VALUES('" . $user_id . "','" . $level_value . "','" . date("Y-m-d") . "')";
        $res = $this->db_conn->query($query);
        return $res;
    }

    public function getLevel($vrg, $vrg_junior = 0) {
        $vrg = sanitize($vrg, INT);
        $query = "SELECT * FROM vrg_tables WHERE " . $vrg . " >= vrg_da AND " . $vrg . " <= vrg_a AND vrg_junior = " . $vrg_junior . " LIMIT 1";
        $res = $this->db_conn->query($query);
        return $res[0];
    }

    private function trovaCoefficiente($vrg_attuale) {
        $vrg_attuale = sanitize($vrg_attuale, INT);
        $query = "SELECT * FROM vrg_tables WHERE " . $vrg_attuale . " >= vrg_da AND " . $vrg_attuale . " <= vrg_a";
        $res = $this->db_conn->query($query);
        return $res[0]['coefficient'];
    }

    public function calculateNewVRG($vrg_attuale, $partite_vinte, $vrg_avversari) {
        $totale_partite = count($vrg_avversari);
        $differenze_vrg = array();
        for ($x = 0; $x < $totale_partite; $x++) {
            $differenze_vrg[$x] = $vrg_attuale - $vrg_avversari[$x];
        }
        $attDiff = array(0, 4, 11, 18, 26, 33, 40, 47, 54, 62, 69, 77, 84, 92, 99, 107, 114, 122, 130, 138, 146, 154, 163, 171, 180, 189, 198, 207, 216, 226, 236, 246, 257, 268, 279, 291, 303, 316, 329, 345, 358, 375, 392, 412, 433, 457, 485, 518, 560, 620, 736);
        $attPerc = array(50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 89, 89, 89, 89, 89, 89, 89, 89, 89, 89, 89);
        $att = array();
        for ($x = 0; $x < $totale_partite; $x++) {
            if ($vrg_avversari[$x] > 0) {
                for ($y = 0; $y < 51; $y++) {
                    if (abs($differenze_vrg[$x]) >= $attDiff[$y]) {
                        $percent = $attPerc[$y];
                        if ($differenze_vrg[$x] >= 0) {
                            $att[$x] = $percent;
                        } else {
                            $att[$x] = 100 - $percent;
                        }
                    }
                }
            }
        }
        $somma = 0;
        for ($x = 0; $x < $totale_partite; $x++) {
            $somma += floor($att[$x]);
        }
        //echo "Somma percentuali attese ".$somma."<br/>";
        $somma = $somma / 100;
        //echo "Punti Attesi ".$somma."<br/>";
        $punti_attesi_arr = floor(($somma + 0.04) * 10) / 10;
        //echo "Punti Attesi Arrotondati ".$punti_attesi_arr."<br/>";
        $val1 = round($partite_vinte * 10);
        //echo "Partite vinte ".$partite_vinte."<br/>";
        $val2 = round($punti_attesi_arr * 10);
        $valore = ($val1 - $val2) / 10;
        //echo "Differenza Fatti - Attesi ".$valore."<br/>";
        $coeff_k = $this->trovaCoefficiente($vrg_attuale);
        $punti_vrg = round($valore * $coeff_k);
        //echo "Differenza Punti VRG ".$punti_vrg."<br/>";        
        $vrg_nuovo = $vrg_attuale + $punti_vrg;
        return $vrg_nuovo;
    }

    private function saveNewVRG($player_id, $vrg_value, $delta_vrg, $id_type_of, $type_of) {
        $level = $this->getLevel($vrg_value);
        $query = "UPDATE users SET level_group = '" . $level['group'] . "', last_level = '" . $vrg_value . "' WHERE id = " . $player_id;
        $this->db_conn->update($query);
        $usr = new iWDUser();
        $usr_detail = $usr->getDetail($player_id);
        if ($vrg_value > $usr_detail['best_level']) {
            $usr->updateBestLevel($player_id, $vrg_value);
        }
        $query = "INSERT INTO users_vrg(id_user,id_type_of, type_of, vrg, increment, date_updated)VALUES(
            '" . $player_id . "',
            '" . $id_type_of . "',
            '" . $type_of . "',
            '" . $vrg_value . "',
            '" . $delta_vrg . "',
            '" . date("Y-m-d") . "')";
        $res = $this->db_conn->insert($query);
        return $res;
    }

    public function getStartAndEndDate($week, $year) {
        $dto = new DateTime();
        $dto->setISODate($year, $week);
        $ret['week_start'] = $dto->format('Y-m-d');
        $dto->modify('+6 days');
        $ret['week_end'] = $dto->format('Y-m-d');
        return $ret;
    }

    public function updateWeeklyChart($player) {
        $day = date("j");
        $month = date("n");
        $year = date("Y");
        $this_week = $this->getWeekByDate($year, $month, $day);

        if ($this_week > 1) {
            $year = $year - 1;
        }
        $past_week = $this->getStartAndEndDate($this_week, $year);

        $query = "SELECT COUNT(*) as conta FROM charts WHERE id_user = " . $player;
        $res = $this->db_conn->query($query);
        if ($res[0]['conta'] == 0) {
            $this->addPlayerToChart($player);
        }

        $query = "UPDATE charts SET score_match = (SELECT SUM(points) FROM users_point WHERE id_user = " . $player . " AND type_of = 'challenge' AND date_inserted >= '" . $past_week['week_start'] . "') WHERE id_user = " . $player;
        $this->db_conn->update($query);

        $somma = 0;
        $query = "SELECT points FROM users_point WHERE date_inserted >= '" . $past_week['week_start'] . "' AND id_user = " . $player . " AND (type_of = 'tournament' OR type_of = 'tournament_round') ORDER BY points DESC LIMIT 15";
        $res = $this->db_conn->query($query);
        foreach ($res as $s) {
            $somma += $s['points'];
        }
        $query = "UPDATE charts SET score_tournament = " . $somma . " WHERE id_user = " . $player;
        $this->db_conn->update($query);

        $query = "UPDATE charts SET score_cup = (SELECT SUM(points) FROM users_point WHERE id_user = " . $player . " AND type_of = 'raftcup' AND date_inserted >= '" . $past_week['week_start'] . "') WHERE id_user = " . $player;
        $this->db_conn->update($query);

        $query = "UPDATE charts SET score_general = score_match + score_tournament + score_cup WHERE id_user = " . $player;
        $this->db_conn->update($query);

//        $usr = new iWDUser();
//        $usr_detail = $usr->getDetail($player);
//
//        if ($usr_detail['gender'] == "M") {
//            $list = $this->generateChart(date("Y-m-d"), date("Y-m-d"), "", "", "", "", "maschile", 100000000, 0);
//        } else {
//            $list = $this->generateChart(date("Y-m-d"), date("Y-m-d"), "", "", "", "", "femminile", 100000000, 0);
//        }
//        $x = 1;
//        foreach ($list as $s) {
//            $query = "UPDATE users SET ranking = " . $x . " WHERE id = " . $s['id_user'];
//            $this->db_conn->update($query);
//            $x++;
//        }
    }

    public function countChart($ranking_type, $nazione, $provincia, $level, $sesso, $search, $circolo, $user_junior = 0) {
        $ranking_type = sanitize($ranking_type, PARANOID);
        $user_junior = sanitize($user_junior, INT);

        $condition = array();
        if (trim($level) != "") {
            array_push($condition, " level_group = '" . $level . "'");
        }

        if (trim($nazione) != "") {
            array_push($condition, " nationality = '" . $nazione . "'");
        }

        if (trim($provincia) != "") {
            array_push($condition, " id_province = '" . $provincia . "'");
        }

        if (trim($sesso) != "") {
            if ($sesso == "maschile") {
                array_push($condition, " gender = 'M'");
            } else {
                array_push($condition, " gender = 'F'");
            }
        }

        if (trim($search) != "") {
            $search = sanitize($search, PARANOID);
            array_push($condition, " surname LIKE '%" . $search . "%'");
        }

//      if (trim($circolo) != "") {
//          array_push($condition, " is_eni = 1");
//      }

        if (trim($circolo) != "") {
            if ($circolo == "eni") {
                array_push($condition, " is_eni = 1");
            } else {
                $circolo = sanitize($circolo, INT);
                array_push($condition, " id_circolo_member = '" . $circolo . "'");
            }
        }

        if ($user_junior == 1) {
            array_push($condition, " user_junior = '1' ");
        } else {
            array_push($condition, " user_junior = '0' ");
        }

        if (count($condition) > 0) {
            $cond_txt = "( " . implode(" AND ", $condition) . " ) ";
        } else {
            $cond_txt = "";
        }

        $query = "SELECT * FROM charts JOIN users ON users.id = id_user WHERE " . $cond_txt;
        $res = $this->db_conn->query($query);
        return count($res);
    }

    public function getDetailPoint($id, $type, $player_id) {
        $id = sanitize($id, INT);
        $player_id = sanitize($player_id, INT);
        $type = sanitize($type, PARANOID);
        if ($type == "bracket") {
            $type = "tournament";
        }

        $tour = new iWDTournament();
        if ($type != "raftcup") {
            if ($type != "challenge") {
                $detail_tour = $tour->getTournamentByBracket($id);
            } else {
                $detail_tour = array();
            }
            if ($tour->isDoubleTournament($detail_tour['id'])) {
                $query = "SELECT * FROM users_point_double WHERE (id_user = '" . $player_id . "' OR id_user_double  = '" . $player_id . "') AND id_type_of = '" . $id . "' AND type_of = '" . $detail_tour['tournament_type'] . "'";
            } else {
                $query = "SELECT * FROM users_point WHERE id_user = '" . $player_id . "' AND id_type_of = '" . $id . "' AND type_of = '" . $type . "'";
            }
        } else {
            $match = new iWDMatch();
            $match_detail = $match->getDetail($id);
            if ($match_detail['match_type'] == "double") {
                if ($match_detail['type_ref'] == "round") {
                    $round_detail = $tour->getRoundMatchDetail($match_detail['id_ref']);
                    $tour_detail = $tour->getDetail($round_detail['id_tournament']);
                } else {
                    $bracket = $tour->getBracketDetail($match_detail['id_ref']);
                    $tour_detail = $tour->getDetail($bracket['id_tournament']);
                }
                if ($tour_detail['tournament_type'] == "squadre maschile") {
                    $double_type = "doppio maschile";
                } elseif ($tour_detail['tournament_type'] == "squadre femminile") {
                    $double_type = "doppio femminile";
                } elseif ($tour_detail['tournament_type'] == "squadre misto") {
                    $double_type = "doppio misto";
                }
                $query = "SELECT * FROM users_point_double WHERE (id_user = '" . $player_id . "' OR id_user_double  = '" . $player_id . "') AND id_type_of = '" . $id . "' AND type_of = '" . $double_type . "'";
            } else {
                $query = "SELECT * FROM users_point WHERE id_user = '" . $player_id . "' AND id_type_of = '" . $id . "' AND type_of = '" . $type . "'";
            }
        }
        $res = $this->db_conn->query($query);
        return $res[0]['points'];
    }

    // DA SISTEMARE!
    public function addInsertBestSocialRanking($id_user_male, $score_male, $id_user_female, $score_female) {
        $id_user_male = sanitize($id_user_male, INT);
        $id_user_female = sanitize($id_user_female, INT);

        $usr = new iWDUser();
        $male = $usr->getDetail($id_user_male);
        $female = $usr->getDetail($id_user_female);

        $query = "INSERT INTO best_social_ranking(id_user_male,level_male,group_male,score_general_male,id_user_female,level_female,group_female,score_general_female)VALUES(
            '" . $id_user_male . "',
            '" . $male['last_level'] . "',
            '" . $male['level_group'] . "',
            '" . $score_male . "',
            '" . $id_user_female . "',
            '" . $female['last_level'] . "',
            '" . $female['level_group'] . "',
            '" . $score_female . "')";
        $res = $this->db_conn->insert($query);
        //echo $query . "<br/>";
        return $res;
    }

    public function getAllBestSocialRanking($anno) {
        $query = "SELECT best_social_ranking.date_execution, best_social_ranking.level_male, best_social_ranking.level_female, best_social_ranking.group_male, best_social_ranking.group_female, best_social_ranking.score_general_male, best_social_ranking.score_general_female, user_male.id as user_id_male, user_male.name as user_name_male, user_male.surname as user_surname_male, user_male.user_image_path as user_image_path_male, user_male.nationality as nationality_male, user_female.id as user_id_female, user_female.name as user_name_female, user_female.surname as user_surname_female, user_female.user_image_path as user_image_path_female, user_female.nationality as nationality_female FROM best_social_ranking JOIN users as user_male ON best_social_ranking.id_user_male = user_male.id JOIN users as user_female ON best_social_ranking.id_user_female = user_female.id WHERE YEAR(date_execution) = " . $anno . " ORDER BY date_execution DESC";
        $res = $this->db_conn->query($query);
        return $res;
    }

    public function removePointTournament($user_id, $id_type_of, $type_of) {
        $user_id = sanitize($user_id, INT);
        $id_type_of = sanitize($id_type_of, INT);
        $type_of = sanitize($type_of, PARANOID);
        $query = "DELETE FROM users_point WHERE id_user = '" . $user_id . "' AND id_type_of = '" . $id_type_of . "' AND type_of = '" . $type_of . "'";
        $res = $this->db_conn->update($query);
        return $res;
    }

    public function removeDoublePointTournament($user_id, $user_id_double, $id_type_of, $type_of) {
        $user_id = sanitize($user_id, INT);
        $user_id_double = sanitize($user_id_double, INT);
        $id_type_of = sanitize($id_type_of, INT);
        $type_of = sanitize($type_of, PARANOID);
        $query = "DELETE FROM users_point_double WHERE ((id_user = '" . $user_id . "' AND id_user_double = '" . $user_id_double . "') OR (id_user = '" . $user_id_double . "' AND id_user_double = '" . $user_id . "'))  AND id_type_of = '" . $id_type_of . "' AND type_of = '" . $type_of . "'";
        $res = $this->db_conn->update($query);
        return $res;
    }

    public function getDetail($user_id, $id_type_of, $type_of) {
        $user_id = sanitize($user_id, INT);
        $id_type_of = sanitize($id_type_of, INT);
        $type_of = sanitize($type_of, PARANOID);
        $query = "SELECT * FROM users_vrg WHERE id_user = '" . $user_id . "' AND id_type_of = '" . $id_type_of . "' AND type_of = '" . $type_of . "'";
        $res = $this->db_conn->query($query);
        return $res[0];
    }

    public function removeVRG($id) {
        $id = sanitize($id, INT);
        $query = "DELETE FROM users_vrg WHERE id = '" . $id . "'";
        $res = $this->db_conn->update($query);
        return $res;
    }

}

?>