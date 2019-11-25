<?php

require_once 'class_db.php';
require_once 'sanitize.php';
require_once 'class_challenge.php';

//require_once 'class_tournament.php';
//require_once 'class_team.php';
//require_once 'class_vrg.php';
//require_once 'class_user.php';
//require_once 'class_utente.php';
//require_once 'class_notification.php';

class sagMatch {

    var $db_conn;

    public function sagMatch() {
        $this->db_conn = new MySQLConn();
    }

    public function saveMatch($date, $time, $team_1, $player_1, $player_1_double, $team_2, $player_2, $player_2_double, $id_ref, $type_of, $type_of_match = "") {
        $date = sanitize($date, PARANOID);
        $time = sanitize($time, PARANOID);
        $team_1 = sanitize($team_1, INT);
        $player_1 = sanitize($player_1, INT);
        $player_1_double = sanitize($player_1_double, INT);
        $team_2 = sanitize($team_2, INT);
        $player_2 = sanitize($player_2, INT);
        $player_2_double = sanitize($player_2_double, INT);
        $id_ref = sanitize($id_ref, INT);
        $type_of = sanitize($type_of, PARANOID);

        if ($type_of_match == "") {
            if ($player_1_double > 0 || $player_2_double > 0) {
                $type_of_match = "double";
            } else {
                $type_of_match = "single";
            }
        }

        $query = "INSERT INTO sag_matches(date_match,time_match,team_1,player_1,player_1_double,team_2,player_2,player_2_double,id_ref,type_ref,match_type)VALUES(
            STR_TO_DATE('" . $date . "','%d/%m/%Y'),
            '" . $time . "',
            '" . $team_1 . "',
            '" . $player_1 . "',
            '" . $player_1_double . "',
            '" . $team_2 . "',
            '" . $player_2 . "',
            '" . $player_2_double . "',
            '" . $id_ref . "',
            '" . $type_of . "',
            '" . $type_of_match . "')";
        $res = $this->db_conn->insert($query);
        return $res;
    }

    public function getAssociatedMatch($id_ref, $type_of) {
        $id_ref = sanitize($id_ref, INT);
        $type_of = sanitize($type_of, PARANOID);
        $query = "SELECT * FROM sag_matches WHERE id_ref = '" . $id_ref . "' AND type_ref = '" . $type_of . "' ORDER BY CASE WHEN date_match = '0000-00-00' THEN 1 ELSE 0 END ASC, date_match ASC, time_match ASC, match_type DESC";
        $res = $this->db_conn->query($query);
        if ($type_of == 'challenge') {
            return $res[0];
        } else {
            return $res;
        }
    }

    public function updateScoreMatch($id, $player_1, $player_2, $player_1_double, $player_2_double, $score_player_1, $score_player_2, $status, $no_assigne_point = 0) {
        $id = sanitize($id, INT);
        $player_1 = sanitize($player_1, INT);
        $player_1_double = sanitize($player_1_double, INT);
        $player_2 = sanitize($player_2, INT);
        $player_2_double = sanitize($player_2_double, INT);
        $status = sanitize($status, PARANOID);
        $no_assigne_point = sanitize($no_assigne_point, INT);

        if ($status != "") {
            if ($status == "ONLYSCORE") {
                $query = "UPDATE sag_matches SET
                    score_player_1 = '" . $score_player_1 . "',
                    score_player_2 = '" . $score_player_2 . "',
                    end_status = '',
                    no_assigne_point = " . $no_assigne_point . ",
                    player_1 = '" . $player_1 . "',
                    player_1_double = '" . $player_1_double . "',
                    player_2 = '" . $player_2 . "',
                    player_2_double = '" . $player_2_double . "' WHERE id = '" . $id . "'";
            } else {
                $query = "UPDATE sag_matches SET
                    score_player_1 = '" . $score_player_1 . "',
                    score_player_2 = '" . $score_player_2 . "',
                    end_status = '" . $status . "',
                    no_assigne_point = " . $no_assigne_point . ",
                    player_1 = '" . $player_1 . "',
                    player_1_double = '" . $player_1_double . "',
                    player_2 = '" . $player_2 . "',
                    player_2_double = '" . $player_2_double . "' WHERE id = '" . $id . "'";
            }
            $res = $this->db_conn->update($query);
            if ($res > 0) {
                $detail = $this->getDetail($id);
                if ($detail['type_ref'] == "challenge" && $status != "" && $status != "ONLYSCORE") {
                    $cha = new sagChallenge();
                    $cha->assignChallengePoint($id);
                }
                //** AGGIORNO STATISTICHE DEL GIOCATORE
                if ($status == "V1" || $status == "R2" || $status == "N2") {
                    $this->updateUserMatch($player_1, 1, 0, 0);
                    $this->updateUserMatch($player_1_double, 1, 0, 0);
                    $this->updateUserMatch($player_2, 0, 1, 0);
                    $this->updateUserMatch($player_2_double, 0, 1, 0);
                } elseif ($status == "V2" || $status == "R1" || $status == "N1") {
                    $this->updateUserMatch($player_2, 1, 0, 0);
                    $this->updateUserMatch($player_2_double, 1, 0, 0);
                    $this->updateUserMatch($player_1, 0, 1, 0);
                    $this->updateUserMatch($player_1_double, 0, 1, 0);
                } elseif ($status == "P0") {
                    $this->updateUserMatch($player_1, 0, 0, 1);
                    $this->updateUserMatch($player_1_double, 0, 0, 1);
                    $this->updateUserMatch($player_2, 0, 0, 1);
                    $this->updateUserMatch($player_2_double, 0, 0, 1);
                }
            }
        } else {
            $query = "UPDATE sag_matches SET
                player_1 = '" . $player_1 . "',
                player_1_double = '" . $player_1_double . "',
                player_2 = '" . $player_2 . "',
                player_2_double = '" . $player_2_double . "' WHERE id = '" . $id . "'";
            $res = $this->db_conn->update($query);
        }
        return $res;
    }

    private function updateUserMatch($player_id, $win, $lose, $draw) {
        $player_id = sanitize($player_id, INT);
        $win = sanitize($win, INT);
        $lose = sanitize($lose, INT);
        $draw = sanitize($draw, INT);
        if ($win == 1) {
            $query = "UPDATE sag_users SET win = win + 1 WHERE id = " . $player_id;
        } elseif ($lose == 1) {
            $query = "UPDATE sag_users SET lose = lose + 1 WHERE id = " . $player_id;
        } elseif ($draw == 1) {
            $query = "UPDATE sag_users SET draw = draw + 1 WHERE id = " . $player_id;
        }
        $res = $this->db_conn->update($query);
        return $res;
    }

    public function setDateAndTime($match_id, $date_match, $time_match) {
        $match_id = sanitize($match_id, INT);
        $date_match = $this->db_conn->escapestr($date_match);
        $time_match = $this->db_conn->escapestr($time_match);
        $query = "UPDATE sag_matches SET date_match = STR_TO_DATE('" . $date_match . "','%d/%m/%Y'), time_match = '" . $time_match . "' WHERE id = " . $match_id;
        $res = $this->db_conn->update($query);
        return $res;
    }

    public function getDetail($id) {
        $id = sanitize($id, INT);
        $query = "SELECT * FROM sag_matches WHERE id = '" . $id . "'";
        $res = $this->db_conn->query($query);
        return $res[0];
    }

    /*

      public function getMatchesByIdTournament($tournament_id,$type_ref) {
      $tournament_id = sanitize($tournament_id, INT);
      if($type_ref == 'bracket'){
      $query = "SELECT matches.* FROM tournament_brackets JOIN matches ON matches.id_ref = tournament_brackets.id WHERE tournament_brackets.id_tournament = '" . $tournament_id . "' AND matches.type_ref = 'bracket' ORDER BY matches.id_ref ASC";
      }elseif($type_ref == 'round'){
      $query = "SELECT matches.* FROM tournament_rounds_matches JOIN matches ON matches.id_ref = tournament_rounds_matches.id WHERE tournament_rounds_matches.id_tournament = '" . $tournament_id . "' AND matches.type_ref = 'round' ORDER BY matches.id_ref ASC";
      }
      $res = $this->db_conn->query($query);
      return $res;
      }

      public function getFreePlayersFromTournament($tournament_id,$type_ref) {
      $tournament_id = sanitize($tournament_id, INT);
      $res = 0;
      if($type_ref == "bracket"){
      $query = "SELECT tournaments_players.id_user, users.name, users.surname FROM tournaments_players JOIN users ON tournaments_players.id_user = users.id WHERE id_tournament = '" . $tournament_id . "' AND tournaments_players.id_user NOT IN (SELECT player_1 AS 'other_players' FROM matches WHERE id_ref IN (SELECT id FROM tournament_brackets WHERE id_tournament = '" . $tournament_id . "') AND type_ref = 'bracket' AND player_1 > 0 UNION SELECT player_2 AS 'other_players' FROM matches WHERE id_ref IN (SELECT id FROM tournament_brackets WHERE id_tournament = '" . $tournament_id . "') AND type_ref = 'bracket' AND player_2 > 0)";
      $res = $this->db_conn->query($query);
      }
      //elseif($type_ref == "round"){
      //    $query = "";
      //}
      //echo $query."\n";
      return $res;
      }

      public function getFreeTeamsFromTournament($tournament_id,$type_ref) {
      $tournament_id = sanitize($tournament_id, INT);
      $res = 0;
      if($type_ref == "bracket"){
      $query = "SELECT tournaments_teams.id, tournaments_teams.name FROM tournaments_teams WHERE tournaments_teams.id_tournament = '" . $tournament_id . "' AND tournaments_teams.id NOT IN (SELECT team_1 AS 'other_teams' FROM matches WHERE id_ref IN (SELECT id FROM tournament_brackets WHERE id_tournament = '" . $tournament_id . "') AND type_ref = 'bracket' AND team_1 > 0 UNION SELECT team_2 AS 'other_teams' FROM matches WHERE id_ref IN (SELECT id FROM tournament_brackets WHERE id_tournament = '" . $tournament_id . "') AND type_ref = 'bracket' AND team_2 > 0)";
      $res = $this->db_conn->query($query);
      }
      //elseif($type_ref == "round"){
      //  $query = "";
      //}
      //echo $query."\n";
      return $res;
      }





      /*public function updateMatch($id, $date, $time, $player_1, $player_1_double, $player_2, $player_2_double) {
      $id = sanitize($id, INT);
      $date = sanitize($date, PARANOID);
      $time = sanitize($time, PARANOID);
      $player_1 = sanitize($player_1, INT);
      $player_1_double = sanitize($player_1_double, INT);
      $player_2 = sanitize($player_2, INT);
      $player_2_double = sanitize($player_2_double, INT);
      $query = "UPDATE matches SET
      date_match = STR_TO_DATE('" . $date . "','%d/%m/%Y'),
      time_match = '" . $time . "',
      player_1 = '" . $player_1 . "',
      player_1_double = '" . $player_1_double . "',
      player_2 = '" . $player_2 . "',
      player_2_double = '" . $player_2_double . "' WHERE id = '" . $id . "'";
      $res = $this->db_conn->update($query);
      return $res;
      }

      public function countMatchRemain($id_ref, $type_ref) {
      $id_ref = sanitize($id_ref, INT);
      $type_ref = sanitize($type_ref, PARANOID);
      $query = "SELECT COUNT(*) as mancano FROM matches WHERE end_status = '' AND id_ref = '" . $id_ref . "' AND type_ref = '" . $type_ref . "'";
      $res = $this->db_conn->query($query);
      return $res[0]['mancano'];
      }


      // DA COMPLETARE!!
      public function changeAwayHomeSingleMatch($tournament_id, $id_ref, $type_of) {
      $tournament_id = sanitize($tournament_id, INT);
      $id_ref = sanitize($id_ref, INT);
      $type_of = sanitize($type_of, PARANOID);
      $tour = new iWDTournament();
      $not = new iWDNotification();

      $tour_detail = $tour->getDetail($tournament_id, FALSE);
      $matches = $this->getAssociatedMatch($id_ref, $type_of);

      $update = 0;
      foreach ($matches as $s_match){
      $player_1 = $s_match['player_2'];
      $player_1_double = $s_match['player_2_double'];
      $player_2 = $s_match['player_1'];
      $player_2_double = $s_match['player_1_double'];

      $query = "UPDATE matches SET
      player_1 = '" . $player_1 . "',
      player_1_double = '" . $player_1_double . "',
      player_2 = '" . $player_2 . "',
      player_2_double = '" . $player_2_double . "' WHERE id = '" . $s_match['id'] . "'";
      $res = $this->db_conn->update($query);
      if($res == 1){
      $update++;
      }
      }
      return $update;
      }

      public function removeScoreMatch($tournament_id, $match_id, $type_of) {
      $tournament_id = sanitize($tournament_id, INT);
      $match_id = sanitize($match_id, INT);
      $type_of = sanitize($type_of, PARANOID);


      $tour = new iWDTournament();
      $not = new iWDNotification();
      $ut = new iWDUtente();
      $vrg = new iWDVRG();
      $tour_detail = $tour->getDetail($tournament_id, FALSE);
      $match_detail = $this->getDetail($match_id);

      $player_1 = $match_detail['player_1'];
      $player_1_double = $match_detail['player_1_double'];
      $old_score_player_1 = $match_detail['score_player_1'];
      $player_2 = $match_detail['player_2'];
      $player_2_double = $match_detail['player_2_double'];
      $old_score_player_2 = $match_detail['score_player_2'];
      $old_end_status = $match_detail['end_status'];

      if($type_of == "bracket" && !$tour->isRoundTournament($tour_detail['id'])){

      $query = "SELECT id FROM tournament_brackets WHERE id_tournament = " . $tour_detail['id'] . " AND ref_match_1 = " . $match_detail['id_ref'];
      $res_ref_match_1 = $this->db_conn->query($query);
      if ($res_ref_match_1[0]['id'] > 0) {
      $query = "UPDATE matches SET player_1 = 0, player_1_double = 0, score_player_2 = '', score_player_1 = '' WHERE id_ref = " . $res_ref_match_1[0]['id'] . " AND type_ref = 'bracket'";
      $this->db_conn->update($query);
      }

      $query = "SELECT id FROM tournament_brackets WHERE id_tournament = " . $tour_detail['id'] . " AND ref_match_2 = " . $match_detail['id_ref'];
      $res_ref_match_2 = $this->db_conn->query($query);
      if ($res_ref_match_2[0]['id'] > 0) {
      $query = "UPDATE matches SET  player_2 = 0, player_2_double = 0, score_player_1 = '', score_player_2 = '' WHERE id_ref = " . $res_ref_match_2[0]['id'] . " AND type_ref = 'bracket'";
      $this->db_conn->update($query);
      }

      $query = "UPDATE matches SET score_player_1 = '', score_player_2 = '', end_status = '' WHERE id = " . $match_detail['id'];
      $this->db_conn->update($query);

      $bracket = $tour->getDetailBracket($match_detail['id_ref']);

      //*** RIFACCIO IL CONTO DELLE PARTITE VINTE E PERSE DI ENTRAMBI I GIOCATORI
      $ut->updateWinLoseDraw($player_1);
      $ut->updateWinLoseDraw($player_2);
      if ($tour->isDoubleTournament($tour_detail['id'])) {
      $ut->updateWinLoseDraw($player_1_double);
      $ut->updateWinLoseDraw($player_2_double);
      }

      //*** ANNULLO IL PUNTEGGIO DEI PUNTI
      if ($tour->isSingleTournament($tour_detail['id'])) {
      $vrg->removePointTournament($player_1, $match_detail['id_ref'], "tournament");
      $vrg->removePointTournament($player_2, $match_detail['id_ref'], "tournament");
      } elseif ($tour->isDoubleTournament($tour_detail['id'])) {
      $vrg->removeDoublePointTournament($player_1, $player_1_double, $match_detail['id_ref'], $tour_detail['tournament_type']);
      $vrg->removeDoublePointTournament($player_2, $player_2_double, $match_detail['id_ref'], $tour_detail['tournament_type']);
      }

      //*** RISISTEMO IL VRG -- DA TESTARE ANCHE con FINALE.
      if ($tour->isSingleTournament($tour_detail['id']) && $match_detail['end_status'] != "N2" && $match_detail['end_status'] != "N1") {
      $vrg->removeUserVRG($player_1, $match_detail['id_ref'], "tournament");
      $vrg->removeUserVRG($player_2, $match_detail['id_ref'], "tournament");
      }

      //**** RIFACCIO IL CALCOLO DELLA CLASSIFICA
      if ($tour->isSingleTournament($tour_detail['id'])) {
      $vrg->updateChart($player_1, 0, "tournament");
      $vrg->updateChart($player_2, 0, "tournament");
      } elseif ($tour->isDoubleTournament($tour_detail['id'])) {
      $vrg->updateDoubleChart($player_1, $player_1_double, $tour_detail['tournament_type']);
      $vrg->updateDoubleChart($player_2, $player_2_double, $tour_detail['tournament_type']);
      }

      //**** RICALCOLO FINALE e TITOLO PER GIOCATORE
      if ($bracket['round'] == "finale") {
      $usr = new iWDUser();
      if ($tour->isSingleTournament($tour_detail['id'])) {
      $p1_tit = $usr->getPalmares($player_1, "titolo");
      $p1_fin = $usr->getPalmares($player_1, "finale");
      $query_p1 = "UPDATE users SET titles = ".count($p1_tit).", finals = " . count($p1_fin) . " WHERE id = " . $player_1;
      $res_update_p1 = $this->db_conn->update($query_p1);

      $p2_tit = $usr->getPalmares($player_2, "titolo");
      $p2_fin = $usr->getPalmares($player_2, "finale");
      $query_p2 = "UPDATE users SET titles = ".count($p2_tit).", finals = " . count($p2_fin) . " WHERE id = " . $player_2;
      $res_update_p2 = $this->db_conn->update($query_p2);
      } elseif ($tour->isDoubleTournament($tour_detail['id'])) {
      $p1d_tit = $usr->getPalmares($player_1_double, "titolo");
      $p1d_fin = $usr->getPalmares($player_1_double, "finale");
      $query_p1d = "UPDATE users SET titles = ".count($p1d_tit).", finals = " . count($p1d_fin) . " WHERE id = " . $player_1_double;
      $res_update_p1d = $this->db_conn->update($query_p1d);

      $p2d_tit = $usr->getPalmares($player_2_double, "titolo");
      $p2d_fin = $usr->getPalmares($player_2_double, "finale");
      $query_p2d = "UPDATE users SET//*** ANNULLO IL PUNTEGGIO DEI P titles = ".count($p2d_tit).", finals = " . count($p2d_fin) . " WHERE id = " . $player_2_double;
      $res_update_p2d = $this->db_conn->update($query_p2d);
      }
      }

      }elseif($type_of == "round" && $tour->isRoundTournament($tour_detail['id'])){

      //ANNULLO IL MATCH
      $query = "UPDATE matches SET score_player_1 = '', score_player_2 = '', end_status = '' WHERE id = " . $match_detail['id'];
      $this->db_conn->update($query);

      //*** RIFACCIO IL CONTO DELLE PARTITE VINTE E PERSE DI ENTRAMBI I GIOCATORI
      $ut->updateWinLoseDraw($player_1);
      $ut->updateWinLoseDraw($player_2);
      if ($tour->isDoubleTournament($tour_detail['id'])) {
      $ut->updateWinLoseDraw($player_1_double);
      $ut->updateWinLoseDraw($player_2_double);
      }

      //AGGIORNO IL ROUND
      $rounds_match = $tour->getRoundMatchDetail($match_detail['id_ref']);
      $round_number = $rounds_match['round_number'];
      if ($tour->isTeamTournament($tournament_id)) {
      $tour->updateAllRoundStat($tournament_id, $round_number, $match_detail['team_1'], $match_detail['team_2'], 0, 0);
      } else {
      $tour->updateAllRoundStat($tournament_id, $round_number, 0, 0, $match_detail['player_1'], $match_detail['player_2']);
      }

      if ($tour->isSingleTournament($tour_detail['id']) && $match_detail['end_status'] != "N2" && $match_detail['end_status'] != "N1") {
      $vrg->removeUserVRG($player_1, $match_detail['id_ref'], "tournament_round");
      $vrg->removeUserVRG($player_2, $match_detail['id_ref'], "tournament_round");
      }
      }

      $not->sendNotificationRemoveScoreMatch($tournament_id,$match_id,$old_score_player_1,$old_score_player_2,$old_end_status);
      return 1;
      }

      public function removeSingleMatchRaftCup($tournament_id, $ref_id, $type_of) {
      $tournament_id = sanitize($tournament_id, INT);
      $ref_id = sanitize($ref_id, INT);
      $type_of = sanitize($type_of, PARANOID);



      $tour = new iWDTournament();
      $not = new iWDNotification();
      $ut = new iWDUtente();
      $vrg = new iWDVRG();
      $tour_detail = $tour->getDetail($tournament_id, FALSE);
      $matches = $this->getAssociatedMatch($ref_id, "round");

      $pt_home_old = $this->countMatchWin($matches[0]['team_1'], $ref_id, "round");
      $pt_away_old = $this->countMatchWin($matches[0]['team_2'], $ref_id, "round");
      foreach ($matches as $s_m){
      $match_detail = $this->getDetail($s_m['id']);
      if($match_detail['end_status'] != ""){
      $player_1 = $match_detail['player_1'];
      $player_1_double = $match_detail['player_1_double'];
      $player_2 = $match_detail['player_2'];
      $player_2_double = $match_detail['player_2_double'];

      $query = "UPDATE matches SET score_player_1 = '', score_player_2 = '', end_status = '' WHERE id = " . $match_detail['id'];
      $this->db_conn->update($query);

      $ut->updateWinLoseDraw($player_1);
      $ut->updateWinLoseDraw($player_2);
      if ($match_detail['match_type'] == "double") {
      $ut->updateWinLoseDraw($player_1_double);
      $ut->updateWinLoseDraw($player_2_double);
      }

      if ($match_detail['match_type'] == "single") {
      $vrg->removePointTournament($player_1, $match_detail['id'], "raftcup");
      $vrg->removePointTournament($player_2, $match_detail['id'], "raftcup");
      } elseif ($match_detail['match_type'] == "double") {
      $vrg->removeDoublePointTournament($player_1, $player_1_double, $match_detail['id'], $tour_detail['tournament_type']);
      $vrg->removeDoublePointTournament($player_2, $player_2_double, $match_detail['id'], $tour_detail['tournament_type']);
      }

      $rounds_match = $tour->getRoundMatchDetail($match_detail['id_ref']);
      $round_number = $rounds_match['round_number'];
      $tour->updateAllRoundStat($tournament_id, $round_number, $match_detail['team_1'], $match_detail['team_2'], 0, 0);

      if ($match_detail['match_type'] == "single" && $match_detail['end_status'] != "N2" && $match_detail['end_status'] != "N1") {
      $vrg->removeUserVRG($player_1, $match_detail['id_ref'], "tournament_round");
      $vrg->removeUserVRG($player_2, $match_detail['id_ref'], "tournament_round");
      }
      }
      }

      $not->sendNotificationRemoveSingleMatchRaftCup($tournament_id,$ref_id,$pt_home_old,$pt_away_old);
      return 1;
      }

      public function getAssociatedBracketMatch($id_ref, $type_of) {
      $id_ref = sanitize($id_ref, INT);
      $type_of = sanitize($type_of, PARANOID);
      $query = "SELECT * FROM matches WHERE id_ref = '" . $id_ref . "' AND type_ref = '" . $type_of . "'";
      $res = $this->db_conn->query($query);
      return $res[0];
      }





      public function updateDetailMatchPlayer($tournament_id, $match_id, $team_1, $team_2, $player_1, $player_2) {
      $tournament_id = sanitize($tournament_id, INT);
      $match_id = sanitize($match_id, INT);

      $before_detail_match = $this->getDetail($match_id);

      $team_1 = sanitize($team_1, INT);
      $team_2 = sanitize($team_2, INT);

      $player_1 = sanitize($player_1, INT);
      $player_2 = sanitize($player_2, INT);

      $detail_match = $this->getDetail($match_id);

      $old_team_1 = sanitize($detail_match['team_1'], INT);
      $old_team_2 = sanitize($detail_match['team_2'], INT);

      $old_player_1 = sanitize($detail_match['player_1'], INT);
      $old_player_2 = sanitize($detail_match['player_2'], INT);

      $tour = new iWDTournament();
      $not = new iWDNotification();

      $update = 0;
      if ($tour->isSingleTournament($tournament_id) || $tour->isDoubleTournament($tournament_id)) {

      $matches = $this->getMatchesByIdTournament($tournament_id, $detail_match['type_ref']);
      $array_id_match = array();
      foreach ($matches as $m) {
      array_push($array_id_match, $m['id']);
      }
      foreach ($matches as $m) {
      $cond = array();

      if ($m['player_2'] == $player_2 && $m['player_1'] == $old_player_1 && $old_player_1 != $player_1) {
      $player_1_double = $tour->getPlayerDoubleID($tournament_id, $player_1);
      array_push($cond, "player_1 = " . $player_1 . ", player_1_double = " . $player_1_double);
      }elseif($m['player_1'] == $player_1 && $old_player_1 != $player_1){
      $old_player_1_double = $tour->getPlayerDoubleID($tournament_id, $old_player_1);
      array_push($cond, "player_1 = " . $old_player_1 . ", player_1_double = " . $old_player_1_double);
      }elseif($m['player_1'] == $player_2 && $player_2 > 0){
      $old_player_2_double = $tour->getPlayerDoubleID($tournament_id, $old_player_2);
      array_push($cond, "player_1 = " . $old_player_2 . ", player_1_double = " . $old_player_2_double);
      }

      if ($m['player_1'] == $player_1 && $m['player_2'] == $old_player_2 && $old_player_2 != $player_2) {
      $player_2_double = $tour->getPlayerDoubleID($tournament_id, $player_2);
      array_push($cond, "player_2 = " . $player_2 . ", player_2_double = " . $player_2_double);
      }elseif ($m['player_2'] == $player_2 && $old_player_2 != $player_2) {
      $old_player_2_double = $tour->getPlayerDoubleID($tournament_id, $old_player_2);
      array_push($cond, "player_2 = " . $old_player_2 . ", player_2_double = " . $old_player_2_double);
      }elseif ($m['player_2'] == $player_1 && $player_1 > 0){
      $old_player_1_double = $tour->getPlayerDoubleID($tournament_id, $old_player_1);
      array_push($cond, "player_2 = " . $old_player_1 . ", player_2_double = " . $old_player_1_double);
      }

      if($m['player_1'] == $old_player_1 && $m['player_2'] == 0){
      if($player_1 > 0){
      $player_1_double = $tour->getPlayerDoubleID($tournament_id, $player_1);
      }else{
      $player_1_double = 0;
      }
      array_push($cond, "player_1 = " . $player_1 . ", player_1_double = " . $player_1_double);
      }

      if($m['player_2'] == $old_player_2 && $m['player_1'] == 0){
      if($player_2 > 0){
      $player_2_double = $tour->getPlayerDoubleID($tournament_id, $player_2);
      }else{
      $player_2_double = 0;
      }
      array_push($cond, "player_2 = " . $player_2 . ", player_2_double = " . $player_2_double);
      }

      if($m['player_1'] == $old_player_1 && $m['player_2'] == -1){
      $player_1_double = $tour->getPlayerDoubleID($tournament_id, $player_1);
      array_push($cond, "player_1 = " . $player_1 . ", player_1_double = " . $player_1_double);
      }

      if($m['player_1'] == $player_1 && $m['player_2'] == -1){
      $player_1_double = $tour->getPlayerDoubleID($tournament_id, $old_player_1);
      array_push($cond, "player_1 = " . $old_player_1 . ", player_1_double = " . $player_1_double);
      }

      if($m['player_2'] == $old_player_2 && $m['player_1'] == -1){
      $player_2_double = $tour->getPlayerDoubleID($tournament_id, $player_2);
      array_push($cond, "player_2 = " . $player_2 . ", player_2_double = " . $player_2_double);
      }

      if($m['player_2'] == $player_2 && $m['player_1'] == -1){
      $player_2_double = $tour->getPlayerDoubleID($tournament_id, $old_player_2);
      array_push($cond, "player_2 = " . $old_player_2 . ", player_2_double = " . $player_2_double);
      }

      if (count($cond) > 0) {
      $query = "UPDATE matches SET " . implode(",", $cond) . " WHERE id = " . $m['id'];
      $res = $this->db_conn->update($query);

      if($res > 0){
      $update++;
      $query_update_BYE = "";
      if($old_player_1 == -1 && $player_1 > 0){
      $query_update_BYE = "UPDATE matches SET player_2 = 0, player_2_double = 0 WHERE player_2 = ".$player_2." AND id <> " . $m['id']. " AND id IN (" . implode(",", $array_id_match) . ")";
      }elseif($old_player_2 == -1 && $player_2 > 0){
      $query_update_BYE = "UPDATE matches SET player_1 = 0, player_1_double = 0 WHERE player_1 = ".$player_1." AND id <> " . $m['id']. " AND id IN (" . implode(",", $array_id_match) . ")";
      }elseif($player_1 == -1 && $player_2 > 0){
      $player_2_double = $tour->getPlayerDoubleID($tournament_id, $player_2);
      $query_update_BYE = "UPDATE matches SET player_2 = ".$player_2.", player_2_double = ".$player_2_double." WHERE player_2 = ".$old_player_2." AND id <> " . $m['id']. " AND id IN (" . implode(",", $array_id_match) . ")";
      }elseif($old_player_1 == -1 && $player_2 > 0){
      $player_2_double = $tour->getPlayerDoubleID($tournament_id, $player_2);
      $query_update_BYE = "UPDATE matches SET player_2 = ".$player_2.", player_2_double = ".$player_2_double." WHERE player_2 = ".$old_player_2." AND id <> " . $m['id']. " AND id IN (" . implode(",", $array_id_match) . ")";
      }elseif($player_2 == -1 && $player_1 > 0){
      $player_1_double = $tour->getPlayerDoubleID($tournament_id, $player_1);
      $query_update_BYE = "UPDATE matches SET player_1 = ".$player_1.", player_1_double = ".$player_1_double." WHERE player_1 = ".$old_player_1." AND id <> " . $m['id']. " AND id IN (" . implode(",", $array_id_match) . ")";
      }elseif($old_player_2 == -1 && $player_1 > 0){
      $player_1_double = $tour->getPlayerDoubleID($tournament_id, $player_1);
      $query_update_BYE = "UPDATE matches SET player_1 = ".$player_1.", player_1_double = ".$player_1_double." WHERE player_1 = ".$old_player_1." AND id <> " . $m['id']. " AND id IN (" . implode(",", $array_id_match) . ")";
      }

      if($query_update_BYE != ""){
      $res_update_BYE = $this->db_conn->update($query_update_BYE);
      }
      }
      }
      }

      } elseif ($tour->isTeamTournament($tournament_id)) {                    // --> TEAM

      $matches = $this->getMatchesByIdTournament($tournament_id, $detail_match['type_ref']);
      $array_id_match = array();
      foreach ($matches as $m) {
      array_push($array_id_match, $m['id']);
      }
      foreach ($matches as $m) {
      $cond = array();

      if ($m['team_2'] == $team_2 && $m['team_1'] == $old_team_1 && $old_team_1 != $team_1) {
      array_push($cond, "team_1 = " . $team_1);
      }elseif($m['team_1'] == $team_1 && $old_team_1 != $team_1){
      array_push($cond, "team_1 = " . $old_team_1);
      }elseif($m['team_1'] == $team_2 && $team_2 > 0){
      array_push($cond, "team_1 = " . $old_team_2);
      }

      if ($m['team_1'] == $team_1 && $m['team_2'] == $old_team_2 && $old_team_2 != $team_2) {
      array_push($cond, "team_2 = " . $team_2);
      }elseif($m['team_2'] == $team_2 && $old_team_2 != $team_2) {
      array_push($cond, "team_2 = " . $old_team_2);
      }elseif($m['team_2'] == $team_1 && $team_1 > 0){
      array_push($cond, "team_2 = " . $old_team_1);
      }

      if($m['team_1'] == $old_team_1 && $m['team_2'] == 0){
      array_push($cond, "team_1 = " . $team_1);
      }

      if($m['team_2'] == $old_team_2 && $m['team_1'] == 0){
      array_push($cond, "team_2 = " . $team_2);
      }

      if($m['team_1'] == $old_team_1 && $m['team_2'] == -1){
      array_push($cond, "team_1 = " . $team_1);
      }

      if($m['team_1'] == $team_1 && $m['team_2'] == -1){
      array_push($cond, "team_1 = " . $old_team_1);
      }

      if($m['team_2'] == $old_team_2 && $m['team_1'] == -1){
      array_push($cond, "team_2 = " . $team_2);
      }

      if($m['team_2'] == $team_2 && $m['team_1'] == -1){
      array_push($cond, "team_2 = " . $old_team_2);
      }

      if (count($cond) > 0) {
      $query = "UPDATE matches SET " . implode(",", $cond) . " WHERE id = " . $m['id'];
      $res = $this->db_conn->update($query);
      if($res > 0){
      $update++;
      $query_update_BYE = "";
      if($old_team_1 == -1 && $team_1 > 0){
      $query_update_BYE = "UPDATE matches SET team_2 = 0 WHERE team_2 = ".$team_2." AND id <> " . $m['id']. " AND id IN (" . implode(",", $array_id_match) . ")";
      }elseif($old_team_2 == -1 && $team_2 > 0){
      $query_update_BYE = "UPDATE matches SET team_1 = 0 WHERE team_1 = ".$team_1." AND id <> " . $m['id']. " AND id IN (" . implode(",", $array_id_match) . ")";
      }elseif($team_1 == -1 && $team_2 > 0){
      $query_update_BYE = "UPDATE matches SET team_2 = ".$team_2." WHERE team_2 = ".$old_team_2." AND id <> " . $m['id']. " AND id IN (" . implode(",", $array_id_match) . ")";
      }elseif($old_team_1 == -1 && $team_2 > 0){
      $query_update_BYE = "UPDATE matches SET team_2 = ".$team_2." WHERE team_2 = ".$old_team_2." AND id <> " . $m['id']. " AND id IN (" . implode(",", $array_id_match) . ")";
      }elseif($team_2 == -1 && $team_1 > 0){
      $query_update_BYE = "UPDATE matches SET team_1 = ".$team_1." WHERE team_1 = ".$old_team_1." AND id <> " . $m['id']. " AND id IN (" . implode(",", $array_id_match) . ")";
      }elseif($old_team_2 == -1 && $team_1 > 0){
      $query_update_BYE = "UPDATE matches SET team_1 = ".$team_1." WHERE team_1 = ".$old_team_1." AND id <> " . $m['id']. " AND id IN (" . implode(",", $array_id_match) . ")";
      }


      if($query_update_BYE != ""){
      $res_update_BYE = $this->db_conn->update($query_update_BYE);
      }
      }
      }
      }
      }

      if ($update > 0) {
      $after_detail_match = $this->getDetail($match_id);
      $not->sendNotificationChangeTournamentPlayer($tournament_id,$before_detail_match,$after_detail_match);
      }

      return $update;
      }

      public function getIDMatchByIdTournamentAndPlayer($tournament_id, $player_id) {
      $tournament_id = sanitize($tournament_id, INT);
      $player_id = sanitize($player_id, INT);
      $query = "SELECT matches.id AS match_id FROM tournament_brackets JOIN matches ON matches.id_ref = tournament_brackets.id WHERE tournament_brackets.id_tournament = '" . $tournament_id . "' AND (matches.player_1 = '" . $player_id . "' OR matches.player_2 = '" . $player_id . "' ) AND matches.type_ref = 'bracket' ";
      $res = $this->db_conn->query($query);
      return $res;
      }

      public function getIDMatchByIdTournamentAndDoublePlayer($tournament_id, $player_id_double) {
      $tournament_id = sanitize($tournament_id, INT);
      $player_id_double = sanitize($player_id_double, INT);
      $query = "SELECT matches.id AS match_id FROM tournament_brackets JOIN matches ON matches.id_ref = tournament_brackets.id WHERE tournament_brackets.id_tournament = '" . $tournament_id . "' AND (matches.player_1_double = '" . $player_id_double . "' OR matches.player_2_double = '" . $player_id_double . "') AND matches.type_ref = 'bracket' ";
      $res = $this->db_conn->query($query);
      return $res;
      }

      public function setScoreMatchTeamTournament($match_id, $match_score_1, $match_score_2, $set_by, $check_by) {
      $match_id = sanitize($match_id, INT);
      //$match_score_1 = sanitize($match_score_1, INT);
      //$match_score_2 = sanitize($match_score_2, INT);
      $set_by = sanitize($set_by, INT);
      $check_by = sanitize($check_by, INT);

      $query = "UPDATE matches SET
      match_set_by = '" . $set_by . "',
      match_check_by = '" . $check_by . "' WHERE id = " . $match_id;
      $res = $this->db_conn->update($query);

      $match = new iWDMatch();
      $detail_match = $match->getDetail($match_id);
      $match->updateScoreMatch($detail_match['id'], $detail_match['player_1'], $detail_match['player_2'], $detail_match['player_1_double'], $detail_match['player_2_double'], $match_score_1, $match_score_2, "ONLYSCORE", 0);

      if ($res > 0) {
      $not = new iWDNotification();
      $not->sendScoreMatchTeamTournamentMail($match_id, $set_by, $check_by);
      }
      return $res;
      }

      public function confirmMatchTeamTournamentScore($match_id, $match_set_by = 0, $match_check_by = 0) {
      $match_id = sanitize($match_id, INT);
      $tour = new iWDTournament();
      $end_status = "";
      $detail_match = $this->getDetail($match_id);
      $round_match = $tour->getRoundMatchDetail($detail_match['id_ref']);
      $id_tournament = $round_match['id_tournament'];
      $tour_detail = $tour->getDetail($id_tournament);

      $score_1 = explode(", ", $detail_match['score_player_1']);
      $score_2 = explode(", ", $detail_match['score_player_2']);

      // round_chart_point_type ( classico, coppa-italia-uisp )
      // 4-3
      // 0-4
      // 4-3
      $x = 0;
      $set_match_1 = 0;
      $set_match_2 = 0;
      $set_game_1 = 0;
      $set_game_2 = 0;
      foreach ($score_1 as $s) {
      $set_game_1 += $score_1[$x]; // sommo i game TEAM1
      $set_game_2 += $score_2[$x]; // sommo i game TEAM2
      if ($score_1[$x] > $score_2[$x]) {
      $set_game_1++; // sommo 1 punto se vince il set TEAM1 LO USO SOLO PER LA COPPA
      $set_match_1++;
      } elseif ($score_1[$x] < $score_2[$x]) {
      $set_game_2++; // sommo 1 punto se vince il set TEAM2 LO USO SOLO PER LA COPPA
      $set_match_2++;
      } elseif ($detail_match['score_player_1'] != '' && $detail_match['score_player_2'] != '' && $score_1[$x] == $score_2[$x]) {
      $set_match_1++;
      $set_match_2++;
      }
      $x++;
      }

      if ($tour_detail['round_chart_point_type'] == "coppa-italia-uisp") {
      if ($set_game_1 > $set_game_2) {
      $set_match_1 = 1;
      $set_match_2 = 0;
      } elseif ($set_game_2 > $set_game_1) {
      $set_match_1 = 0;
      $set_match_2 = 1;
      } elseif ($set_game_1 == $set_game_2) { // se game team-1 = game team-2 --> verifico i set vinti
      if ($set_match_1 > $set_match_2) {
      $set_match_1 = 1;
      $set_match_2 = 0;
      } elseif ($set_match_2 > $set_match_1) {
      $set_match_1 = 0;
      $set_match_2 = 1;
      }
      }
      }

      if ($set_match_1 > $set_match_2) {
      $end_status = "V1";
      } elseif ($set_match_1 < $set_match_2) {
      $end_status = "V2";
      } elseif ($detail_match['score_player_1'] != '' && $detail_match['score_player_2'] != '' && $set_match_1 == $set_match_2) {
      $end_status = "P0";
      }

      $res = $this->updateScoreMatch($detail_match['id'], $detail_match['player_1'], $detail_match['player_2'], $detail_match['player_1_double'], $detail_match['player_2_double'], $detail_match['score_player_1'], $detail_match['score_player_2'], $end_status, 0);

      if ($res > 0) {
      $not = new iWDNotification();
      if ($end_status != "") {
      $not->sendConfirmScoreMatchTeamTournamentMail($match_id);
      }
      }
      return $res;
      }

      public function countMatchWin($element_id, $id_ref, $type_ref) {
      $element_id = sanitize($element_id, INT);
      $id_ref = sanitize($id_ref, INT);
      $count_pt = "";
      $matches = $this->getAssociatedMatch($id_ref, $type_ref);
      foreach ($matches as $s_m) {
      if (($s_m['team_1'] == $element_id) && ($s_m['end_status'] == "V1" || $s_m['end_status'] == "R2" || $s_m['end_status'] == "N2" || $s_m['end_status'] == "P0")) {
      $count_pt++;
      } elseif (($s_m['team_2'] == $element_id) && ($s_m['end_status'] == "V2" || $s_m['end_status'] == "R1" || $s_m['end_status'] == "N1" || $s_m['end_status'] == "P0")) {
      $count_pt++;
      }
      }

      return $count_pt;
      }

      public function disputeScoreMatchTeamTournament($user_id, $match_id) {
      $user_id = sanitize($user_id, INT);
      $match_id = sanitize($match_id, INT);
      $query = "UPDATE matches SET match_dispute = '1' WHERE id = " . $match_id;
      $res = $this->db_conn->update($query);
      $not = new iWDNotification();
      $res = $not->sendDisputeScoreMatchTeamTournamentMail($user_id, $match_id);
      return $res;
      } */
}

?>