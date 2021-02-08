<?php
    session_start();
    session_destroy(); // dovrebbe essere sufficiente a eliminare completamente la sessione
    
    header("Location: ../index.php");
?>