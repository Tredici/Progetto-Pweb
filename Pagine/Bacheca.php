<?php
    //Gestione accesso senza permesso
    session_start();
    if(!isset($_SESSION["user"])) {
        header("Location: ../index.php");
        exit;
    }
    
    require_once '../php/ClassConnectionBuilder.php';
    require_once '../php/Utility.php';
    require_once 'Appoggio/GestioneDOMAnnunci.php';
    
    // La crea qui perché servirà molte volte dopo
    $conn = ClassConnectionBuilder::buildDefaultConnection();
    $usr = $_SESSION["user"];
    $user = $conn->getUserData($usr);
?>
<!DOCTYPE html>
<!--
    La bacheca dove si sottoscrivono e si posizionano gli annunci
-->
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <title>Bacheca</title>
        
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        
        <link rel="stylesheet" type="text/css" href="../css/StilePrincipale.css">
        <link rel="stylesheet" type="text/css" href="../css/StileNuovaBacheca.css">
        
        <link rel="icon" href="../logo.bmp"  type="image/bmp">
        
        <script type="text/javascript" src="../js/GestioneNuovaBacheca.js"></script>
        <script type="text/javascript" src="../js/ScaricamentoAnnunci.js"></script>
        
    </head>
    <body>
        <div class="wrapper">
            <header>
                <h1>Bacheca</h1>
            </header>
            <?php require_once 'inline/Navbar.php'; ?>

            <div id="container" class="container">
                <div class="descrizione">
                    <h3>Descrizione</h3>
                    <p>
                        Pagina della bacheca. Crea i tuoi annunci, sottoscrivi 
                        quelli esistenti o registra i risultati delle partite 
                        giocate.
                    </p>
                </div>

                <div class="bacheca">
                    <table id="Bacheca" class="bacheca">
                        <caption> Annunci
                        <thead>
                            <tr>
                                <th>Autore
                                <th>Partita
                                <th>Data e ora
                                <th>Note
                                <th>Giocatori
                                <th>Stato
                                <th>Azioni
                        </thead>
                        <tbody>

                            <tr id="NuovoAnnuncio" data-me="<?=$usr?>">
                                <th><a href="PaginaPersonale.php"><?=htmlentities($user->getUsername())?></a>
                                <td><select name="TipoPartita" id="TipoPartita" required form="RegistrazioneNuovoAnnuncio">
                                        <option label="Tipo" value="" disabled="">
                                        <option value="singola">Singola
                                        <option value="trittico">Trittico
                                    </select>
                                <td><input type="date" name="date" required form="RegistrazioneNuovoAnnuncio" class="datetime">
                                    <input step="300" type="time" name="time" required form="RegistrazioneNuovoAnnuncio" class="datetime">
                                <td id="SpazioTextarea"><textarea name="Notes" form="RegistrazioneNuovoAnnuncio"></textarea>
                                <td>Giocatori
                                <td>Creazione
                                <td><input type="reset"  class="button" value="Annulla" form="RegistrazioneNuovoAnnuncio">
                                    <input type="Submit" class="button" value="Invia"   form="RegistrazioneNuovoAnnuncio">
                        </tbody>

                    </table>
                    <p>
                        Importante: un appuntamento può essere fissato solo fino 
                        alla mezzanotte del terzo quarto giorno a partire dal momento attuale.
                        <br>
                        L'allineamento è ai 5 minuti (es: 12:45 Ok, 13:33 No).
                    </p>
                </div>

                <form id="RegistrazioneNuovoAnnuncio">

                </form>
            </div>
            <div class="push"></div>
        </div>
        <?php require_once 'inline/Footer.php'; ?>
    </body>
</html>
