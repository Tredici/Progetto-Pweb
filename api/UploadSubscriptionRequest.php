<?php

/* 
 * Questa pagina serve unicamente a getire le richieste di
 *  iscrizione al sito.
 */

require_once '../php/ClassMySqlConnection.php';
require_once '../php/ClassConnectionBuilder.php';
require_once '../php/ClassSubscriptionReqeuest.php';

session_start();

// un utente non può reiscriversi
if(isset($_SESSION["user"]))
{
    header($_SERVER["SERVER_PROTOCOL"] . " 400 Bad Request");
    echo "Can't submit.";
    exit;
}

// Controlla che il metodo sia post
if(!isset($_SERVER['REQUEST_METHOD']) or $_SERVER['REQUEST_METHOD'] !== 'POST')
{
    header($_SERVER["SERVER_PROTOCOL"] . " 501 Not Implemented");
    echo "Invalid request.";
    exit;
}

// controlla che i parametri siano quelli
if(!isset($_POST["name"]) || !isset($_POST["surname"]) ||
    !isset($_POST["username"]) || !isset($_POST["email"]) ||
    !isset($_POST["password"]) || !isset($_POST["birthday"]) ||
    !isset($_POST["sex"]))
{
    header($_SERVER["SERVER_PROTOCOL"] . " 400 Bad Request");
    echo "title and text needed.";
    exit;
}

// Ricava i parametri per la richiesta di iscrizione
$name = $_POST["name"];
$surname = $_POST["surname"];
$username = $_POST["username"];
$email = $_POST["email"];
$password = $_POST["password"];
$birthday = $_POST["birthday"];
$sex = $_POST["sex"];

if(!preg_match("/\d{4}-\d{2}-\d{2}/", $birthday))
{
    header($_SERVER["SERVER_PROTOCOL"] . " 400 Bad Request");
    echo "Invalid birthday.";
    exit;
}

if($sex != "M" and $sex != "F")
{
    header($_SERVER["SERVER_PROTOCOL"] . " 400 Bad Request");
    echo "Unknown sex.";
    exit;
}

if(isset($_POST["note"]))
    $note = $_POST["note"];

try{
    /*
    * Inizia la preparazione a fare la richiesta.
    */
   $conn = ClassConnectionBuilder::buildDefaultConnection();

   // Per ora si trascurano le note
   $sbrq = new ClassSubscriptionReqeuest($name, $surname, 
           $username, $email, $birthday, $sex, $note);
   
    // Carica la password
    $sbrq->setPassword($password);
    
    // Il momento della verità
    $conn->uploadSubscriptionRequest($sbrq);
    
   
   echo 'Request successfully uploaded.';
    
} catch (Exception $ex) {
    header($_SERVER["SERVER_PROTOCOL"] . " 500 Internal Server Error");
    echo "Something went bad.";
    exit;
}


