<? require_once 'header.php'; ?>
<?
require_once './lib/class_user.php';

$user = new sagUser();
$list = $user->getList();
?>
<!-- Page Heading
================================================== -->
<div class="page-heading" style="background-image: url(img/giocatori.jpg)">
    <div class="container">
        <div class="row">
            <div class="col-md-10 offset-md-1">
                <h1 class="page-heading__title">Elenco <span class="highlight">Giocatori</span></h1>                
            </div>
        </div>
    </div>
</div>
<!-- Page Heading / End -->
<!-- Content
================================================== -->
<div class="site-content">
    <div class="container">
        <!-- Team Standings -->
        <div class="card card--has-table">
            <div class="card__content">
                <div class="table-responsive">
                    <table class="table table-hover table-standings table-standings--full table-standings--full-soccer">
                        <thead>
                            <tr>
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
                                <tr>
                                    <td class="team-standings__team">
                                        <a href="scheda-giocatore.php?id=<?=$l['id']?>">
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