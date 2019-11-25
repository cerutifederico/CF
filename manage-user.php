<?php
@session_start();
require_once 'class_user.php';
$op_type = $_POST['op_type'];
if ($op_type == "login") {
    $u_mail = $_POST['u_mail'];
    $u_password = $_POST['u_password'];
    $usr = new sagUser();
    $res = $usr->login($u_mail, $u_password);
    echo $res;
} elseif ($op_type == "create-quick") {
    $nome = $_POST['nome'];
    $cognome = $_POST['cognome'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $usr = new sagUser();
    $res = $usr->createQuick($nome, $cognome, $email, $password);
    echo $res;
} elseif ($op_type == "update") {
    $user_id = $_POST['user_id'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $gender = $_POST['gender'];
    $birthday = $_POST['birthday'];
    $nationality = $_POST['nationality'];
    $nation = $_POST['nation'];
    $place_birthday = $_POST['place_birthday'];
    $province_birthday = $_POST['province_birthday'];
    $city = $_POST['city'];
    $codfiscale = $_POST['codfiscale'];
    $telephone = $_POST['telephone'];
    $is_blank = $_POST['is_blank'];
    $is_fit = $_POST['is_fit'];
    $is_uisp = $_POST['is_uisp'];
    $is_asc = $_POST['is_asc'];
    $is_other = $_POST['is_other'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $zip = $_POST['zip'];
    $province = $_POST['province'];
    $usr = new sagUser();
    $res = $usr->update($user_id, $first_name, $last_name, $gender, $nationality, $nation, $place_birthday, $province_birthday, $birthday, $city, $codfiscale, $telephone, $is_blank, $is_fit, $is_uisp, $is_asc, $is_other, $email, $address, $zip, $province);
    echo $res;
}
?>