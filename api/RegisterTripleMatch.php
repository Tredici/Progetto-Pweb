<?php

/* 
 * Corrispondente a RegisterSingleMatch ma per i trittici
 */

require_once '../php/ClassConnectionBuilder.php';

// controlla che il protocollo utilizzato sia POST
if(!isset($_SERVER['REQUEST_METHOD']) or $_SERVER['REQUEST_METHOD'] !== 'POST')
{
    header($_SERVER["SERVER_PROTOCOL"] . " 501 Not Implemented");
    echo "Invalid method.";
    exit;
}

session_start();

// controlla se la richiesta fa capo a un utente attualmente connesso
if(!isset($_SESSION["user"]))
{
    header($_SERVER["SERVER_PROTOCOL"] . " 401 Unauthorized");
    echo "Can't execute.";
    exit;
}

// stavolta permetto di ogni tipo di richiesta
if(!isset($_REQUEST["G1"]) || !isset($_REQUEST["G2"])
  || !isset($_REQUEST["G3"]) || !isset($_REQUEST["G4"])
    || !isset($_REQUEST["PunteggioP1S1"]) || !isset($_REQUEST["PunteggioP1S2"])
    || !isset($_REQUEST["PunteggioP2S1"]) || !isset($_REQUEST["PunteggioP2S2"])
    || !isset($_REQUEST["PunteggioP3S1"]) || !isset($_REQUEST["PunteggioP3S2"])
)
{
    header($_SERVER["SERVER_PROTOCOL"] . " 400 Bad Request");
    echo "Missing arguments.";
    exit;
}

$G1 = $_REQUEST["G1"];
$G2 = $_REQUEST["G2"];
$G3 = $_REQUEST["G3"];
$G4 = $_REQUEST["G4"];

//dati partita 1
$PunteggioP1S1 = $_REQUEST["PunteggioP1S1"];
$PunteggioP1S2 = $_REQUEST["PunteggioP1S2"];
//dati partita 2
$PunteggioP2S1 = $_REQUEST["PunteggioP2S1"];
$PunteggioP2S2 = $_REQUEST["PunteggioP2S2"];
//dati partita 3
$PunteggioP3S1 = $_REQUEST["PunteggioP3S1"];
$PunteggioP3S2 = $_REQUEST["PunteggioP3S2"];

// casino dovuto al fatto che devo ancora decidere come gestire l'assenza
// di un annuncio di riferimento tramite js
if(isset($_POST["annuncio"]))
    $annuncio = $_POST["annuncio"];
else
    $annuncio = NULL;

try{
    /*
     * Solo dopo esserci assicurati che tutti i dati siano disponibili
     *  iniziamo ad allocare risorse
     */
    $conn = ClassConnectionBuilder::buildDefaultConnection();

    $usr = $_SESSION["user"];
    $conn->registerTripleMatch(
            /*int*/ $G1, /*int*/ $G2, /*int*/ $G3, /*int*/ $G4, 
            /*int*/ $PunteggioP1S1, /*int*/ $PunteggioP1S2,
            /*int*/ $PunteggioP2S1, /*int*/ $PunteggioP2S2,
            /*int*/ $PunteggioP3S1, /*int*/ $PunteggioP3S2,
            /*int*/ $annuncio);
    
    echo "Success";
    
} catch (Exception $ex) {
    header($_SERVER["SERVER_PROTOCOL"] . " 500 Internal Server Error");
    echo "Something went bad.";
    exit(1);
}
