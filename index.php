<? require_once 'header.php'; ?>
<!-- Hero Slider
================================================== -->
<div class="hero-slider-wrapper">

    <div class="hero-slider">

        <!-- Slide #0 -->
        <div class="hero-slider__item hero-slider__item--img1" style="background-image: url(img/visual_home.jpg)">

            <div class="container hero-slider__item-container">
                <div class="row">
                    <div class="col-md-8 offset-md-2">
                        <!-- Post Meta - Top -->
                        <div class="post__meta-block post__meta-block--top">

                            <!-- Post Category -->
                            <div class="post__category">
                                <span class="label posts__cat-label">Set A Game</span>
                            </div>
                            <!-- Post Category / End -->

                            <!-- Post Title -->
                            <h1 class="page-heading__title"><a href="_soccer_blog-post-1.html"><span class="highlight"> new social<br/>tennis platform</span></a></h1>
                            <!-- Post Title / End -->

                            <!-- Post Meta Info -->

                            <!-- Post Meta Info / End -->

                            <!-- Post Author -->

                            <!-- Post Author / End -->

                        </div>
                        <!-- Post Meta - Top / End -->
                    </div>
                </div>
            </div>

        </div>
        <!-- Slide #1 / End -->        
    </div>
</div>
<!-- Content
================================================== -->
<div class="site-content">
    <div class="container">
        <div class="row">
            <!-- Content -->
            <div class="content col-lg-8">
                <div class="card card--has-table">
                    <div class="card__header">
                        <h4>Ultime partite giocate</h4>
                    </div>
                    <?
                    require_once './lib/class_user.php';
                    require_once './lib/class_challenge.php';
                    $usr = new sagUser();
                    $cha = new sagChallenge();
                    $allmatch = $cha->getAllFinishChallenges();
                    ?>
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
                                        <b><?= $circolo['club_name'] ?></b>
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

                <!-- Featured News -->


            </div>
            <!-- Content / End -->

            <!-- Sidebar -->
            <div id="sidebar" class="sidebar col-lg-4">
                <!-- Widget: Standings -->
                <aside class="widget card widget--sidebar widget-standings">
                    <div class="widget__title card__header card__header--has-btn">
                        <h4>Classifica</h4>
                        <a href="classifica.php" class="btn btn-default btn-outline btn-xs card-header__button">Tutta la classifica</a>
                    </div>
                    <div class="widget__content card__content">
                        <div class="table-responsive">
                            <?$list = $usr->getChartList();?>
                            <table class="table table-hover table-standings table-standings--full table-standings--full-soccer">
                        <thead>
                            <tr>
                               <th class="team-standings__total-points">#</th>
                               <th class="team-standings__team">Giocatore</th>
                                <th class="team-standings__played">Punti</th>
                                <th class="team-standings__played">M</th>
                                <th class="team-standings__win">W</th>
                                <th class="team-standings__lose">L</th>
                            </tr>
                        </thead>
                        <tbody>
                            <? foreach ($list as $l) { ?>
                                <tr>
                                    <td class="team-standings__total-points"><?= $l['ranking'] ?></td>
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
                                    <td class="team-standings__played"><?= $l['score_general'] ?></td>
                                    <td class="team-standings__played"><?= ($l['win'] + $l['lose'] + $l['draw']) ?></td>
                                    <td class="team-standings__win"><?= $l['win'] ?></td>
                                    <td class="team-standings__lose"><?= $l['lose'] ?></td>                                    
                                </tr>                            
                            <? } ?>
                        </tbody>
                    </table>
                        </div>
                    </div>
                </aside>
                <!-- Widget: Standings / End -->

            </div>
            <!-- Sidebar / End -->
        </div>

    </div>
</div>

<!-- Content / End -->
<? require_once 'footer.php'; ?>