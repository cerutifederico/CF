<?php
if (!$_SESSION) {
    session_start();
    session_cache_expire(30);
}

$_SESSION['logged'] = 0;
$_SESSION['user_id'] = 0;
unset($_SESSION['logged']);
unset($_SESSION['user_id']);
@session_start();
session_destroy();
session_start();
header("Location: ../index.php");
?>

