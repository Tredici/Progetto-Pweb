<?php

/*
 * Il senso di questa pagina è semplicemente quello di caricare tramite una
 *  POST request un nuovo post nella pagina personale dell'utente proprietario.
 * 
 * 
 * La richiesta dovrà essere accompagnata da esattamente 2 parametri:
 *  Titolo varchar(128) 
 *      - titolo del post, di al più 128 caratteri
 *          (La lunghezza sarà gestita solo più avanti)
 *  Testo  text
 *      - il testo effettivo del messaggio
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

if(!isset($_POST["reqID"]) || !isset($_POST["choice"]))
{
    header($_SERVER["SERVER_PROTOCOL"] . " 400 Bad Request");
    echo "Missing arguments.";
    exit;
}

$reqID = (int) $_POST["reqID"];
$choice  = $_POST["choice"];

/*if($choice != "approved" && $choice != "rejected")
{
    header($_SERVER["SERVER_PROTOCOL"] . " 400 Bad Request");
    echo "Invalid arguments.";
    exit;
}*/


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

    if($usr->getPriviledge() > InterfaceConnection::privAdmin)
    {
        header($_SERVER["SERVER_PROTOCOL"] . " 401 Unauthorized");
        echo "Must be admin.";
        exit;
    }
   
    
    switch ($choice) {
        case "approved":
            $conn->acceptSubscriptionRequests($reqID);
            break;
        
        case "rejected":
            $conn->rejectSubscriptionRequests($reqID);
            break;

        default:
            header($_SERVER["SERVER_PROTOCOL"] . " 400 Bad Request");
            echo "Invalid arguments.";
            exit;
            break;
    }
    
    
} catch (Exception $ex) {
    header($_SERVER["SERVER_PROTOCOL"] . " 500 Internal Server Error");
    echo "Something went bad.";
    exit;
}
