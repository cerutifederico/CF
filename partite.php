<? require_once 'header.php'; ?>
<?
require_once './lib/class_user.php';
require_once './lib/class_challenge.php';
$usr = new sagUser();
$cha = new sagChallenge();
$allmatch = $cha->getAllFinishChallenges();
$circolo = $cha->getCircoli();
?>
<div class="page-heading" style="background-image: url(img/giocatori.jpg)">
    <div class="container">
        <div class="row">
            <div class="col-md-10 offset-md-1">
                <h1 class="page-heading__title">Elenco <span class="highlight">Partite</span></h1>                
            </div>
        </div>
    </div>
</div>
<!-- Page Heading / End -->
<!-- Content
================================================== -->
<div class="site-content">
    <div class="container">
        <div class="card card--has-table">
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
        <!-- Team Standings / End -->

    </div>
</div>

<!-- Content / End -->


<? require_once 'footer.php'; ?>