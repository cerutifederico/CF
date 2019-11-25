<? require_once 'header.php'; ?>
<!-- Pushy Panel - Dark -->
<?
$usr = new sagUser();
$card_detail = $usr->getDetail($_GET['id']);

require_once './lib/class_challenge.php';
$cha = new sagChallenge();
$allmatch = $cha->getMyAllFinishChallenges($_GET['id']);

$usr_detail = $usr->getDetail($_GET['id']);

require_once 'lib/class_setting.php';
$set = new sagSetting();
$provinces = $set->getProvinces();
$countries = $set->getCountries();
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
                <li class="content-filter__item"><a href="scheda-giocatore.php?id=<?= $_GET['id'] ?>" class="content-filter__link">Riepilogo<br/>Partite</a></li>
                <? if ($_SESSION['user_id'] != $_GET['id']) { ?>
                    <li class="content-filter__item"><a href="scheda-giocatore-sfida.php?id=<?= $_GET['id'] ?>" class="content-filter__link">Sfida<br/>questo giocatore</a></li>
                <? } ?>
                <? if ($_SESSION['user_id'] == $_GET['id']) { ?>
                    <li class="content-filter__item content-filter__item--active"><a href="scheda-giocatore-modifica.php?id=<?= $_GET['id'] ?>" class="content-filter__link">Modifca<br/>i miei dati</a></li>
                    <li class="content-filter__item"><a href="scheda-giocatore-partite.php?id=<?= $_GET['id'] ?>" class="content-filter__link">Le mie<br/>partite</a></li>
                <? } ?>
            </ul>
        </div>
    </nav>
<? } ?>
<!-- Player Pages Filter / End -->

<!-- Content 
================================================== -->
<style>
    .select.form-control {color: rgba(154,157,162,1) !important; }
</style>
<script>
    function checkField(field_id) {
        var field_value = $("#" + field_id).val();
        if (field_value == "") {
            $("#" + field_id).addClass("error-field");
            $("#" + field_id).focus();
            return false;
        } else {
            $("#" + field_id).removeClass("error-field");
            return true;
        }
    }

    function checkFieldSelect(field_id) {
        var field_value = $("#" + field_id).val();
        if (field_value == '') {
            $("#" + field_id).addClass("error-field");
            $("#" + field_id).focus();
            return false;
        } else {
            $("#" + field_id).removeClass("error-field");
            return true;
        }
    }

    function checkFieldEmail(field_id) {
        var field_value = $("#" + field_id).val();
        if (/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))$/i.test(field_value)) {
            $("#" + field_id).removeClass("error-field");
            return true;
        } else {
            $("#" + field_id).addClass("error-field");
            $("#" + field_id).focus();
            return false;
        }
    }
    function updateUser(user_id) {
        var first_name = $("#account-first-name").val();
        var last_name = $("#account-last-name").val();
        var gender = $("#account-gender").val();
        var account_nationality = $("#account-nationality").val();
        var account_nation = $("#account-nation").val();
        var place_birthday = $("#account-place_birthday").val();
        var province_birthday = $("#account-province_birthday").val();
        var birthday_dd = $("#account-dd").val();
        var birthday_mm = $("#account-mm").val();
        var birthday_yyyy = $("#account-yyyy").val();
        var city = $("#account-city").val();
        var codfiscale = $("#account-codfiscale").val();
        var telephone = $("#account-telephone").val();
        var is_blank = 0;
        var is_fit = 0;
        var is_uisp = 0;
        var is_asc = 0;
        var is_other = 0;
        if ($("#account-card-is_blank").is(":checked")) {
            is_blank = 1;
        }
        if ($("#account-card-is_fit").is(":checked")) {
            is_fit = 1;
        }
        if ($("#account-card-is_uisp").is(":checked")) {
            is_uisp = 1;
        }
        if ($("#account-card-is_asc").is(":checked")) {
            is_asc = 1;
        }
        if ($("#account-card-is_other").is(":checked")) {
            is_other = 1;
        }
        var address = $("#account-address").val();
        var zip = $("#account-zip").val();
        var province = $("#account-province").val();
        var email = $("#account-email").val();

        if (checkField("account-first-name") &&
                checkField("account-last-name") &&
                checkFieldSelect("account-dd") &&
                checkFieldSelect("account-mm") &&
                checkFieldSelect("account-yyyy") &&
                checkFieldSelect("account-gender") &&
                checkField("account-place_birthday") &&
                checkFieldSelect("account-province_birthday") &&
                checkField("account-city") &&
                checkField("account-codfiscale") &&
                checkField("account-telephone")) {
            $("#update_status_messagge").html("Aggiornamento in corso...");
            $.post("lib/manage-user.php", {
                op_type: "update",
                user_id: user_id,
                first_name: first_name,
                last_name: last_name,
                gender: gender,
                birthday: birthday_dd + "/" + birthday_mm + "/" + birthday_yyyy,
                nationality: account_nationality,
                nation: account_nation,
                place_birthday: place_birthday,
                province_birthday: province_birthday,
                city: city,
                codfiscale: codfiscale,
                telephone: telephone,
                is_blank: is_blank,
                is_fit: is_fit,
                is_uisp: is_uisp,
                is_asc: is_asc,
                is_other: is_other,
                email: email,
                address: address,
                zip: zip,
                province: province}, function (data) {
                if (data >= 0) {
                    $("#update_status_messagge").html("<span class='blue_label'>Aggiornamento avvenuto con successo.</span>");
                }
            });
        } else {
            $("#update_status_messagge").html("Si prega di compilare tutti i campi.");
        }
    }
</script>
<div class="site-content">
    <div class="container">

        <!-- Last Game Log -->
        <div class="card card--has-table">
            <div class="card">
                <div class="card__header">
                    <h4>Modifica i miei dati</h4>
                </div>
                <div class="card__content">
                    <div class="df-personal-info">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="account-first-name">Nome*</label>
                                    <input type="text" value="<?= $usr_detail['name'] ?>" class="form-control" name="account-first-name" id="account-first-name" placeholder="Inserisci il tuo nome..." />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="account-last-name">Cognome*</label>
                                    <input type="text" value="<?= $usr_detail['surname'] ?>" class="form-control" name="account-last-name" id="account-last-name" placeholder="Inserisci il tuo cognome..." />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="account-codfiscale">Codice fiscale*</label>
                                    <input type="text" class="form-control" value="<?= $usr_detail['codfisc'] ?>" name="account-codfiscale" id="account-codfiscale" placeholder="Inserisci il tuo codice fiscale..." />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="account-dd">Data di nascita*</label>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <select name="account-dd" id="account-dd" class="form-control">
                                                <option value="">Giorno</option>
                                                <? for ($x = 1; $x <= 31; $x++) { ?>
                                                    <option value="<?= $x ?>"><?= $x ?></option>
                                                <? } ?>
                                            </select>
                                        </div> 
                                        <div class="col-md-4">
                                            <select id="account-mm" class="form-control">
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
                                            <select id="account-yyyy" class="form-control">
                                                <option value="">Anno</option>
                                                <? for ($x = 1926; $x <= date("Y"); $x++) { ?>
                                                    <option value="<?= $x ?>"><?= $x ?></option>
                                                <? } ?>
                                            </select>
                                        </div> 
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="account-place_birthday">Luogo di nascita*</label>
                                    <input type="text" class="form-control" value="<?= $usr_detail['place_birthday'] ?>" name="account-place_birthday" id="account-place_birthday" placeholder="Inserisci il tuo luogo di nascita..." />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="account-province_birthday">Provincia di nascita</label>
                                    <select id="account-province_birthday" class="form-control">
                                        <option value="0">Inserisci la tua provincia di nascita...</option>
                                        <? foreach ($provinces as $s_prov) { ?>
                                            <option value="<?= $s_prov['id'] ?>"><?= $s_prov['name'] ?></option>
                                        <? } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="account-nationality">Nazionalità</label>
                                <select id="account-nationality" class="form-control">
                                    <option value="">Inserisci la tua nazionalità...</option>
                                    <? foreach ($countries as $s) { ?>
                                        <option value="<?= $s['alpha_2'] ?>"><?= $s['nationality'] ?></option>
                                    <? } ?>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="account-address">Indirizzo</label>
                                    <input type="text" class="form-control" value="<?= $usr_detail['address'] ?>" name="account-address" id="account-address" placeholder="Inserisci il tuo indirizzo..." />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="account-city">Città*</label>
                                    <input type="text" class="form-control" value="<?= $usr_detail['city'] ?>" name="account-city" id="account-city" placeholder="Inserisci la tua città..." />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="account-province">Provincia</label>
                                    <select id='account-province' class="form-control">
                                        <option value="0">Seleziona la tua provincia</option>
                                        <? foreach ($provinces as $s) { ?>
                                            <option value="<?= $s['id'] ?>"><?= $s['name'] ?></option>
                                        <? } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="account-zip">CAP</label>
                                    <input type="text" class="form-control" value="<?= $usr_detail['zip'] ?>" name="account-zip" id="account-zip" placeholder="Inserisci il tuo CAP..." />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="account-nation">Stato</label>
                                <select id="account-nation" class="form-control">
                                    <option value="">Inserisci lo stato...</option>
                                    <? foreach ($countries as $s) { ?>
                                        <option value="<?= $s['alpha_2'] ?>"><?= $s['name'] ?></option>
                                    <? } ?>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="account-gender">Sesso</label>
                                    <select name="account-gender" id="account-gender" class="form-control">
                                        <option value="">Specificare il sesso...</option>
                                        <option value="M">Maschio</option>
                                        <option value="F">Femmina</option>
                                    </select>
                                </div>                                
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="account-telephone">Telefono*</label>
                                    <input type="text" class="form-control" value="<?= $usr_detail['telephone'] ?>" name="account-telephone" id="account-telephone" placeholder="Inserisci il tuo telefono..." />
                                </div>
                            </div>
                        </div>
                        <div class="row">                            
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="account-card">Tesserato</label><br/>
                                    <label class="checkbox checkbox-inline">
                                        <input type="checkbox" id="account-card-is_blank" value="" <? if ($usr_detail['is_blank'] == 1) { ?> checked <? } ?> onclick="checkCard('is_blank', '<?= $_SESSION['country'] ?>');" > NESSUNA
                                        <span class="checkbox-indicator"></span>
                                    </label>
                                    <label class="checkbox checkbox-inline">
                                        <input type="checkbox" id="account-card-is_fit" value="" <? if ($usr_detail['is_fit'] == 1) { ?> checked <? } ?> onclick="checkCard('is_fit', '<?= $_SESSION['country'] ?>');" > FIT
                                        <span class="checkbox-indicator"></span>
                                    </label>
                                    <label class="checkbox checkbox-inline">
                                        <input type="checkbox" id="account-card-is_uisp" value="" <? if ($usr_detail['is_uisp'] == 1) { ?> checked <? } ?> onclick="checkCard('is_uisp', '<?= $_SESSION['country'] ?>');" > UISP
                                        <span class="checkbox-indicator"></span>
                                    </label>
                                    <label class="checkbox checkbox-inline">
                                        <input type="checkbox" id="account-card-is_asc" value="" <? if ($usr_detail['is_asc'] == 1) { ?> checked <? } ?> onclick="checkCard('is_asc', '<?= $_SESSION['country'] ?>');" > ASC
                                        <span class="checkbox-indicator"></span>
                                    </label>
                                    <label class="checkbox checkbox-inline">
                                        <input type="checkbox" id="account-card-is_other" value="" <? if ($usr_detail['is_other'] == 1) { ?> checked <? } ?> onclick="checkCard('is_other', '<?= $_SESSION['country'] ?>');" > ALTRO
                                        <span class="checkbox-indicator"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group--submit">
                            <button onclick="updateUser(<?= $_SESSION['user_id'] ?>)" class="btn btn-default btn-lg btn-block">Aggiorna dati</button>
                        </div>
                        <div class="row">
                            <div class="col-md-12" id="update_status_messagge"></div>
                        </div>
                    </div>
                </div>
            </div>            
        </div>
    </div>
</div>
<!-- Content / End -->

<script>
    $(document).ready(function () {
        
        $("#account-dd").val('<?= date("j", strtotime($usr_detail['birthday'])) ?>');
        $("#account-mm").val(<?= date("n", strtotime($usr_detail['birthday'])) ?>);
        $("#account-yyyy").val(<?= date("Y", strtotime($usr_detail['birthday'])) ?>);
        $("#account-nationality").val('<?= $usr_detail['nationality'] ?>');
        $("#account-nation").val('<?= $usr_detail['nation'] ?>');
        $("#account-province_birthday").val('<?= $usr_detail['id_province_birthday'] ?>');
        $("#account-card").val('<?= $usr_detail['card'] ?>');
        $("#account-province").val('<?= $usr_detail['id_province'] ?>');
        $("#account-gender").val('<?= $usr_detail['gender'] ?>');        

    });

</script>

<? require_once 'footer.php'; ?>