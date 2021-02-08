<?php

/* 
 * Verifica che il nome utente fornito esista e fornisce un oggetto contenente
 *  username e codice utente
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
if(!isset($_REQUEST["username"]))
{
    header($_SERVER["SERVER_PROTOCOL"] . " 400 Bad Request");
    echo "Missing arguments.";
    exit;
}

$username = $_REQUEST["username"];

try{
    /*
     * Solo dopo esserci assicurati che tutti i dati siano disponibili
     *  iniziamo ad allocare risorse
     */
    $conn = ClassConnectionBuilder::buildDefaultConnection();

    try {
        $userCode = $conn->getUserCode($username);
        $risposta = [
            "username"  => $username,
            "userid"    => $userCode
        ];
        $json = json_encode($risposta);
        echo $json;
    } catch (Exception $ex) {
        header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");
        echo "Username not found.";
        exit;
    }
    
} catch (Exception $ex) {
    header($_SERVER["SERVER_PROTOCOL"] . " 500 Internal Server Error");
    echo "Something went bad.";
    exit(1);
}