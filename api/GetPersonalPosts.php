<?php
ob_start();
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once '../php/ClassMySqlConnection.php';
require_once '../php/ClassConnectionBuilder.php';
require_once '../php/ClassRegisteredUser.php';


session_start();

// controlla se la richiesta fa capo a un utente attualmente connesso
if(!isset($_SESSION["user"]))
{
    header($_SERVER["SERVER_PROTOCOL"] . " 401 Unauthorized");
    echo "Can't execute.";
    exit;
}

if(!isset($_SERVER['REQUEST_METHOD']) or $_SERVER['REQUEST_METHOD'] !== 'GET')
{
    header($_SERVER["SERVER_PROTOCOL"] . " 501 Not Implemented");
    echo "Invalid request.";
    exit;
}

try{
    /*
    * Solo dopo esserci assicurati che tutti i dati siano disponibili
    *  iniziamo ad allocare risorse
    */
   $conn = ClassConnectionBuilder::buildDefaultConnection();

   // intero identificativo dell'utente
   $user = $_SESSION["user"];

    // classe ClassRegisteredUser
    $usr = $conn->getUserData($user);
    // classe ClassPersonalPageBuilder
    $ppb = $conn->getPersonalPage($usr);

    // array di post personali
    $posts = $conn->getPersonalPosts($ppb->getCode());
    
    $json = json_encode($posts);
    
    if($json === FALSE)
    {
        throw new RuntimeException("Can't serialize!");
    }
    ob_end_clean();
    header($_SERVER["SERVER_PROTOCOL"] . " 200 OK");
    
    //header("Content-type: application/json; charset=UTF-8");
    header("Content-type: application/json");
    echo $json;
    
} catch (Exception $ex) {
    header($_SERVER["SERVER_PROTOCOL"] . " 500 Internal Server Error");
    echo "Something went bad.";
    exit;
}
