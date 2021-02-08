<?php
    //Gestione accesso senza permesso
    session_start();
?>
<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <title>Gestione Richieste</title>
        
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="../css/StilePrincipale.css">
        <link rel="stylesheet" type="text/css" href="../css/StileGestioneRichieste.css">
        
        <link rel="icon" href="../logo.bmp"  type="image/bmp">
        
        <script type="text/javascript" src="../js/GestioneRichiesteIscrizione.js"></script>
        
    </head>
    <body>
        <div class="wrapper">
            <header>
                <h1>Gestione Richieste</h1>
            </header>
            <?php require_once 'inline/Navbar.php'; ?>

            <div class="container">
                <table id="Richieste">
                    <caption> Richieste di iscrizione ricevute
                    <thead>
                        <tr>
                            <th>Nome
                            <th>Cognome
                            <th>Indirizzo email
                            <th>Data di nascita
                            <th>Sesso
                            <th>Data richiesta
                            <th>Stato

                    <tbody id="richieste">
                        <!-- È pensata per venire riempita in maniera dinamica -->
                        <tr>
                            <td colspan="8"> Caricamento...
                    <tfoot>
                </table>
            </div>
            <div class="push"></div>
        </div>
        <?php require_once 'inline/Footer.php'; ?>
    </body>
</html>
