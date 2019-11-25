<? require_once 'header.php'; ?>
<!-- Pushy Panel - Dark -->
<?
$usr = new sagUser();
$card_detail = $usr->getDetail($_GET['id']);
?>
<!-- Player Heading
================================================== -->
<div class="player-heading" style="background-image: url(img/giocatori.jpg)">
    <div class="container">

        <div class="player-info__title player-info__title--mobile">
            <div class="player-info__number"><?= $card_detail['ranking'] ?></div>
            <h1 class="player-info__name">
                <span class="player-info__first-name"><?= $card_detail['name'] ?></span>
                <span class="player-info__last-name"><?= $card_detail['surname'] ?></span>
            </h1>
        </div>

        <div class="player-info">

            <!-- Player Details -->
            <div class="player-info__item player-info__item--details">

                <div class="player-info__title player-info__title--desktop">
                    <div class="player-info__number" style="margin-left: 0px"><?= $card_detail['ranking'] ?></div>
                    <h1 class="player-info__name">
                        <span class="player-info__first-name"><?= $card_detail['name'] ?></span>
                        <span class="player-info__last-name"><?= $card_detail['surname'] ?></span>
                    </h1>
                </div>

                <div class="player-info-details">
                    <div class="player-info-details__item player-info-details__item--height">
                        <h6 class="player-info-details__title">Partite</h6>
                        <div class="player-info-details__value"><?= ($card_detail['win'] + $card_detail['lose'])?></div>
                    </div>
                    <div class="player-info-details__item player-info-details__item--height">
                        <h6 class="player-info-details__title">Vinte</h6>
                        <div class="player-info-details__value"><?= $card_detail['win']?></div>
                    </div>
                    <div class="player-info-details__item player-info-details__item--height">
                        <h6 class="player-info-details__title">Perse</h6>
                        <div class="player-info-details__value"><?= $card_detail['lose']?></div>
                    </div>
                    <div class="player-info-details__item player-info-details__item--height">
                        <h6 class="player-info-details__title">Nazionalità</h6>
                        <div class="player-info-details__value">IT</div>
                    </div>                    
                </div>
            </div>
            <!-- Player Details / End -->

            <!-- Player Stats / End -->
        </div>
    </div>
</div>

<script>
    function inviaSfida() {
        $.post("lib/manage-challenge.php", {
            op_type: 'invia-sfida',
            player_1: <?= $_SESSION['user_id'] ?>,
            player_2: <?= $_GET['id'] ?>,
        }, function (data) {
            alert("SFIDA LANCIATA!");
        });
    }
</script>

<!-- Player Pages Filter -->
<? if (isset($_SESSION['logged']) && $_SESSION['logged'] == 1) { ?>
    <nav class="content-filter">
        <div class="container">
            <a href="#" class="content-filter__toggle"></a>
            <ul class="content-filter__list">
                <li class="content-filter__item"><a href="scheda-giocatore.php?id=<?= $_GET['id'] ?>" class="content-filter__link">Riepilogo<br/>Partite</a></li>
                <? if ($_SESSION['user_id'] != $_GET['id']) { ?>
                    <li class="content-filter__item content-filter__item--active"><a href="scheda-giocatore-sfida.php?id=<?= $_GET['id'] ?>" class="content-filter__link">Sfida<br/>questo giocatore</a></li>
                <? } ?>
                <? if ($_SESSION['user_id'] == $_GET['id']) { ?>
                    <li class="content-filter__item"><a href="scheda-giocatore-modifica.php?id=<?= $_GET['id'] ?>" class="content-filter__link">Modifca<br/>i miei dati</a></li>
                    <li class="content-filter__item content-filter__item--active"><a href="scheda-giocatore-partite.php?id=<?= $_GET['id'] ?>" class="content-filter__link">Le mie<br/>partite</a></li>
                <? } ?>
            </ul>
        </div>
    </nav>
<? } ?>
<!-- Player Pages Filter / End -->
<?
require_once './lib/class_user.php';
require_once './lib/class_challenge.php';
$cha = new sagChallenge();
$usr = new sagUser();
$list = $cha->getActiveChallenges($_SESSION['user_id']);
$month_challenge = $cha->getCountMonthChallenges($_SESSION['user_id']);
?>
<script>
    function removeChallenge(challenge_id) {
        $("#box_challenge_button").hide();
        $.post("lib/manage-challenge.php", {
            op_type: "remove-challenge",
            challenge_id: challenge_id
        }, function (data) {
            if (data >= 0) {
                location.reload();
            }
        });
    }

    function acceptChallenge(challenge_id) {
        $("#box_challenge_button").hide();
        $.post("lib/manage-challenge.php", {
            op_type: "accept-challenge",
            challenge_id: challenge_id
        }, function (data) {
            if (data >= 0) {
                location.reload();
            }
        });
    }


    function declineChallenge(challenge_id) {
        $("#box_challenge_button").hide();
        $.post("lib/manage-challenge.php", {
            op_type: "decline-challenge",
            challenge_id: challenge_id
        }, function (data) {
            if (data >= 0) {
                location.reload();
            }
        });
    }

    function askRemoveChallenge(user_id, challenge_id) {
        $.post("lib/manage-challenge.php", {
            op_type: 'ask-remove-challenge',
            user_id: user_id,
            challenge_id: challenge_id
        }, function (data) {
            if (data >= 0) {
                location.reload();
            }
        });
    }

    function declineRemoveChallenge(challenge_id) {
        $.post("lib/manage-challenge.php", {
            op_type: 'decline-remove-challenge',
            challenge_id: challenge_id
        }, function (data) {
            if (data >= 0) {
                location.reload();
            }
        });
    }

    function acceptRemoveChallenge(challenge_id) {
        $.post("lib/manage-challenge.php", {
            op_type: 'accept-remove-challenge',
            challenge_id: challenge_id
        }, function (data) {
            if (data >= 0) {
                location.reload();
            }
        });
    }

    function confirmScoreChallenge(challenge_id, check_by, set_by) {
        $("#conferma_sfide").hide();
        $.post("lib/manage-challenge.php", {
            op_type: "confirm-challenge-score",
            challenge_id: challenge_id,
            check_by: check_by,
            set_by: set_by
        }, function (data) {
            if (data >= 0) {
                $("#final_score_match").html("Punteggio confermato.");
            }
        });
    }
</script>
<!-- Content 
================================================== -->
<div class="site-content">
    <div class="container">
        <div class="col-md-12">
            <aside class="widget widget--sidebar card widget-preview">
                <div class="widget__title card__header">
                    <h4>Sfide in sospeso</h4>
                </div>
            </aside>
        </div>
        <? foreach ($list as $s) { ?>
            <?
            $player1 = $usr->getDetail($s['player_1']);
            $player2 = $usr->getDetail($s['player_2']);
            ?>
            <div class="col-md-12">
                <div class=" card">
                    <div class="widget__content card__content">
                        <!-- Match Preview -->
                        <div class="match-preview">
                            <section class="match-preview__body">
                                <header class="match-preview__header">
                                    <h3 class="match-preview__title match-preview__title--lg"><?= date("d-m-Y", strtotime($s['date_match'])) ?></h3>
                                </header>
                                <div class="match-preview__content">

                                    <!-- 1st Team -->
                                    <div class="match-preview__team match-preview__team--first">                               
                                        <h5 class="match-preview__team-name"><?= $player1['name'] ?></h5>
                                        <div class="match-preview__team-info"><?= $player1['surname'] ?></div>
                                    </div>
                                    <!-- 1st Team / End -->

                                    <div class="match-preview__vs" style="padding-top: 0px;">
                                        <div class="match-preview__conj"><?= $s['score_player_1'] ?> VS <?= $s['score_player_2'] ?></div>
                                        <div class="match-preview__match-info">
                                            <div class="match-preview__match-place">
                                                <?
                                                if ($s['id_circolo'] > 0) {
                                                    $circ_detail = $cha->getCircolo($s['id_circolo']);
                                                    echo $circ_detail['club_name'];
                                                }
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- 2nd Team -->
                                    <div class="match-preview__team match-preview__team--second">
                                        <h5 class="match-preview__team-name"><?= $player2['name'] ?></h5>
                                        <div class="match-preview__team-info"><?= $player2['surname'] ?></div>
                                    </div>
                                    <!-- 2nd Team / End -->
                                </div>
                            </section>
                            <div class="match-preview__action match-preview__action--ticket">
                                <? if ($s['status_challenge'] == 0) { ?>
                                    <? if ($s['player_1'] == $_SESSION['user_id']) { ?>
                                        <div onclick="removeChallenge('<?= $s['id'] ?>')" class="btn btn-primary-inverse btn-lg btn-block" style="background-color: #656565">Ritira la sfida</div>
                                    <? } else { ?>
                                        <? if ($month_challenge < 4) { ?>
                                            <div onclick="acceptChallenge('<?= $s['id'] ?>')" class="btn btn-primary-inverse btn-lg btn-block">Accetta la sfida</div><div onclick="declineChallenge('<?= $s['id'] ?>')" class="btn btn-primary-inverse btn-lg btn-block" style="background-color: #656565;">Rifiuta la sfida</div>
                                        <? } else { ?>
                                            <div class="btn btn-primary-inverse btn-lg btn-block" style="background-color: #CC0000">Hai già 4 match confermati</div>
                                        <? } ?>
                                    <? } ?>

                                <? } else { ?>
                                        <? if ($s['score_player_1'] != 0 || $s['score_player_2'] != 0) { ?>
                                            <? if ($s['check_by'] == $_SESSION['user_id']) { ?>
                                            <div id="final_score_match">
                                                <? if ($s['dispute_challenge'] == 1) { ?>
                                                    <div class="btn btn-primary-inverse btn-lg btn-block" style="background-color: #CC0000">Risultato contestato</div>
                <? } else { ?>
                                                    <div id="final_score_match_status"></div>
                                                    <div id="conferma_sfide" onclick="confirmScoreChallenge('<?= $s['id'] ?>', '<?= $s['check_by'] ?>', '<?= $s['set_by'] ?>')" class="btn btn-primary-inverse btn-lg btn-block">Conferma il punteggio</div>
                                                    <div id="dispute_sfide" onclick="clickDisputeScoreChallenge()" class="btn btn-primary-inverse btn-lg btn-block" style="background-color: #CC0000">Contesta il punteggio</div>
                                            <? } ?>
                                            </div>
                                        <? } else { ?>
                                            <? if ($s['dispute_challenge'] == 1) { ?>
                                                <div class="game-result__score-label btn btn-default btn-xs" style="background-color: #CC0000">Risultato contestato</div>
                                            <? } else { ?>
                                                <div class="btn btn-primary-inverse btn-lg btn-block">In attesa di conferma</div>
                                            <? } ?>
                                        <? } ?>
        <? } elseif ($s['status_challenge'] == 1 && $s['ask_remove_challenge'] == 1) { ?>
                                        <? if ($s['check_by'] == $_SESSION['user_id']) { ?>
                                            <h3 class="game-result__title">Accetti di voler rimuovere il match?</h3>
                                            <div onclick="declineRemoveChallenge('<?= $s['id'] ?>')" class="btn btn-primary-inverse btn-lg btn-block"  style="background-color: #656565">Annulla</div> <div onclick="acceptRemoveChallenge('<?= $s['id'] ?>')" class="btn btn-primary-inverse btn-lg btn-block">Accetta</div>
                                        <? } else { ?>
                                            <div class="btn btn-primary-inverse btn-lg btn-block">Inviata richiesta di rimozione</div>
            <? } ?>
                                    <? } else { ?>
                                        <a href="scheda-giocatore-sfida-dettaglio.php?id=<?= $_GET['id'] ?>&sfida_id=<?= $s['id'] ?>" class="btn btn-primary-inverse btn-lg btn-block">Dettaglio</a>
                                        <a onclick="askRemoveChallenge('<?= $_SESSION['user_id'] ?>', '<?= $s['id'] ?>')" class="btn btn-primary-inverse btn-lg btn-block" style="background-color: #656565">Ritira la sfida</a>                                        
        <? } ?>
    <? } ?>
                            </div>
                        </div>
                        <!-- Match Preview / End -->
                    </div>
                </div>
            </div>
<? } ?>

        <!-- Last Game Log / End -->        
    </div>
</div>

<!-- Content / End -->


<? require_once 'footer.php'; ?>