<?php

/* 
 * Restituisce la foto profilo dell'utente in questione.
 * In alternativa fornisce una foto a caso.
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
    
    if(isset($_REQUEST["userid"])) {
        $usr = $_REQUEST["userid"];
    } else {
        $usr = $_SESSION["user"];
    }
    
    $pic = $conn->getPersonalPicture($usr);
    
    if(!$pic) {
        throw new Exception("Foto assente.");
    }
    
    header("Content-Type: " . $pic->getContentType());
    
    echo $pic->getContent();
    
} catch (Exception $exc) {
    $filename = "../img/missing.PNG";
    
    if(!file_exists($filename))
    {
        header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");
        return;
    }
    
    header("Content-Type: image/png");
    readfile($filename);
}

