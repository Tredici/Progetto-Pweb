<?php
    require_once '../php/ClassConnectionBuilder.php';
    require_once 'Appoggio/CostruzionePaginaPersonale.php';
    
    session_start();
    
    if(!isset($_SESSION["user"]))
    {
        header("Location: ../index.php");
        exit();
    } else {
        try{
            $paginaPersonale = TRUE;
            
            $conn = ClassConnectionBuilder::buildDefaultConnection();
            if(isset($_GET["userid"])) {
                /*
                 * fornisce la pagina personale dell'utente di cui è fornito l'id
                 */
                $code = $_GET["userid"];
                try {
                    $usr = $conn->getUserData($code);
                } catch (Exception $exc) {
                    header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");
                    echo $exc->getTraceAsString();
                    
                    exit(1);
                }
            
            } else if(isset($_GET["username"])) {
                /*
                 * fornisce la pagina personale dell'utente di cui è fornito l'id
                 */
                $username = $_GET["username"];
                try {
                    $code = $conn->getUserCode($username);
                    $usr = $conn->getUserData($code);
                } catch (Exception $exc) {
                    header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");
                    echo $exc->getTraceAsString();
                    
                    exit(1);
                }

            } else {
                $code = $_SESSION["user"];
                $usr = $conn->getUserData($code);
            }
            $paginaPersonale = ($code == $_SESSION["user"]);
            $ppb = $conn->getPersonalPage($usr);
        } catch (Exception $ex) {
            header($_SERVER["SERVER_PROTOCOL"] . " 500 Internal Server Error");
            echo '<br>msg:<br>';
            echo $exc->getMessage();
            echo '<br>stack trace:<br>';
            echo $exc->getTraceAsString();
        }
    }
    
?>
<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="utf-8" />
        <title><?= $ppb->getUsername() ?></title>
        
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        
        <link rel="stylesheet" type="text/css" href="../css/StilePrincipale.css">
        <link rel="stylesheet" type="text/css" href="../css/StilePaginaPersonale.css">
        
        <link rel="icon" href="../logo.bmp"  type="image/bmp">
        
    <?php if($paginaPersonale){ ?>
        <script type="text/javascript" src="../js/CaricaNuovaFoto.js"></script>
        <script type="text/javascript" src="../js/GestionePostPersonali.js"></script>
    <?php } ?>
        
    </head>
    <body>
        <div class="wrapper">
            <header>
            <?php if($paginaPersonale){ ?>
                <h1>La tua P.P. (Pagina Personale)</h1>
            <?php } else { ?>
                <h1>Una P.A. (Pagina Altrui)</h1>
            <?php } ?>
            </header>
            <?php require_once 'inline/Navbar.php'; ?>

            <div class="container">

                <section class="presentazione">
                    <h2>
                        Presentazione:
                    </h2>

                    <table class="presentazione">
                        <tr>
                            <th>Nome:
                            <td><?=htmlentities($ppb->getUser()->getName())?>
                        <tr>
                            <th>Cognome:
                            <td><?=htmlentities($ppb->getUser()->getSurname())?>
                        <tr>
                            <th>Soprannome:
                            <td><?=htmlentities($ppb->getUser()->getUsername())?>
                        <tr>
                            <th>Compleanno:
                            <td><?=htmlentities($ppb->getUser()->getBirthday())?>
                    </table>
                </section>
                <section>
                    <h2>
                        Post personali:
                    </h2>

                    <?php
                        $doc = new DOMDocument;
                        // class container
                        $container = $doc->createElement("div");
                        $doc->appendChild($container);

                        $divNAME = $doc->createAttribute("name");
                        $container->appendChild($divNAME);
                        $divNAME->value = "Fotografia";

                        foreach($ppb->getPosts() as $post)
                        {
                            $divPost = $doc->createElement("div");
                            $container->appendChild($divPost);
                            $postID = $doc->createAttribute("id");
                            $divPost->appendChild($postID);
                            $postID->value = "post" . (string)$post->getCode();

                            $postCODE = $doc->createAttribute("data-code");
                            $divPost->appendChild($postCODE);
                            $postCODE->value = (string)$post->getCode();

                            $postCLASS = $doc->createAttribute("class");
                            $divPost->appendChild($postCLASS);
                            $postCLASS->value = "post";

                            $title = $doc->createElement("h3");
                            $divPost->appendChild($title);
                            $title->appendChild($doc->createTextNode($post->getTitle()));

                            $p = $doc->createElement("p");
                            $divPost->appendChild($p);
                            $p->appendChild($doc->createTextNode($post->getEscapedContent()));

                            if($paginaPersonale) {
                                $button = $doc->createElement("button");
                                $divPost->appendChild($button);
                                $button->appendChild($doc->createTextNode("Rimuovi"));

                                $buttonCLASS = $doc->createAttribute("class");
                                $button->appendChild($buttonCLASS);
                                $buttonCLASS->value = "button cancellazione";

                                $buttonNAME = $doc->createAttribute("name");
                                $button->appendChild($buttonNAME);
                                $buttonNAME->value = "bottoneCancellazione";
                            }
                        }

                        echo $doc->saveHTML();
                        unset($doc);
                    ?>

                <?php if($paginaPersonale)
                    {   // bottone per l'aggiunta di nuovi post  
                ?>
                    <div id="Inserimento">
                        <button id="addPost" class="button">Aggiungi un nuovo post</button>
                    </div>
                <?php } ?>

                </section>
                <?php inserisciFotoUtente(); ?>
            </div>
            <div class="push"></div>
        </div>
        <?php require_once 'inline/Footer.php'; ?>
    </body>
</html>

