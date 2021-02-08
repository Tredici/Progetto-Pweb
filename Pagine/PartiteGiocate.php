<?php
    //Gestione accesso senza permesso
    session_start();
    if(!isset($_SESSION["user"])) {
        header("Location: ../index.php");
        exit;
    }
    $usr = $_SESSION["user"];
?>
<!DOCTYPE html>
<!--
Pagina dove sono mostrate tutte le partite giocate dal giocatore in questione
-->
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <title>Partite Giocate</title>
        
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <link rel="stylesheet" type="text/css" href="../css/StilePrincipale.css">
        <link rel="stylesheet" type="text/css" href="../css/StilePaginaPartite.css">
        
        <link rel="icon" href="../logo.bmp"  type="image/bmp">
        
        <script type="text/javascript" src="../js/DatiPartite.js"></script>
    </head>
    <body>
        <div class="wrapper">
            <header>
                <h1>Le tue partite</h1>
            </header>
            <?php require_once 'inline/Navbar.php'; ?>
            <div class="container" id="container" data-me="<?=$usr?>">

                <div class="descrizione">
                    <h3>Descrizione</h3>
                    <p>
                        Ogni volta che giocherai e registrerai il risultato di una 
                        partita questo sarà visibile qui.
                    </p>
                </div>
                <div id="segnaposto" class="warning">
                    <span>
                        Caricamento...
                    </span>
                </div>
            </div>
            <div class="push"></div>
        </div>
        <?php require_once 'inline/Footer.php'; ?>
    </body>
</html>
