<?php
/*
 * Permette di controllare che gli username di 4 utenti 
 * 
 * 
 * La richiesta dovrà essere accompagnata da esattamente 2 parametri:
 *  Titolo varchar(128) 
 *      - titolo del post, di al più 128 caratteri
 *          (La lunghezza sarà gestita solo più avanti)
 *  Testo  text
 *      - il testo effettivo del messaggio
 */

require_once '../php/ClassConnectionBuilder.php';

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
    || !isset($_REQUEST["G3"]) || !isset($_REQUEST["G4"]))
{
    header($_SERVER["SERVER_PROTOCOL"] . " 400 Bad Request");
    echo "Missing arguments.";
    exit;
}

$G1 = $_REQUEST["G1"];
$G2 = $_REQUEST["G2"];
$G3 = $_REQUEST["G3"];
$G4 = $_REQUEST["G4"];

try{
    /*
     * Solo dopo esserci assicurati che tutti i dati siano disponibili
     *  iniziamo ad allocare risorse
     */
    $conn = ClassConnectionBuilder::buildDefaultConnection();

    $players = $conn->checkPlayers($G1, $G2, $G3, $G4);

    if(sizeof($players) !== 4)
    {
        $presenti = array_keys($players);
        $risposta_errore["trovati"] = sizeof($players);
        $risposta_errore["presenti"] = $players;

        $missing = [];

        if(!in_array($G1, $presenti))    $missing[] = $G1;
        if(!in_array($G2, $presenti))    $missing[] = $G2;
        if(!in_array($G3, $presenti))    $missing[] = $G3;
        if(!in_array($G4, $presenti))    $missing[] = $G4;

        $risposta_errore["assenti"] = $missing;
        $json = json_encode($risposta_errore);

        //header("Content-type: application/json");
        echo $json;
        exit(0);
    }
    
    $risposta_successo = [
        "trovati"   => 4,
        "presenti"  => $players
    ];
    $json = json_encode($risposta_successo);
    
    //header("Content-type: application/json");
    echo $json;
    
} catch (Exception $ex) {
    header($_SERVER["SERVER_PROTOCOL"] . " 500 Internal Server Error");
    echo "Something went bad.";
    exit(1);
}