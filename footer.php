

<!-- Footer
================================================== -->
<footer id="footer" class="footer">

    <!-- Footer Widgets -->
    <div class="footer-widgets">
        <div class="footer-widgets__inner">
            <div class="container">
                <div class="row">
                    <div class="col-sm-6 col-lg-3">
                        <div class="footer-col-inner">

                            <!-- Footer Logo -->
                            <div class="footer-logo footer-logo--has-txt">
                                <a href="index.html">
                                    <img src="img/logo-tennis.png"  style="width: 100px"  class="footer-logo__img">
                                    <div class="footer-logo__heading">
                                        <h5 class="footer-logo__txt">Set A Game</h5>
                                        <span class="footer-logo__tagline">Ceruti, Bonacina, Spagnuolo</span>
                                    </div>
                                </a>
                            </div>
                            <!-- Footer Logo / End -->

                            <!-- Widget: Contact Info -->
                            
                            <!-- Widget: Contact Info / End -->
                        </div>
                    </div>

                    <div class="clearfix visible-sm"></div>							
                </div>
            </div>
        </div>



    </div>
    <!-- Footer Widgets / End -->

    <!-- Footer Secondary -->
    <div class="footer-secondary">
        <div class="container">
            <div class="footer-secondary__inner">
                <div class="row">
                    <div class="col-md-4">
                        <div class="footer-copyright"><a href="_soccer_index.html">Set a game</a> <?=date("Y")?> &nbsp; | &nbsp; All Rights Reserved</div>
                    </div>
                    <div class="col-md-8">
                        <ul class="footer-nav footer-nav--right footer-nav--condensed footer-nav--sm">
                            <li class="footer-nav__item"><a href="index.php">Home</a></li>
                            <li class="footer-nav__item"><a href="circoli.php">Circoli</a></li>
                            <li class="footer-nav__item"><a href="giocatori.php">Giocatori</a></li>
                            <li class="footer-nav__item"><a href="classifica.php">Classifica</a></li>
                            <li class="footer-nav__item"><a href="partite.php">Partite</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Footer Secondary / End -->
</footer>
<!-- Footer / End -->

<script>
    function loginUser() {
        var email = $("#u_email").val();
        var password = $("#u_password").val();
        $.post("lib/manage-user.php", {
            op_type: 'login',
            u_mail: email,
            u_password: password
        }, function (data) {
            if (data == "LOGIN-ERROR") {
                $("#u_message").html("Indirizzo mail o password errata!");
            } else {
                location.reload();
            }
        });
    }

    function quickCreate() {
        var nome = $("#nome").val();
        var cognome = $("#cognome").val();
        var email = $("#email").val();
        var password = $("#password").val();
        $.post("lib/manage-user.php", {
            op_type: 'create-quick',
            nome: nome,
            cognome: cognome,
            email: email,
            password: password
        }, function (data) {
            if (data == "MAIL-EXISTS") {
                $("#message").html("Indirizzo mail già esistente!");
            } else {
                $("#message").html("Iscrizione avvenuta con successo!");
            }
        });
    }
</script>
<!-- Login/Register Tabs Modal -->
<div class="modal fade" id="modal-login-tabs" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg modal--login modal--login-only" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="modal-account-holder">
                    <div class="modal-account__item modal-account__item--logo" style="background-image: url(img/modal_background.jpg)">
                        <p class="modal-account__item-register-txt">Non hai un account? <a href="#" data-toggle="modal" data-target="#modal-register-tabs">Registrati subito</a> e inizia a giocare!</p>
                    </div>
                    <div class="modal-account__item">
                        <div class="modal-form">
                            <h5>Accedi al tuo profilo</h5>
                            <div class="form-group">
                                <input type="email" id='u_email' class="form-control" placeholder="Inserisci la tua email...">
                            </div>
                            <div class="form-group">
                                <input type="password" id='u_password' class="form-control" placeholder="Inserisci la password...">
                            </div>
                            <div class="form-group form-group--pass-reminder">
                                <a href="#">Hai dimenticato la password?</a>
                            </div>
                            <div class="form-group form-group--submit">
                                <span onclick="loginUser()" class="btn btn-primary-inverse btn-block">Accedi</span>
                            </div>   
                            <div id='u_message' class="modal-form--note"></div>
                        </div>
                        <!-- Tab panes -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal-register-tabs" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg modal--login modal--login-only" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="modal-account-holder">
                    <div class="modal-account__item modal-account__item--logo" style="background-image: url(img/modal_background.jpg)">
                        <p class="modal-account__item-register-txt">Non hai un account? <a href="#">Registrati subito</a> e inizia a giocare!</p>
                    </div>
                    <div class="modal-account__item">
                        <div class="modal-form">
                            <h5>Registrati subito!</h5>
                            <div class="form-group">
                                <input type="text" id='nome' class="form-control" placeholder="Inserisci il tuo nome...">
                            </div>
                            <div class="form-group">
                                <input type="text" id='cognome' class="form-control" placeholder="Inserisci il tuo cognome...">
                            </div>
                            <div class="form-group">
                                <input type="email" id='email' class="form-control" placeholder="Inserisci la tua email...">
                            </div>
                            <div class="form-group">
                                <input type="password" id='password' class="form-control" placeholder="Inserisci la password...">
                            </div>
                            <div class="form-group form-group--submit">
                                <span onclick="quickCreate()" class="btn btn-success btn-block">Crea il tuo account</span>
                            </div>
                            <div id='message' class="modal-form--note"></div>
                            <div class="modal-form--note">Riceverai una mail di conferma per l'attivazione del tuo account.</div>
                        </div>
                        <!-- Tab panes -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Login/Register Tabs Modal / End -->

</div>

<!-- Javascript Files
================================================== -->
<!-- Core JS -->

<script src="assets/vendor/jquery/jquery-migrate.min.js"></script>
<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/core.js"></script>

<!-- Vendor JS -->
<script src="assets/vendor/twitter/jquery.twitter.js"></script>

<!-- Template JS -->
<script src="assets/js/init.js"></script>
<script src="assets/js/custom.js"></script>

</body>
</html>
