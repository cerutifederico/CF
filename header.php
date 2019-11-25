<?
@session_start();
$file = $_SERVER["PHP_SELF"];
$page = basename($file);
$page = preg_replace("/(\.php).?/", "", $page);

require_once 'lib/class_user.php';

$h_user = new sagUser();
if (isset($_SESSION['logged']) && $_SESSION['logged'] == 1) {
    $h_user_detail = $h_user->getDetail($_SESSION['user_id']);
}
?>
<!DOCTYPE html>
<html lang="it">
    <head>

        <!-- Basic Page Needs
        ================================================== -->
        <title>Set A Game</title>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="description" content=" ">
        <meta name="author" content=" ">
        <meta name="keywords" content=" ">

        <!-- Favicons
        ================================================== -->

        <!-- Mobile Specific Metas
        ================================================== -->
        <meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0">

        <!-- Google Web Fonts
        ================================================== -->
        <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700%7CSource+Sans+Pro:400,700" rel="stylesheet">

        <!-- CSS
        ================================================== -->
        <!-- Vendor CSS -->
        <link href="assets/vendor/bootstrap/css/bootstrap.css" rel="stylesheet">
        <link href="assets/fonts/font-awesome/css/all.min.css" rel="stylesheet">
        <link href="assets/fonts/font-awesome/css/v4-shims.min.css" rel="stylesheet">
        <link href="assets/fonts/simple-line-icons/css/simple-line-icons.css" rel="stylesheet">
        <link href="assets/vendor/magnific-popup/dist/magnific-popup.css" rel="stylesheet">
        <link href="assets/vendor/slick/slick.css" rel="stylesheet">

        <!-- Template CSS-->
        <link href="assets/css/style-soccer.css" rel="stylesheet">

        <!-- Custom CSS-->
        <link href="assets/css/custom.css" rel="stylesheet">
<script src="assets/vendor/jquery/jquery.min.js"></script>
    </head>
    <body data-template="template-soccer" class="page-loader-disable">

        <div class="site-wrapper clearfix">
            <div class="site-overlay"></div>

            <!-- Header
            ================================================== -->

            <!-- Header Mobile -->
            <div class="header-mobile clearfix" id="header-mobile">
                <div class="header-mobile__logo">
                    <a href="index.php"><img src="img/logo-tennis.png" alt="set a game" class="header-mobile__logo-img"></a>
                </div>
                <div class="header-mobile__inner">
                    <a id="header-mobile__toggle" class="burger-menu-icon"><span class="burger-menu-icon__line"></span></a>
                    <span class="header-mobile__search-icon" id="header-mobile__search-icon"></span>
                </div>
            </div>

            <!-- Header Desktop -->
            <header class="header header--layout-1">

                <!-- Header Top Bar -->
                <div class="header__top-bar clearfix">
                    <div class="container">
                        <div class="header__top-bar-inner">
                            <!-- Account Navigation -->
                            <? if (isset($_SESSION['logged']) && $_SESSION['logged'] == 1) { ?>
                                <ul class="nav-account">
                                    <li class="nav-account__item"><a href="#">Bentornato <?= $h_user_detail['name'] ?></a></li>
                                    <li class="nav-account__item"><a href="scheda-giocatore.php?id=<?= $_SESSION['user_id'] ?>">Il mio profilo</a></li>
                                    <li class="nav-account__item nav-account__item--logout"><a href="lib/logout.php">Logout</a></li>
                                </ul>
                            <? } else { ?>
                                <ul class="nav-account">
                                    <li class="nav-account__item"><a href="#">&nbsp;</a></li>                                    
                                </ul>
                            <? } ?>
                            <!-- Account Navigation / End -->
                        </div>
                    </div>
                </div>
                <!-- Header Top Bar / End -->

                <!-- Header Secondary -->
                <div class="header__secondary">
                    <div class="container">
                        <!-- Header Search Form / End -->
                        <ul class="info-block info-block--header">
                            <? if (isset($_SESSION['logged']) && $_SESSION['logged'] == 1) { ?>
                                <li class="info-block__item info-block__item--contact-primary">
                                    <a href="scheda-giocatore.php?id=<?=$_SESSION['user_id']?>"><svg role="img" class="df-icon df-icon--whistle">
                                        <use xlink:href="assets/images/icons-soccer.svg#whistle"/>
                                        </svg>
                                        <h6 class="info-block__heading">Bentornato</h6>
                                        <span class="info-block__link">Il mio profilo</span>
                                    </a>                                
                                </li>
                                <li class="info-block__item info-block__item--contact-primary">
                                    <a href="scheda-giocatore-partite.php?id=<?=$_SESSION['user_id']?>"><svg role="img" class="df-icon df-icon--whistle">
                                        <use xlink:href="assets/images/icons-soccer.svg#whistle"/>
                                        </svg>
                                        <h6 class="info-block__heading">Elenco</h6>
                                        <span class="info-block__link">Partite</span>
                                    </a>                                
                                </li>
                            <? } else { ?>
                                <li class="info-block__item info-block__item--contact-primary">
                                    <a href="#" data-toggle="modal" data-target="#modal-login-tabs"><svg role="img" class="df-icon df-icon--whistle">
                                        <use xlink:href="assets/images/icons-soccer.svg#whistle"/>
                                        </svg>
                                        <h6 class="info-block__heading">Accedi</h6>
                                        <span class="info-block__link">al tuo profilo</span>
                                    </a>                                
                                </li>
                                <li class="info-block__item info-block__item--contact-primary">
                                <a href="#" data-toggle="modal" data-target="#modal-register-tabs"><svg role="img" class="df-icon df-icon--whistle">
                                    <use xlink:href="assets/images/icons-soccer.svg#whistle"/>
                                    </svg>
                                    <h6 class="info-block__heading">Iscriviti</h6>
                                    <span class="info-block__link">Inizia a giocare</span>
                                </a>                                
                            </li>
                            <li class="info-block__item info-block__item--contact-primary">
                                <svg role="img" class="df-icon df-icon--whistle">
                                <use xlink:href="assets/images/icons-soccer.svg#whistle"/>
                                </svg>
                                <h6 class="info-block__heading">Sei un circolo?</h6>
                                <a class="info-block__link" href="">Iscriviti e organizza tornei</a>
                            </li>
                            <? } ?>
                            
                        </ul>
                    </div>
                </div>
                <!-- Header Secondary / End -->

                <!-- Header Primary -->
                <div class="header__primary">
                    <div class="container">
                        <div class="header__primary-inner">
                            <!-- Header Logo -->
                            <div class="header-logo">
                                <a href="index.php"><img src="img/logo-tennis.png" alt="set a game" style="width: 148px" class="header-logo__img"></a>
                            </div>
                            <!-- Header Logo / End -->
                            <!-- Main Navigation -->
                            <nav class="main-nav clearfix">
                                <ul class="main-nav__list">
                                    <li <? if ($page == "index") { ?>class="active"<? } ?>><a href="index.php">Home</a></li>                                    
                                    <li <? if ($page == "circoli") { ?>class="active"<? } ?>><a href="circoli.php">Circoli</a></li>                                    
                                    <li <? if ($page == "giocatori") { ?>class="active"<? } ?>><a href="giocatori.php">Giocatori</a></li>                                    
                                    <li <? if ($page == "classifica") { ?>class="active"<? } ?>><a href="classifica.php">Classifica</a></li>                                    
                                    <li <? if ($page == "partite") { ?>class="active"<? } ?>><a href="partite.php">Partite</a></li>                                    
                                </ul>
                                <!-- Pushy Panel Toggle -->
                                <!-- Pushy Panel Toggle / Eng -->
                            </nav>
                            <!-- Main Navigation / End -->
                        </div>
                    </div>
                </div>
                <!-- Header Primary / End -->
            </header>
            <!-- Header / End -->