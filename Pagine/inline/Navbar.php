<?php /*session_start();*/ 
function navBar() {
?>
<nav class="navbar"> 
    <ul>
    <?php if(!isset($_SESSION["user"])) { ?>
        <li>
            <a href="Login.php">Login</a>
        </li>
        <li>
            <a href="SubscribeRequest.php">Iscriviti</a>
        </li>
    <?php } else { ?>
        <li> <a href="Home.php">Home</a> </li>
        <?php
            require_once '../php/ClassMySqlConnection.php';
            require_once '../php/ClassConnectionBuilder.php';
            require_once '../php/ClassRegisteredUser.php';

            $user = $_SESSION["user"];
            $conn = ClassConnectionBuilder::buildDefaultConnection();
            $usr = $conn->getUserData($user);
            // solo gli amministratori possono gestire le richieste di login
            if($conn::privAdmin >= $usr->getPriviledge()) { 
        ?>
            <li> <a href="PaginaGestioneRichieste.php">Gestione Richieste</a> </li>
        <?php } ?>
        <li> <a href="Bacheca.php">Bacheca</a> </li>
        <li> <a href="PartiteGiocate.php">Le tue partite</a> </li>
        <li> <a href="Classifica.php">Classifica</a> </li>
        <li> <a href="PaginaPersonale.php">Pagina Personale</a> </li>
        <li> <a href="../php/Logout.php">Logout</a> </li>
    <?php } ?>
    </ul>
</nav>
<?php } 
navBar();
?>