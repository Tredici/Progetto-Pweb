<?php

/* 
 * Per sottoscrivere gli annunci da parte del chiamante
 * 
 * Ritorna un oggetto JSON rappresentante l'annuncio sottoscritto
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

try {
    $conn = ClassConnectionBuilder::buildDefaultConnection();
    $conn->startTransaction();
    
    // id dell'utente che prova l'upload
    $usr = $_SESSION["user"];
    
    $annCODE = $_REQUEST["code"];
    
    $conn->subscribeAnnouncement($annCODE, $usr);
    $ann = $conn->getAnnouncement($annCODE);
    $conn->commit();
    
    echo json_encode($ann);
    
} catch (Exception $exc) {
    // server error 500
    header($_SERVER["SERVER_PROTOCOL"] . " 500 Internal Server Error");
    exit(1);
}

