Lavori in corso...

<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */



require_once '../php/ClassMySqlConnection.php';
require_once '../php/ClassConnectionBuilder.php';

session_start();

/*
 * Bisognerà aggiungere un controllo per assicurarsi che la
 * richiesta non sia stata inoltrata da un utente già registrato
 */
if(isset($_SESSION["user"]))
{
    header($_SERVER["SERVER_PROTOCOL"] . " 400 Bad Request");
    echo "Invalid arguments.";
    exit;
}


/*
 * Pagina predisposta al controllo e alla gestione delle richieste di iscrizione
 */

// Ccntrolla che l'accesso sia avvenuto a modo
if(!isset($_SERVER['REQUEST_METHOD']) or $_SERVER['REQUEST_METHOD'] !== 'POST')
{
    header($_SERVER["SERVER_PROTOCOL"] . " 501 Not Implemented");
    echo "Invalid request.";
    exit;
}

// Controlla che siano stati ricevuti tutti i campi
if(!isset($_POST["name"]) || !isset($_POST["surname"])
        || !isset($_POST["username"]) || !isset($_POST["email"])
        || !isset($_POST["password"]) || !isset($_POST["birthday"])
        || !isset($_POST["sex"]))
{
    header($_SERVER["SERVER_PROTOCOL"] . " 400 Bad Request");
    echo "Invalid arguments.";
    exit;
}

$name = $_POST["name"];
$surname = $_POST["surname"];
$username = $_POST["username"];
$email = $_POST["email"];
$password = $_POST["password"];
$birthday = $_POST["birthday"];
$sex = $_POST["sex"];


$conn = ClassConnectionBuilder::buildDefaultConnection();


// Controlla che la struttura dei campi sia quella giusta