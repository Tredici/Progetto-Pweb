<?php

/*
 * Permette a un utente di cancellare le note associate a un annuncio
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
if(!isset($_REQUEST["annuncio"]))
{
    header($_SERVER["SERVER_PROTOCOL"] . " 400 Bad Request");
    echo "Missing arguments.";
    exit;
}

$annuncio = $_REQUEST["annuncio"];

if(!is_numeric($annuncio))
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
    
    $conn->deleteAnnouncementNotes($annuncio);
    
    echo "Success";
    
} catch (Exception $ex) {
    header($_SERVER["SERVER_PROTOCOL"] . " 500 Internal Server Error");
    echo "Something went bad.";
    exit(1);
}