<? require_once 'header.php'; ?>
<!-- Pushy Panel - Dark -->
<?
$usr = new sagUser();
$card_detail = $usr->getDetail($_GET['id']);

require_once './lib/class_challenge.php';
$cha = new sagChallenge();
$allmatch = $cha->getMyAllFinishChallenges($_GET['id']);
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


<!-- Player Pages Filter -->
<? if (isset($_SESSION['logged']) && $_SESSION['logged'] == 1) { ?>
    <nav class="content-filter">
        <div class="container">
            <a href="#" class="content-filter__toggle"></a>
            <ul class="content-filter__list">
                <li class="content-filter__item content-filter__item--active"><a href="scheda-giocatore.php?id=<?=$_GET['id']?>" class="content-filter__link">Riepilogo<br/>Partite</a></li>
                <?if($_SESSION['user_id'] != $_GET['id']){?>
                    <li class="content-filter__item"><a href="scheda-giocatore-sfida.php?id=<?=$_GET['id']?>" class="content-filter__link">Sfida<br/>questo giocatore</a></li>
                <?}?>
                <?if($_SESSION['user_id'] == $_GET['id']){?>
                    <li class="content-filter__item"><a href="scheda-giocatore-modifica.php?id=<?=$_GET['id']?>" class="content-filter__link">Modifca<br/>i miei dati</a></li>
                    <li class="content-filter__item"><a href="scheda-giocatore-partite.php?id=<?=$_GET['id']?>" class="content-filter__link">Le mie<br/>partite</a></li>
                <?}?>
            </ul>
        </div>
    </nav>
<? } ?>
<!-- Player Pages Filter / End -->

<!-- Content 
================================================== -->
<div class="site-content">
    <div class="container">

        <!-- Last Game Log -->
        <div class="card card--has-table">
            <div class="card__header">
                <h4>Ultime partite giocate</h4>
            </div>
            <div class="card__content">
                <div class="table-responsive">
                    <table class="table table-hover table-standings table-standings--full table-standings--full-soccer">
                        <thead>
                            <tr>
                                <th class="team-standings__pos" style="width: 90px;">Data</th>
                                <th class="team-standings__win">Circolo</th>
                                <th class="team-standings__team"  style="text-align: right">Giocatore 1</th>
                                <th class="team-standings__played">Risultato</th>
                                <th class="team-standings__team">Giocatore 2</th>

                            </tr>
                        </thead>
                        <tbody>
                            <? foreach ($allmatch as $m) { ?>
                                <?
                                $player_1 = $usr->getDetail($m['player_1']);
                                $player_2 = $usr->getDetail($m['player_2']);
                                $circolo = $cha->getCircolo($m['id_circolo']);
                                ?>
                                <tr>
                                    <td class="team-standings__pos"><?= date("d-m-Y", strtotime($m['date_match'])) ?></td>
                                    <td class="team-standings__lose" style="text-align: left">
                                        <b><?= $circolo['club_name'] ?></b><br/>
                                        <b>Referente</b>: <?= $circolo['reference'] ?><br/>
                                        <b>Contatti</b>: <?= $circolo['email'] ?> - <?= $circolo['telephone'] ?><br/>
                                        <b>Indirizzo</b>: <?= $circolo['address'] ?> - <?= $circolo['zip'] ?>, <?= $circolo['city'] ?> (<?= $circolo['province'] ?>)<br/>
                                        <b>Carte accettate</b>: <?= $circolo['cards'] ?><br/>
                                        <b>Campi</b>: <?= $circolo['courts'] ?><br/>
                                        <b>Servizi</b>: <?= $circolo['services'] ?><br/>
                                    </td>     
                                    <td class="team-standings__team" style="text-align: right">
                                        <div class="team-meta" style="text-align: right">
                                            <div class="team-meta__info">
                                                <h6 class="team-meta__name"><?= $player_1['name'] ?></h6>
                                                <span class="team-meta__place"><?= $player_1['surname'] ?></span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="team-standings__played"><?= $m['score_player_1'] ?> - <?= $m['score_player_2'] ?></td>
                                    <td class="team-standings__team">
                                        <div class="team-meta">
                                            <div class="team-meta__info">
                                                <h6 class="team-meta__name"><?= $player_2['name'] ?></h6>
                                                <span class="team-meta__place"><?= $player_2['surname'] ?></span>
                                            </div>
                                        </div>
                                    </td>

                                </tr>  
                            <? } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
       
    </div>
</div>

<!-- Content / End -->


<? require_once 'footer.php'; ?>