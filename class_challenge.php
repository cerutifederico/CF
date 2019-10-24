<?php

require_once 'class_db.php';
require_once 'sanitize.php';
require_once 'class_notification.php';
require_once 'class_vrg.php';
require_once 'class_user.php';
require_once 'class_match.php';

class sagChallenge {

    var $db_conn;

    public function sagChallenge() {
        $this->db_conn = new MySQLConn();
    }
    
    public function getScheduledChallenges($user_id, $filter) {
        $user_id = sanitize($user_id, INT);
        $filter = $this->db_conn->escapestr($filter);
        if ($filter == "tutte") {
            $query = "SELECT challenges.*, player_1, player_2, date_match, time_match FROM challenges JOIN matches ON challenges.id = id_ref AND type_ref = 'challenge' WHERE (player_1 = '" . $user_id . "' OR player_2 = '" . $user_id . "') AND status_challenge = 1 ORDER BY date_match DESC ";
        } else {
            $query = "SELECT challenges.*, player_1, player_2, date_match, time_match FROM challenges JOIN matches ON challenges.id = id_ref AND type_ref = 'challenge'  WHERE (player_1 = '" . $user_id . "' AND player_2 = '" . $filter . "' AND status_challenge = 1 ) OR (player_1 = '" . $filter . "' AND player_2 = '" . $user_id . "' AND status_challenge = 1 ) ORDER BY date_match DESC ";
        }
        $res = $this->db_conn->query($query);
        return $res;
    }
    
    public function getPlayerChallenged($user_id) {
        $user_id = sanitize($user_id, INT);
        $query = "SELECT if(player_1 = ".$user_id.", player_2, player_1) as player_id, SUM(if((player_1 = ".$user_id." AND (end_status = 'V1' or end_status = 'R2' or end_status = 'N2')) OR (player_2 = ".$user_id." AND (end_status = 'V2' or end_status = 'R1' or end_status = 'N1')), 1, 0)) as vinte, SUM(if((player_1 = ".$user_id." AND (end_status = 'V2' or end_status = 'R1' or end_status = 'N1')) OR(player_2 = ".$user_id." AND (end_status = 'V1' or end_status = 'R2' or end_status = 'N2')), 1, 0)) as perse, SUM(if(player_1 = ".$user_id." AND (end_status = 'P0'), 1, 0)) as pareggiate FROM matches WHERE (player_1 = ".$user_id." OR player_2 = ".$user_id.") AND end_status <> '' AND player_1_double = 0 AND player_2_double = 0 GROUP BY player_id ORDER BY date_match DESC, time_match DESC";
        $res = $this->db_conn->query($query);
        return $res;
    }

    public function getDetail($challenge_id) {
        $challenge_id = sanitize($challenge_id, INT);
        $query = "SELECT challenges.*, player_1, score_player_1, player_2, score_player_2, date_match, time_match FROM challenges JOIN matches ON id_ref = challenges.id AND type_ref = 'challenge' WHERE challenges.id = " . $challenge_id;
        $res = $this->db_conn->query($query);
        return $res[0];
    }
    
    public function getActiveChallenges($user_id) {
        $user_id = sanitize($user_id, INT);
        $query = "SELECT challenges.*,player_1, player_2,date_match,time_match,score_player_1,score_player_2 FROM challenges JOIN matches ON challenges.id = id_ref AND type_ref = 'challenge' WHERE YEAR(date_match) = '" . date("Y") . "' AND MONTH(date_match) = '" . date("m") . "' AND (player_1 = '" . $user_id . "' OR player_2 = '" . $user_id . "') AND status_challenge >=0 AND status_challenge < 2 ORDER BY status_challenge ASC, date_match ASC";
        $res = $this->db_conn->query($query);
        return $res;
    }
    
    public function getCountYearChallenges($challenged, $user_id) {
        $challenged = sanitize($challenged, INT);
        $user_id = sanitize($user_id, INT);
        $query = "SELECT COUNT(*) as count_challenge FROM challenges JOIN matches ON challenges.id = id_ref WHERE YEAR(matches.date_match) = '" . date("Y") . "' AND (matches.player_1 = '" . $challenged . "' AND matches.player_2 = '" . $user_id . "') OR (matches.player_1 = '" . $user_id . "' AND matches.player_2 = '" . $challenged . "') AND challenges.status_challenge > 0 ";
        $res = $this->db_conn->query($query);
        return $res[0]['count_challenge'];
    }
    
    public function getCountMonthChallenges($user_id) {
        $user_id = sanitize($user_id, INT);
        $query = "SELECT COUNT(*) as count_challenge FROM matches JOIN challenges ON matches.id_ref = challenges.id AND type_ref = 'challenge' WHERE YEAR(date_match) = '" . date("Y") . "' AND MONTH(date_match) = '" . date("m") . "' AND ((player_1 = '" . $user_id . "' AND status_challenge >= 0 ) OR  (player_2 = '" . $user_id . "' AND status_challenge > 0 )) ";
        $res = $this->db_conn->query($query);
        return $res[0]['count_challenge'];
    }
    
    public function removeChallenge($challenge_id) {
        $challenge_id = sanitize($challenge_id, INT);
        $not = new sagNotification();
        $res_not = $not->sendRemoveChallengeMail($challenge_id);
        if ($res_not == 1) {
            $query = "DELETE FROM challenges WHERE id = '" . $challenge_id . "' ";
            $res = $this->db_conn->update($query);
            
            $query = "DELETE FROM matches WHERE id_ref = '" . $challenge_id . "' AND type_ref = 'challenge'";
            $res = $this->db_conn->update($query);
            
            return $res;
        } else {
            return 0;
        }
    }
    
    #DA QUA NO
    public function getAllFinishChallenges() {
        $query = "SELECT challenges.* FROM challenges WHERE challenges.status_challenge = 2 ORDER BY challenges.date_challenge DESC ";
        $res = $this->db_conn->query($query);
        return $res;
    }
   
    public function declineChallenge($challenge_id) {
        $challenge_id = sanitize($challenge_id, INT);
        $not = new sagNotification();
        $res_not = $not->sendDeclineChallengeMail($challenge_id);
        if ($res_not == 1) {
            $query = "DELETE FROM challenges WHERE id = '" . $challenge_id . "' ";
            $res = $this->db_conn->update($query);
            
            $query = "DELETE FROM matches WHERE id_ref = '" . $challenge_id . "' AND type_ref = 'challenge'";
            $res = $this->db_conn->update($query);
            
            return $res;
        } else {
            return 0;
        }
    }

   public function create($player_1, $player_2) {
        $player_1 = sanitize($player_1, INT);
        $player_2 = sanitize($player_2, INT);
        $month_challenge = $this->getCountMonthChallenges($player_1);
        if ($month_challenge < 4) {
            $query = "INSERT INTO challenges(status_challenge) VALUES ('0')";
            $res = $this->db_conn->insert($query);
            if ($res > 0) {
                $match = new sagMatch();
                $match->saveMatch(date("d/m/Y"), "00:00", '0', $player_1, '0', '0', $player_2, '0', $res, "challenge");
                $not = new sagNotification();
                $not->sendChallengeMail($res);
            }
        } else {
            $res = 0;
        }
        return $res;
    }

    public function updateChallenge($challenge_id, $date, $time, $id_circolo, $address, $city, $id_province, $zip) {
        $challenge_id = sanitize($challenge_id, INT);
        $date = $this->db_conn->escapestr($date);
        $time = $this->db_conn->escapestr($time);
        $id_circolo = sanitize($id_circolo, INT);

        $address = $this->db_conn->escapestr($address);
        $city = $this->db_conn->escapestr($city);
        $id_province = sanitize($id_province, INT);
        $zip = $this->db_conn->escapestr($zip);
        $query = "UPDATE challenges SET 
            id_circolo = '" . $id_circolo . "',
            address = '" . $address . "',
            city = '" . $city . "',
            id_province = '" . $id_province . "',
            zip = '" . $zip . "' WHERE id = " . $challenge_id;
        $res = $this->db_conn->update($query);
        
        $match = new iWDMatch();
        $detail_match = $match->getAssociatedMatch($challenge_id, 'challenge');
        $match->setDateAndTime($detail_match['id'], $date, $time);
        return $res;
    }
    
    public function updateStatusChallenge($challenge_id, $status_challenge) {
        $challenge_id = sanitize($challenge_id, INT);
        $status_challenge = sanitize($status_challenge, INT);
        $end_status = "";
        $match = new iWDMatch();
        $detail_match = $match->getAssociatedMatch($challenge_id, 'challenge');
        if ($detail_match['score_player_1'] > $detail_match['score_player_2']) {
            $end_status = "V1";
        } elseif ($detail_match['score_player_2'] > $detail_match['score_player_1']) {
            $end_status = "V2";
        } elseif ($detail_match['score_player_1'] != '' && $detail_match['score_player_2'] != '' && $detail_match['score_player_1'] == $detail_match['score_player_2']) {
            $end_status = "P0";
        }

        $query = "UPDATE challenges SET status_challenge = '" . $status_challenge . "' WHERE id = " . $challenge_id;
        $res = $this->db_conn->update($query);
        
        $match->updateScoreMatch($detail_match['id'], $detail_match['player_1'], $detail_match['player_2'], '0', '0', $detail_match['score_player_1'], $detail_match['score_player_2'], $end_status);
        
        if ($res > 0) {
            $not = new sagNotification();
            // mail a player 1 con sfida accettata status = 1
            if ($status_challenge == 1) {
                $not->sendAcceptChallengeMail($challenge_id);
            } else if ($status_challenge == 2) {
                $not->sendConfirmScoreChallengeMail($challenge_id);
            } else {
                $not->sendDeclineChallengeMail($challenge_id);
            }
        }
        return $res;
    }
    
    public function disputeStatusChallenge($user_id, $challenge_id,$score_1,$score_2) {
        $user_id = sanitize($user_id, INT);
        $challenge_id = sanitize($challenge_id, INT);
        $score_1 = sanitize($score_1, INT);
        $score_2 = sanitize($score_2, INT);
        $query = "UPDATE challenges SET dispute_challenge = '1' WHERE id = " . $challenge_id;
        $res = $this->db_conn->update($query);
        $not = new sagNotification();
        $res = $not->sendDisputeChallengeMail($user_id, $challenge_id, $score_1, $score_2);
        return $res;
    }
    
    public function setScoreChallenge($challenge_id, $score_player_1, $score_player_2, $set_by, $check_by) {
        $challenge_id = sanitize($challenge_id, INT);
        $score_player_1 = sanitize($score_player_1, INT);
        $score_player_2 = sanitize($score_player_2, INT);
        $set_by = sanitize($set_by, INT);
        $check_by = sanitize($check_by, INT);
        
        $query = "UPDATE challenges SET 
            set_by = '" . $set_by . "',
            check_by = '" . $check_by . "' WHERE id = " . $challenge_id;
        $res = $this->db_conn->update($query);
        
        $match = new sagMatch();
        $detail_match = $match->getAssociatedMatch($challenge_id,'challenge');
        $match->updateScoreMatch($detail_match['id'],$detail_match['player_1'], $detail_match['player_2'], '0', '0', $score_player_1, $score_player_2, "ONLYSCORE");

        if ($res > 0) {
            $not = new sagNotification();
            $not->sendScoreChallengeMail($challenge_id, $set_by, $check_by);
        }
        return $res;
    }
    
    public function askRemoveChallenge($user_id, $challenge_id) {
        $user_id = sanitize($user_id, INT);
        $challenge_id = sanitize($challenge_id, INT);

        $match = new iWDMatch();
        $detail_match = $match->getAssociatedMatch($challenge_id, 'challenge');
        if ($detail_match['player_1'] == $user_id) {
            $set_by = $detail_match['player_1'];
            $check_by = $detail_match['player_2'];
        } else {
            $set_by = $detail_match['player_2'];
            $check_by = $detail_match['player_1'];
        }
        
        $query = "UPDATE challenges SET 
            status_challenge = 1,
            set_by = '" . $set_by . "',
            check_by = '" . $check_by . "',
            ask_remove_challenge = 1 WHERE id = " . $challenge_id;
        $res = $this->db_conn->update($query);
        if ($res > 0) {
            $not = new sagNotification();
            $not->sendAskRemoveChallengeMail($challenge_id, $set_by, $check_by);
        }
        return $res;
    }
    
    public function declineRemoveChallenge($challenge_id) {
        $challenge_id = sanitize($challenge_id, INT);
        $query = "UPDATE challenges SET 
                    status_challenge = 1,
                    set_by = 0,
                    check_by = 0,
                    ask_remove_challenge = 0 WHERE id = " . $challenge_id;
        $res = $this->db_conn->update($query);
        if ($res > 0) {
            $not = new sagNotification();
            $not->sendDeclineRemoveChallengeMail($challenge_id);
        }
        return $res;
    }
    
    public function acceptRemoveChallenge($challenge_id) {
        $challenge_id = sanitize($challenge_id, INT);
        $query = "DELETE FROM challenges WHERE id = '" . $challenge_id . "' ";
        $res = $this->db_conn->update($query);
        
        $query = "DELETE FROM matches WHERE id_ref = '" . $challenge_id . "' AND type_ref = 'challenge'";
        $res = $this->db_conn->update($query);
            
        if ($res >= 0) {
            $not = new sagNotification();
            $not->sendAcceptRemoveChallengeMail($challenge_id);
        }
        return $res;
    }
    
    public function resetDisputeChallenge($challenge_id, $score_player_1, $score_player_2) {
        $challenge_id = sanitize($challenge_id, INT);
        $score_player_1 = sanitize($score_player_1, INT);
        $score_player_2 = sanitize($score_player_2, INT);
        $query = "UPDATE challenges SET dispute_challenge = '0' WHERE id = " . $challenge_id;
        $res_chall = $this->db_conn->update($query);
        
        $query = "UPDATE matches SET score_player_1 = '" . $score_player_1 . "', score_player_2 = '" . $score_player_2 . "' WHERE id_ref = '" . $challenge_id . "' AND type_ref = 'challenge'";
        $res = $this->db_conn->update($query);
        if ($res > 0) {
            $not = new sagNotification();
            $not->sendConfirmAdminScoreChallengeMail($challenge_id);
        }
        return $res;
    }
    
    public function assignChallengePoint($match_id){
        $match = new iWDMatch();
        $detail_match = $match->getDetail($match_id);
        $usr = new iWDUser();
        $player_1 = $usr->getDetail($detail_match['player_1']);
        $player_2 = $usr->getDetail($detail_match['player_2']);
        $vrg = new iWDVRG();
        $new_point = $vrg->calculatePoint($player_1['last_level'], $player_2['last_level'], $detail_match['score_player_1'], $detail_match['score_player_2']);
        $vrg->saveNewPoint($detail_match['player_1'], $new_point['player_1_point'], $detail_match['id_ref'], 'challenge');
        $vrg->saveNewPoint($detail_match['player_2'], $new_point['player_2_point'], $detail_match['id_ref'], 'challenge');
        $vrg->updateChart($detail_match['player_1'], $new_point['player_1_point'], "challenge");
        $vrg->updateChart($detail_match['player_2'], $new_point['player_2_point'], "challenge");
        return 1;
    }
}
?>