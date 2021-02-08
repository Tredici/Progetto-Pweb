<?php

/*
 * Fornisce le informazioni in classifica sul'utente in formato JSON.
 *  L'utente può essere il "chiamante" o quello di cui viene fornito l'id.
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
    $conn = ClassConnectionBuilder::buildDefaultConnection();
    $rank = $conn->getUserRank($usr);
    
    echo json_encode($rank);
} catch (Exception $ex) {
    header($_SERVER["SERVER_PROTOCOL"] . " 500 Internal Server Error");
    echo "Something went bad.";
    exit(1);
}