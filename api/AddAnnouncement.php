<?php

/* 
 * Serve ad aggiungere un nuovo annuncio, fine.
 */

require_once '../php/ClassConnectionBuilder.php';

session_start();

if(!isset($_SESSION["user"]))
{
    header($_SERVER["SERVER_PROTOCOL"] . " 401 Unauthorized");
    echo 'Bisogna essere loggati per eseguire il comando';
    return;
}

if(!isset($_SERVER['REQUEST_METHOD']) or $_SERVER['REQUEST_METHOD'] !== "POST")
{
    header($_SERVER["SERVER_PROTOCOL"] . " 405 Method Not Allowed");
    echo 'Usa POST.';
    return;
}

if(!isset($_REQUEST["Giorno"]) || !isset($_REQUEST["Orario"])
    || !isset($_REQUEST["TipoPartita"]))
{
    header($_SERVER["SERVER_PROTOCOL"] . " 400 Bad Request");
    echo "Missing arguments.";
    exit;
}

try {
    $conn = ClassConnectionBuilder::buildDefaultConnection();
} catch (Exception $exc) {
    header($_SERVER["SERVER_PROTOCOL"] . " 500 Internal Server Error");
    echo "DB unreachable.";
    exit();
}

try {
    $conn->startTransaction();
    // id dell'utente che prova l'upload
    $usr = $_SESSION["user"];
    $date = $_REQUEST["Giorno"];
    $time = $_REQUEST["Orario"];
    $matchType = $_REQUEST["TipoPartita"];
    $notes = isset($_REQUEST["Notes"]) ? $_REQUEST["Notes"] : NULL;
    $annCODE = $conn->createAnnouncement($usr, $matchType, $date, $time, $notes);
    $ann = $conn->getAnnouncement($annCODE);
    $conn->commit();
    
    echo json_encode($ann);
} catch (Exception $exc) {
    /*
     * Forse sarà il caso di aggiungere un "500 Server Error" un po' 
     * dappertutto più avanti
     */
    header($_SERVER["SERVER_PROTOCOL"] . " 500 Internal Server Error");
    $conn->rollback();
}
