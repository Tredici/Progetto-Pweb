<?php

/* 
 * Per scaricare le pagine dell'utente il cui id viene passato
 */

require_once '../php/ClassMySqlConnection.php';
require_once '../php/ClassConnectionBuilder.php';
require_once '../php/ClassRegisteredUser.php';
require_once '../php/ClassSubscriptionReqeuest.php';

session_start();

if(!isset($_SESSION["user"]))
{
    header($_SERVER["SERVER_PROTOCOL"] . " 401 Unauthorized");
    echo 'Bisogna essere loggati per eseguire il comando';
    return;
}


try {
    $conn = ClassConnectionBuilder::buildDefaultConnection();
    
    if(isset($_GET["userid"])) {
        $usr = $_GET["userid"];
    } else {
        // id dell'utente che prova a eseguire
        $usr = $_SESSION["user"];
    }
    $pic = $conn->getPersonalPicture($usr);
    
    if(!$pic) {
        header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");
        echo 'Missing argument';
        return;
    }
    
    header("Content-Type: " . $pic->getContentType());
    
    echo $pic->getContent();
    
} catch (Exception $exc) {
    header($_SERVER["SERVER_PROTOCOL"] . " 500 Internal Server Error");
    exit(1);
}

