<?php
// Inizia una sessione
session_start();

//Vede se l'utente è loggato e a seconda di ciò decide quale pagina restituire
if(isset($_SESSION["user"])) {
    header("Location: Pagine/Home.php");
} else {
    header("Location: Pagine/Login.php");
}

exit();
?>
