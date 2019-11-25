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
    function inviaSfida(){
        $.post("lib/manage-challenge.php",{
            op_type: 'invia-sfida',
            player_1: <?=$_SESSION['user_id']?>,
            player_2: <?=$_GET['id']?>,
        },function(data){
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
                <li class="content-filter__item"><a href="scheda-giocatore.php?id=<?=$_GET['id']?>" class="content-filter__link">Riepilogo<br/>Partite</a></li>
                <?if($_SESSION['user_id'] != $_GET['id']){?>
                    <li class="content-filter__item content-filter__item--active"><a href="scheda-giocatore-sfida.php?id=<?=$_GET['id']?>" class="content-filter__link">Sfida<br/>questo giocatore</a></li>
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
<?
require_once './lib/class_user.php';
$user = new sagUser();
$list = $user->getList();
?>
<!-- Content 
================================================== -->
<div class="site-content">
    <div class="container">

        <div class="card card--has-table">
            <div class="card__header">
                <h4>Sfida questo giocatore</h4>
            </div>
            <div class="card__content">
                <div class="table-responsive">
                    <table class="table table-hover table-standings table-standings--full table-standings--full-soccer">
                        <thead>
                            <tr>
                                <th class="team-standings__team" style="width: 110px !important">&nbsp;</th>
                                <th class="team-standings__team">Giocatore</th>
                                <th class="team-standings__played">Partite</th>
                                <th class="team-standings__win">Vinte</th>
                                <th class="team-standings__lose">Perse</th>
                                <th class="team-standings__drawn">Pareggiate</th>
                                <th class="team-standings__goals-for">Finali</th>
                                <th class="team-standings__goals-against">Titoli</th>
                                <th class="team-standings__total-points">Classifica</th>
                            </tr>
                        </thead>
                        <tbody>
                            <? foreach ($list as $l) { ?>
                                <? if (($l['id'] != $_SESSION['user_id']) && $l['id'] == $_GET['id']) { ?>
                                    <tr>
                                        <td style="width: 110px !important"><span onclick="inviaSfida()" class="btn btn-primary-inverse btn-xs" style="cursor: pointer">SFIDA</span></td>
                                        <td class="team-standings__team">
                                            <a href="scheda-giocatore.php?id=<?= $l['id'] ?>">
                                                <div class="team-meta">
                                                    <div class="team-meta__info">
                                                        <h6 class="team-meta__name"><b><?= $l['name'] ?></b></h6>
                                                        <span class="team-meta__place"><?= $l['surname'] ?></span>
                                                    </div>
                                                </div>
                                            </a>
                                        </td>
                                        <td class="team-standings__played"><?= ($l['win'] + $l['lose'] + $l['draw']) ?></td>
                                        <td class="team-standings__win"><?= $l['win'] ?></td>
                                        <td class="team-standings__lose"><?= $l['lose'] ?></td>
                                        <td class="team-standings__drawn"><?= $l['draw'] ?></td>
                                        <td class="team-standings__goals-for">SOON</td>
                                        <td class="team-standings__goals-against">SOON</td>
                                        <td class="team-standings__total-points"><?= $l['ranking'] ?></td>
                                    </tr>   
                                <? } ?>
                            <? } ?>
                        </tbody>
                    </table>                    
                </div>
            </div>
        </div>
        <!-- Last Game Log -->
        <div class="card card--has-table">
            <div class="card__header">
                <h4>Vuoi sfidare un altro giocatore?</h4>
            </div>
            <div class="card__content">
                <div class="table-responsive">
                    <table class="table table-hover table-standings table-standings--full table-standings--full-soccer">
                        <thead>
                            <tr>
                                <th class="team-standings__team" style="width: 110px !important">&nbsp;</th>
                                <th class="team-standings__team">Giocatore</th>
                                <th class="team-standings__played">Partite</th>
                                <th class="team-standings__win">Vinte</th>
                                <th class="team-standings__lose">Perse</th>
                                <th class="team-standings__drawn">Pareggiate</th>
                                <th class="team-standings__goals-for">Finali</th>
                                <th class="team-standings__goals-against">Titoli</th>
                                <th class="team-standings__total-points">Classifica</th>
                            </tr>
                        </thead>
                        <tbody>
                            <? foreach ($list as $l) { ?>
                                <? if (($l['id'] != $_SESSION['user_id']) && $l['id'] != $_GET['id']) { ?>
                                    <tr>
                                        <td style="width: 110px !important"><span onclick="inviaSfida()" class="btn btn-primary-inverse btn-xs" style="cursor: pointer">SFIDA</span></td>
                                        <td class="team-standings__team">
                                            <a href="scheda-giocatore.php?id=<?= $l['id'] ?>">
                                                <div class="team-meta">
                                                    <div class="team-meta__info">
                                                        <h6 class="team-meta__name"><b><?= $l['name'] ?></b></h6>
                                                        <span class="team-meta__place"><?= $l['surname'] ?></span>
                                                    </div>
                                                </div>
                                            </a>
                                        </td>
                                        <td class="team-standings__played"><?= ($l['win'] + $l['lose'] + $l['draw']) ?></td>
                                        <td class="team-standings__win"><?= $l['win'] ?></td>
                                        <td class="team-standings__lose"><?= $l['lose'] ?></td>
                                        <td class="team-standings__drawn"><?= $l['draw'] ?></td>
                                        <td class="team-standings__goals-for">SOON</td>
                                        <td class="team-standings__goals-against">SOON</td>
                                        <td class="team-standings__total-points"><?= $l['ranking'] ?></td>
                                    </tr>   
                                <? } ?>
                            <? } ?>
                        </tbody>
                    </table>                    
                </div>
            </div>
        </div>
        <!-- Last Game Log / End -->        
    </div>
</div>

<!-- Content / End -->


<? require_once 'footer.php'; ?>