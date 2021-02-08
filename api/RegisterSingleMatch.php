<?php

/* 
 * Serve a registrare l'esito in un incontro singolo
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
if(!isset($_POST["S1G1"]) || !isset($_POST["S1G2"]) || 
    !isset($_POST["S2G1"]) || !isset($_POST["S2G2"]) || 
    !isset($_POST["PunteggioS1"]) || !isset($_POST["PunteggioS2"])
    )
{
    header($_SERVER["SERVER_PROTOCOL"] . " 400 Bad Request");
    echo "Missing arguments.";
    exit;
}

$S1G1 = $_POST["S1G1"];
$S1G2 = $_POST["S1G2"];
$S2G1 = $_POST["S2G1"];
$S2G2 = $_POST["S2G2"];
$PunteggioS1 = $_POST["PunteggioS1"];
$PunteggioS2 = $_POST["PunteggioS2"];

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
    $conn->registerSingleMatch(
            /*int*/ $S1G1, /*int*/ $S1G2, /*int*/ $S2G1, /*int*/ $S2G2, 
            /*int*/ $PunteggioS1, /*int*/ $PunteggioS2, 
            /*int*/ $annuncio);
    
    echo "Success";
    
} catch (Exception $ex) {
    header($_SERVER["SERVER_PROTOCOL"] . " 500 Internal Server Error");
    echo "Something went bad.";
    exit(1);
}