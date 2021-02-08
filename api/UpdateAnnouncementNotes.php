<?php

/*
 * Permette a un utente di aggiornare il campo note associato a un annuncio
 *  da lui emesso.
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
if(!isset($_POST["annuncio"]) || !isset($_POST["testo"]))
{
    header($_SERVER["SERVER_PROTOCOL"] . " 400 Bad Request");
    echo "Missing arguments.";
    exit;
}

$annuncio = $_POST["annuncio"];
$nuovo_testo = $_POST["testo"];

if(!is_numeric($annuncio) or !is_string($nuovo_testo) or sizeof($nuovo_testo) === 0)
{
    header($_SERVER["SERVER_PROTOCOL"] . " 400 Bad Request");
    echo "Invalid arguments.";
    exit;
}

try{
    /*
     * Solo dopo esserci assicurati che tutti i dati siano disponibili
     *  iniziamo ad allocare risorse
     */
    $conn = ClassConnectionBuilder::buildDefaultConnection();

    $usr = $_SESSION["user"];
    $ann = $conn->getAnnouncement($annuncio);
    if($ann->getOwnerCode() != $usr)
    {
        header($_SERVER["SERVER_PROTOCOL"] . " 403 Forbidden");
        echo "Can't execute.";
        exit;
    }
    
    $conn->updateAnnouncementNotes($annuncio, $nuovo_testo);
    
    $ann = $conn->getAnnouncement($annuncio);
    echo $ann->getNotes();
    
} catch (Exception $ex) {
    header($_SERVER["SERVER_PROTOCOL"] . " 500 Internal Server Error");
    echo "Something went bad.";
    exit(1);
}