<?php

/* 
 * Per cancellare la sottoscrizione agli annunci
 */

require_once '../php/ClassConnectionBuilder.php';

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

try {
    $conn = ClassConnectionBuilder::buildDefaultConnection();
    $conn->startTransaction();
    
    $usr = $_SESSION["user"];
    $annCODE = $_REQUEST["code"];
    $conn->unsubscribeAnnouncement($annCODE, $usr);
    $ann = $conn->getAnnouncement($annCODE);
    $conn->commit();
    
    echo json_encode($ann);
    
} catch (Exception $exc) {
    // 500
    header($_SERVER["SERVER_PROTOCOL"] . " 500 Internal Server Error");
    exit(1);
}

