<?php

/* 
 * Serve a caricare immagini sul db.
 * 
 * Le immagini sono associate a un utente e occasionalmente 
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

if(!isset($_SERVER['REQUEST_METHOD']) or $_SERVER['REQUEST_METHOD'] !== "POST")
{
    header($_SERVER["SERVER_PROTOCOL"] . " 405 Method Not Allowed");
    echo 'Usa POST.';
    return;
}

if(!isset($_FILES["picture"]))
{
    header($_SERVER["SERVER_PROTOCOL"] . " 400 Bad Request");
    echo "È difficile carica un'immagine senza caricare un'immagine.";
    return;
}

try {
    $conn = ClassConnectionBuilder::buildDefaultConnection();
    
    // id dell'utente che prova l'upload
    $usr = $_SESSION["user"];
    
    $personal = FALSE;
    if(isset($_POST["personal"]))
    {
        $personal = $_POST["personal"] == "true";
    }
    
    $filename = $_FILES["picture"]["name"];
    $mime = $_FILES["picture"]["type"];
    $temp_filename = $_FILES['picture']['tmp_name'];
    $content = file_get_contents($temp_filename);
    
    // Se non lancia è andato bene
    $conn->uploadPicture($usr, $mime, $content, $personal, $filename);
    
    
    echo "Uploaded!";
    
} catch (Exception $exc) {
    // 500
    header($_SERVER["SERVER_PROTOCOL"] . " 500 Internal Server Error");
    exit(1);
}