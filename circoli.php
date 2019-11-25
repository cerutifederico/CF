<? require_once 'header.php'; ?>
<?
require_once './lib/class_challenge.php';
$cha = new sagChallenge();
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
                                <th class="team-standings__win" style="width: auto !important">Circolo</th>
                                <th class="team-standings__team" style="width: auto !important">Referente</th>
                                <th class="team-standings__played" style="width: auto !important">Indirizzo</th>
                                <th class="team-standings__team" style="width: auto !important">Carte</th>
                                <th class="team-standings__team" style="width: auto !important">Campi</th>
                                <th class="team-standings__team" style="width: auto !important">Servizi</th>

                            </tr>
                        </thead>
                        <tbody>
                            <? foreach ($circolo as $c) { ?>
                                <tr>
                                    <td class="team-standings__lose" style="text-align: left">
                                        <b><?= $c['club_name'] ?></b>                                        
                                    </td>     
                                    <td class="team-standings__lose" style="text-align: left">
                                        <?= $c['reference'] ?><br/>
                                        <?= $c['email'] ?> - <?= $c['telephone'] ?>                                        
                                    </td>     
                                    <td class="team-standings__lose" style="text-align: left">
                                        <?= $c['address'] ?> - <?= $c['zip'] ?>, <?= $c['city'] ?> (<?= $c['province'] ?>)
                                    </td>     
                                    <td class="team-standings__lose" style="text-align: left">
                                        <?= $c['cards'] ?>
                                    </td>     
                                    <td class="team-standings__lose" style="text-align: left">
                                        <?= $c['courts'] ?>
                                    </td>     
                                    <td class="team-standings__lose" style="text-align: left">
                                        <?= $c['services'] ?>
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