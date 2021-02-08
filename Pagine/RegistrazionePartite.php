<?php
    //Gestione accesso senza permesso
    session_start();
    if(!isset($_SESSION["user"])) {
        header("Location: ../index.php");
        exit;
    }
    
    require_once '../php/ClassConnectionBuilder.php';
    require_once '../php/Utility.php';
    require_once 'Appoggio/GestioneDOMRegistrazionePartite.php';
    
    // La crea qui perché servirà molte volte dopo
    $conn = ClassConnectionBuilder::buildDefaultConnection();
    $usr = $_SESSION["user"];

    // start buffering dell'output
    ob_start();
?>
<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <title>Registrazione Partite</title>
        
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        
        <link rel="stylesheet" type="text/css" href="../css/StilePrincipale.css">
        <link rel="stylesheet" type="text/css" href="../css/StileRegistrazionePartite.css">
        
        <link rel="icon" href="../logo.bmp"  type="image/bmp">
        
        <script type="text/javascript" src="../js/UtilityRegistrazionePartite.js"></script>
        
    </head>
    <body>
        
        <header>
            <h1>Registrazione Partite</h1>
        </header>
        <?php require_once 'inline/Navbar.php'; ?>
        
        <div id="container" class="container">
            <h3>Descrizione</h3>
            <p>
                In questa pagina andrà segnato l'esito delle partite giocate 
                organizzate tramite annunci. Poiché è frequente (o almeno lo era)
                che mentre qualcuno gioca altri stanno a guardare ho deciso di 
                dare la possibilità anche a chi non è uno dei giocatori di 
                registrare l'esito degli incontri. Comunque, al fine di prevenire 
                sviste e imbrogli (anche se questi onestamente non mi aspetto vi
                siano) è stato implementato un sistema di doppia conferma dei 
                risultati che impone a tutti e 4 i partecipanti a un incontro di
                confermare la correttezza del risultato prima che questo sia 
                effettivamente registrato.
            </p>
                
            <?php
            try{
                if(completamentoAnnuncio()) {}
                else if(confermaAnnuncio()) {}
                else if(visionaAnnuncio()) {}
                else{
                    echo "Errore! <br>";
                    throw new Exception("Go giù");
                }
            }
            catch(Exception $exc)
            {
                header($_SERVER["SERVER_PROTOCOL"] . " 500 Internal Server Error");
                header("Location: Error.php");
                exit(1);
            }
            ?>
            <div class="push"></div>
        </div>
        <?php require_once 'inline/Footer.php'; ?>
    </body>
</html>
<?php
    // flush dell'output nel buffer
    ob_end_flush();
?>