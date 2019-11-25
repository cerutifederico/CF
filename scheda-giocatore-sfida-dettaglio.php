<? require_once 'header.php'; ?>
<!-- Pushy Panel - Dark -->
<?
require_once 'lib/class_challenge.php';
$cha = new sagChallenge();
$lista_circoli = $cha->getCircoli();
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
                        <div class="player-info-details__value"><?= ($card_detail['win'] + $card_detail['lose']) ?></div>
                    </div>
                    <div class="player-info-details__item player-info-details__item--height">
                        <h6 class="player-info-details__title">Vinte</h6>
                        <div class="player-info-details__value"><?= $card_detail['win'] ?></div>
                    </div>
                    <div class="player-info-details__item player-info-details__item--height">
                        <h6 class="player-info-details__title">Perse</h6>
                        <div class="player-info-details__value"><?= $card_detail['lose'] ?></div>
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
                    <li class="content-filter__item"><a href="scheda-giocatore-partite.php?id=<?= $_GET['id'] ?>" class="content-filter__link">Le mie<br/>partite</a></li>
                <? } ?>
            </ul>
        </div>
    </nav>
<? } ?>
<!-- Player Pages Filter / End -->
<?
require_once 'lib/class_challenge.php';
$cha = new sagChallenge();
$usr_detail = $usr->getDetail($_SESSION['user_id']);
$detail = $cha->getDetail($_GET['sfida_id']);


$player1 = $usr->getDetail($detail['player_1']);
$player2 = $usr->getDetail($detail['player_2']);

if ($detail['player_1'] == $_SESSION['user_id']) {
    $set_by = $detail['player_1'];
    $check_by = $detail['player_2'];
} else {
    $set_by = $detail['player_2'];
    $check_by = $detail['player_1'];
}
?>
<script>
    function updateCircoloInfo() {
        var id_circolo = $("#circolo").val();
        if (id_circolo > 0) {
            /*$.post("lib/manage-circolo.php", {
             op_type: 'dettaglio_circolo',
             circolo_id: id_circolo
             }, function (data) {
             var circolo = JSON.parse(data);
             $("#challenge-address").val(circolo.address);
             $("#challenge-city").val(circolo.city);
             $("#challenge-zip").val(circolo.zip);
             $("#challenge-province").val(circolo.id_province);
             });*/
        } else {
            $("#challenge-address").val("");
            $("#challenge-city").val("");
            $("#challenge-zip").val("");
            $("#challenge-province").val("");
        }
    }


    function updateChallenge(challenge_id, set_by, check_by) {
        var circolo = $("#circolo").val();
        var challenge_dd = $("#challenge-dd").val();
        var challenge_mm = $("#challenge-mm").val();
        var challenge_yyyy = $("#challenge-yyyy").val();
        var challenge_hours = $("#challenge-hours").val();
        var challenge_minutes = $("#challenge-minutes").val();
        var challenge_address = $("#challenge-address").val();
        var challenge_city = $("#challenge-city").val();
        var challenge_province = $("#challenge-province").val();
        var challenge_zip = $("#challenge-zip").val();

        var score_1 = $("#score_1").val();
        var score_2 = $("#score_2").val();

        $("#match_send_score_button").hide();
        $.post("lib/manage-challenge.php", {
            op_type: 'update-challenge',
            challenge_id: challenge_id,
            challenge_dd: challenge_dd,
            challenge_mm: challenge_mm,
            challenge_yyyy: challenge_yyyy,
            challenge_hours: challenge_hours,
            challenge_minutes: challenge_minutes,
            challenge_circolo: circolo,
            challenge_address: challenge_address,
            challenge_city: challenge_city,
            challenge_province: challenge_province,
            challenge_zip: challenge_zip,
            challenge_score_player_1: score_1,
            challenge_score_player_2: score_2,
            challenge_set_by: set_by,
            challenge_check_by: check_by
        }, function (data) {
            if (data >= 0) {
                location.href = "scheda-giocatore-partite.php?id=<?= $_GET['id'] ?>";
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
                    <h4>Dettaglio sfida</h4>
                </div>
                <div class="card__content">
                    <style>
                        .score{width: 40px; text-align: center; padding-top: 5px; padding-bottom: 5px}
                    </style>
                    <div class="game-result">
                        <section class="game-result__section">
                            <div class="game-result__content">
                                <div class="game-result__team game-result__team--first">
                                    <div class="game-result__team-info">
                                        <h5 class="game-result__team-name"><?= $player1['name'] ?></h5>
                                        <div class="game-result__team-desc"><?= $player1['surname'] ?></div>
                                    </div>
                                </div>
                                <div class="game-result__score-wrap">
                                    <div class="game-result__score">
                                        <span class="game-result__score-result game-result__score-result--winner">
                                            <select class="form-control" id='score_1' style="display: inline; width: 70px;">
                                                <? for ($x = 0; $x <= 21; $x++) { ?>
                                                    <option value="<?= $x ?>"><?= $x ?></option>
                                                <? } ?>
                                            </select>
                                        </span> 
                                        <span class="game-result__score-dash">-</span> 
                                        <span class="game-result__score-result game-result__score-result--loser">
                                            <select class="form-control" id='score_2' style="display: inline; width: 70px;">
                                                <? for ($x = 0; $x <= 21; $x++) { ?>
                                                    <option value="<?= $x ?>"><?= $x ?></option>
                                                <? } ?>
                                            </select>
                                        </span>
                                    </div>
                                    <div id='box_challenge_button' class="box_challenge_button">
                                        <h3 class="game-result__title">Inserisci il punteggio della sfida<br/>e invialo al tuo <span class="blue_label">avversario</span>.</h3>
                                    </div>
                                </div>
                                <div class="game-result__team game-result__team--second">
                                    <div class="game-result__team-info">
                                        <h5 class="game-result__team-name"><?= $player2['name'] ?></h5>
                                        <div class="game-result__team-desc"><?= $player2['surname'] ?></div>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>
                    <div class="df-personal-info">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="challenge-dd">Data</label>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <select name="challenge-dd" id="challenge-dd" class="form-control">
                                                <option value="">Giorno</option>
                                                <? for ($x = 1; $x <= 31; $x++) { ?>
                                                    <option value="<?= $x ?>"><?= $x ?></option>
                                                <? } ?>
                                            </select>
                                        </div> 
                                        <div class="col-md-4">
                                            <select id="challenge-mm" class="form-control">
                                                <option value="">Mese</option>
                                                <option value="1">Gen</option>
                                                <option value="2">Feb</option>
                                                <option value="3">Mar</option>
                                                <option value="4">Apr</option>
                                                <option value="5">Mag</option>
                                                <option value="6">Giu</option>
                                                <option value="7">Lug</option>
                                                <option value="8">Ago</option>
                                                <option value="9">Sett</option>
                                                <option value="10">Ott</option>
                                                <option value="11">Nov</option>
                                                <option value="12">Dic</option>
                                            </select>
                                        </div> 
                                        <div class="col-md-4">
                                            <select id="challenge-yyyy" class="form-control">
                                                <option value="">Anno</option>
                                                <? for ($x = 1926; $x <= date("Y"); $x++) { ?>
                                                    <option value="<?= $x ?>"><?= $x ?></option>
                                                <? } ?>
                                            </select>
                                        </div> 
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="challenge-circolo">Circolo</label>
                                    <select id='circolo'  onclick="updateCircoloInfo()" class='form-control'>
                                        <option value='0'>Seleziona un circolo o inserisci l'indirizzo</option>
                                        <? foreach ($lista_circoli as $s) { ?>
                                            <? if ((strpos($s['cards'], "FIT") !== false && $h_user_detail['is_fit'] == 1) || (strpos($s['cards'], "UIPS") !== false && $h_user_detail['is_uisp'] == 1)) { ?>   
                                                <option value='<?= $s['id'] ?>'><?= $s['club_name'] ?></option>
                                            <? } ?>
                                        <? } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row" style="display: none">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="challenge-address">Indirizzo</label>
                                    <input type="text" class="form-control" value="<?= $detail['address'] ?>" name="challenge-address" id="challenge-address" placeholder="Inserisci l'indirizzo...">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="challenge-city">Città</label>
                                    <input type="text" class="form-control" value="<?= $detail['city'] ?>" name="challenge-city" id="challenge-city" placeholder="Inserisci la città...">
                                </div>
                            </div>
                        </div>
                        <div class="row" style="display: none">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="challenge-province">Provincia</label>
                                    <select name="challenge-province" id="challenge-province" class="form-control">
                                        <option value="">Inserisci la tua provincia...</option>
                                        <? foreach ($provinces as $s_prov) { ?>
                                            <option value="<?= $s_prov['id'] ?>"><?= $s_prov['name'] ?></option>
                                        <? } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="challenge-zip">CAP</label>
                                    <input type="text" class="form-control" value="<?= $detail['zip'] ?>" name="challenge-zip" id="challenge-zip" placeholder="Inserisci il CAP...">
                                </div>
                            </div>
                        </div>
                        <div class="form-group--submit">
                            <button id="match_send_score_button" onclick="updateChallenge(<?= $detail['id'] ?>,<?= $set_by ?>,<?= $check_by ?>)" class="btn btn-default btn-lg btn-block">Invia punteggio</button>
                        </div>
                        <div class="row">
                            <div class="col-md-12" id="challenge_status_messagge"></div>
                        </div>
                    </div>
                    </section>
                </div>
            </aside>

        </div>

        <!-- Last Game Log / End -->        
    </div>
</div>

<!-- Content / End -->
<script>

    $(document).ready(function () {
        //$("#circolo").val('<?= $detail['id_circolo'] ?>')
        $("#challenge-dd").val('<?= date("j", strtotime($detail['date_match'])) ?>');
        $("#challenge-mm").val('<?= date("n", strtotime($detail['date_match'])) ?>');
        $("#challenge-yyyy").val('<?= date("Y", strtotime($detail['date_match'])) ?>');
        $("#challenge-hours").val('<?= date("G", strtotime($detail['time_match'])) ?>');
        $("#challenge-minutes").val('<?= date("i", strtotime($detail['time_match'])) ?>');
        //$("#challenge-province").val('<?= $detail['id_province'] ?>');

        $("#score_1").val(<?= $detail['score_player_1'] ?>);
        $("#score_2").val(<?= $detail['score_player_2'] ?>);
    });

</script>

<? require_once 'footer.php'; ?>