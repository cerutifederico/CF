<?php

@session_start();
require_once 'class_challenge.php';
$op_type = $_POST['op_type'];
$cha = new sagChallenge();
if ($op_type == "invia-sfida") {
    $player_1 = $_POST['player_1'];
    $player_2 = $_POST['player_2'];
    $res = $cha->sendChallenge($player_1, $player_2);
    echo $res;
} elseif ($op_type == "remove-challenge") {
    $challenge_id = $_POST['challenge_id'];
    $res = $cha->removeChallenge($challenge_id);
    echo $res;
} elseif ($op_type == "accept-challenge") {
    $challenge_id = $_POST['challenge_id'];
    $res = $cha->updateStatusChallenge($challenge_id, 1);
    echo $res;
} elseif ($op_type == "decline-challenge") {
    $challenge_id = $_POST['challenge_id'];
    $res = $cha->declineChallenge($challenge_id);
    echo $res;
} elseif ($op_type == "ask-remove-challenge") {
    $user_id = $_POST['user_id'];
    $challenge_id = $_POST['challenge_id'];
    $res = $cha->askRemoveChallenge($user_id, $challenge_id);
    echo $res;
} elseif ($op_type == "decline-remove-challenge") {
    $challenge_id = $_POST['challenge_id'];
    $res = $cha->declineRemoveChallenge($challenge_id);
    echo $res;
} elseif ($op_type == "accept-remove-challenge") {
    $challenge_id = $_POST['challenge_id'];
    $res = $cha->acceptRemoveChallenge($challenge_id);
    echo $res;
} elseif ($op_type == "update-challenge") {
    $challenge_id = $_POST['challenge_id'];
    $date_dd = $_POST['challenge_dd'];
    $date_mm = $_POST['challenge_mm'];
    $date_yyyy = $_POST['challenge_yyyy'];
    $date_challenge = $date_dd . "/" . $date_mm . "/" . $date_yyyy;
    $time_hh = $_POST['challenge_hours'];
    $time_mm = $_POST['challenge_minutes'];
    $time_ss = "00";
    $time_challenge = $time_hh . ":" . $time_mm . ":" . $time_ss;
    $challenge_circolo = $_POST['challenge_circolo'];
    $challenge_address = $_POST['challenge_address'];
    $challenge_city = $_POST['challenge_city'];
    $challenge_province = $_POST['challenge_province'];
    $challenge_zip = $_POST['challenge_zip'];
    $res = $cha->updateChallenge($challenge_id, $date_challenge, $time_challenge, $challenge_circolo, $challenge_address, $challenge_city, $challenge_province, $challenge_zip);

    //*** PER PUNTI
    $score_player_1 = $_POST['challenge_score_player_1'];
    $score_player_2 = $_POST['challenge_score_player_2'];
    $set_by = $_POST['challenge_set_by'];
    $check_by = $_POST['challenge_check_by'];

    $res = $cha->setScoreChallenge($challenge_id, $score_player_1, $score_player_2, $set_by, $check_by);
    echo $res;
}elseif ($op_type == "confirm-challenge-score") {
    $challenge_id = $_POST['challenge_id'];
    $check_by = $_POST['check_by'];
    $set_by = $_POST['set_by'];    
    $res = $cha->updateStatusChallenge($challenge_id, 2,$set_by,$check_by);
    echo $res;
}
?>