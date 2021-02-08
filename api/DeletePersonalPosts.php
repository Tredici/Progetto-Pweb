<?php

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

if(!isset($_SERVER['REQUEST_METHOD']) or $_SERVER['REQUEST_METHOD'] !== 'POST')
{
    header($_SERVER["SERVER_PROTOCOL"] . " 501 Not Implemented");
    echo "Invalid request.";
    exit;
}

if(!isset($_POST['code']))
{
    header($_SERVER["SERVER_PROTOCOL"] . " 404 Bad Request");
    echo "Missing arguments.";
    exit;
}

$postCODE = $_POST['code'];

try{
    /*
     * Solo dopo esserci assicurati che tutti i dati siano disponibili
     *  iniziamo ad allocare risorse
     */
    $conn = ClassConnectionBuilder::buildDefaultConnection();
    $conn->startTransaction();
    
    // intero identificativo dell'utente
    $user = $_SESSION["user"];

    // array di post personali
    $post = $conn->getPost($postCODE);
    if($post->getAuthor() != $user)
    {
        header($_SERVER["SERVER_PROTOCOL"] . " 401 Unauthorized");
        echo "Must be author.";
        exit;
    }
    $conn->deletePost($postCODE);
    $conn->commit();
    
    //header("Content-type: application/json; charset=UTF-8");
    echo json_encode($post);
} catch (Exception $ex) {
    $conn->rollback();
    
    header($_SERVER["SERVER_PROTOCOL"] . " 500 Internal Server Error");
    echo "Something went bad.";
    exit;
}
