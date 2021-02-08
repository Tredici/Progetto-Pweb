<?php
    //Gestione accesso senza permesso
    session_start();
    if(isset($_SESSION["user"])) {
        header("Location: ../index.php");
        exit;
    }
?>
<!DOCTYPE html>
<html lang="it">
    <head>
        <title>Login</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="../css/StilePrincipale.css">
        
        <link rel="stylesheet" type="text/css" href="../css/StileLogin.css">
        
        <link rel="icon" href="../logo.bmp"  type="image/bmp">
        
        <script type="text/javascript" src="../js/GestioneLogin.js"></script>
    </head>
    <body>
        <div class="wrapper">
            <header>
                <h1>Pagina di login</h1>
            </header>
            <?php require_once 'inline/Navbar.php'; ?>
            <div class="container">

                <h3>Cosa devo fare?</h3>
                <p>Inserisci username e password nel form e premi invio.</p>

                <p>Per la <strong>Documentazione</strong> clicka <a href="Documentazione.html">QUI</a>.</p>
                
                <div id="divLogin">
                    <h3>Form di Login</h3>
                    <form id="formLogin"> <!-- action="/CoseBuffe/php/Autentication.php" method="post" autocomplete="off" -->
                        <div id="Credenziali">
                            <div class="userdata">
                                <label for="usr">Username:</label>
                                <input type="text" name="username" id="usr" required="">
                            </div>
                            <div class="userdata">
                                <label for="pass">Password:</label>
                                <input type="password" name="password" id="pass" required="">
                            </div>
                        </div>
                        <div class="confirm">
                            <input type="reset" value="Annulla" name="bottoneRisposta" class="button">
                            <input type="submit" value="Login" name="bottoneRisposta" class="button">
                        </div>
                    </form>
                </div>
                <div class="push"></div>
            </div>
        </div>
        <?php require_once 'inline/Footer.php'; ?>
    </body>
</html>
