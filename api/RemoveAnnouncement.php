<?php

/* 
 * Serve ad aggiungere un nuovo annuncio, fine.
 * 
 * Ritorna un oggetto JSON rappresentante l'annuncio appena rimosso.
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
    
    try{
        $ann = $conn->getAnnouncement($annCODE);
        
        if($ann->getOwnerCode() != $usr)
        {
            header($_SERVER["SERVER_PROTOCOL"] . " 401 Unauthorized");
            echo 'Solo l\'autore può rimuovere un annuncio.';
            return;
        }
        
    } catch (Exception $ex) {
        header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");
        echo 'Annuncio inesistente.';
        exit();
    }
    
    $conn->deleteAnnouncement($annCODE);
    $conn->commit();
    echo json_encode($ann);
    
} catch (Exception $exc) {
    // È andata male
    header($_SERVER["SERVER_PROTOCOL"] . " 500 Internal Server Error");
    exit(1);
}