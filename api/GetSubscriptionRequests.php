<?php

/*
 * Fornisce a un utente autorizzato l'elenco completo delle richieste di 
 *  iscrizione ricevute gestite o ancora da gestire.
 */

/*
 * Di fatto opera in maniera simile a UploadPersonalPosts.php
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

// controlla che il protocollo utilizzato sia GET
if(!isset($_SERVER['REQUEST_METHOD']) or $_SERVER['REQUEST_METHOD'] !== 'GET')
{
    header($_SERVER["SERVER_PROTOCOL"] . " 501 Not Implemented");
    echo "Invalid request.";
    exit;
}

try {
    $conn = ClassConnectionBuilder::buildDefaultConnection();    
    
    // controlla se le richieste sono effettuate da un admin
    $user = $_SESSION["user"];
    $usr = $conn->getUserData($user);
    if($usr->getPriviledge() > $conn::privAdmin)
    {
        header($_SERVER["SERVER_PROTOCOL"] . " 401 Unauthorized");
        echo "Must be admin.";
        exit;
    }
    
    $reqs = $conn->getSubscriptionRequests();
    header("Content-Type: application/JSON; charset=UTF-8");
    
    echo json_encode($reqs);
    
} catch (Exception $exc) {
    header($_SERVER["SERVER_PROTOCOL"] . " 500 Internal Server Error");
    exit(1);
}

