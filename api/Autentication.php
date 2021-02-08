<?php
    // Modulo per l'autentiìcazione dell'utente

require_once '../php/ClassConnectionBuilder.php';

session_start();

if(!isset($_REQUEST["username"]) || !isset($_REQUEST["password"]))
{
    header($_SERVER["SERVER_PROTOCOL"] . " 400 Bad Request");
    echo 'Missing Arguments.';
    exit();
}

try{
    $usr  = $_REQUEST["username"];
    $pass = $_REQUEST["password"];

    // Forse impedisce che venga richiesto di inviare i dati nuovamente
    unset($_REQUEST["username"]);
    unset($_REQUEST["password"]);

    $conn = ClassConnectionBuilder::buildDefaultConnection();
    
    try{
        // se le credenziali sono invalide fallisce qui
        $code = $conn->AutenticateUser($usr, $pass, 'username');
        $_SESSION["user"] = $code; //Login avvenuto con successo
        
        
        echo json_encode(["userid" => $code]);
    } catch (Exception $ex) {
        header($_SERVER["SERVER_PROTOCOL"] . " 403 Unauthorized");
        switch ($conn->checkRequestState($usr, $pass, 'username')) {
            // ENUM('in attesa', 'accettata', 'respinta')
            case "in attesa":
                echo "La tua richiesta non è ancora stata valutata, riprova più tardi!";
                break;
            
            case "respinta":
                echo "La tua richiesta è stata respinta, non potrai accedere al sito!";
                break;
            
            default:
                echo "Credenziali invalide!";
                break;
        }
        
    }
    
} catch (Exception $ex) {
    header($_SERVER["SERVER_PROTOCOL"] . " 500 Internal Server Error");
    echo "Something went bad.";
}

?>