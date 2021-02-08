<?php

/* 
 * Fornisce in formato JSON l'elenco degli annunci con i quali l'utente può 
 *  interagire, ovvero:
 *      -i propri
 *      -quelli sottoscritti da sé
 *      -quelli altrui che potrebbe voler registrare per dare una mano
 * 
 *  sostanzialmente tutti quelli non scaduti e non da confermare
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


try{
    $conn = ClassConnectionBuilder::buildDefaultConnection();
    $announcements = $conn->getAvaibleAnnouncements($usr);
    
    echo json_encode($announcements);
} catch (Exception $ex) {
    header($_SERVER["SERVER_PROTOCOL"] . " 500 Internal Server Error");
    echo "Something went bad.";
    exit(1);
}