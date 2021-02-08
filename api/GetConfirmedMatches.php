<?php

/*
 * Fornisce le partite in formato JSON.
 *  Le partite possono essere quelle del "chiamante" o quelle
 *  dell'utente cui è fornito l'id.
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

$usr = $_SESSION["user"];

// stavolta permetto di ogni tipo di richiesta
if(isset($_REQUEST["userid"]))
{
    $usr = $_REQUEST["userid"];
    
    if(!is_numeric($usr))
    {
        header($_SERVER["SERVER_PROTOCOL"] . " 400 Bad Request");
        echo "Invalid arguments.";
        exit;
    }
}

try{
    /*
     * Solo dopo esserci assicurati che tutti i dati siano disponibili
     *  iniziamo ad allocare risorse
     */
    $conn = ClassConnectionBuilder::buildDefaultConnection();

    $matches = $conn->getConfirmedMatches($usr);
    
    echo json_encode($matches);
    
} catch (Exception $ex) {
    header($_SERVER["SERVER_PROTOCOL"] . " 500 Internal Server Error");
    echo "Something went bad.";
    exit(1);
}