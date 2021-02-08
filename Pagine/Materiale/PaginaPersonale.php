<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="utf-8" />
        <title><?php $this->utente->getName() ?></title>
        
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        
        <!-- meta http-equiv="Content-Type" content="text/html; charset=utf-8" / -->
        
        <link rel="stylesheet" type="text/css" href="/CoseBuffe/css/StilePrincipale.css">
        <link rel="stylesheet" type="text/css" href="/CoseBuffe/css/StilePaginaPrincipale.css">
        
        <script type="text/javascript" src="/CoseBuffe/js/CaricaNuovaFoto.js"></script>
        <script type="text/javascript" src="/CoseBuffe/js/GestionePostPersonali.js"></script>
        
    </head>
    <body>
        <header>
            <h1>La tua P.P. (Pagina Personale)</h1>
            <div>
                <?php require_once 'inline/Navbar.php'; ?>
            </div>
        </header>
        
        <div class="container">
        
            <main class="PaginaPersonale">

                <!-- Questo diventerà lo spazio dove figureranno le informazioni generali dell'utente -->

                <!--<div> style="text-align: left; " - ->
                    <!--<div>
                        <figure> <!-- id="Fotografia" - ->
                            <!--id="FotoProfilo"- ->
                            <img class="profilo" src="/CoseBuffe/img/TipaBuffa.jpg" alt="Una ragazza buffa" style="max-height: 5cm; max-width: 4cm;">
                        </figure>
                    </div>
                    <section>
                        <h2>
                            Giulia
                        </h2>
                    </section>
                </div>-->


                <div>
                    <section>
                        <h2>
                            <?php echo $this->getUserName(); ?>
                        </h2>
                    </section>
                </div>
                <div <!-- style="display: block;" -->

                    <?php
                        $doc = new DOMDocument;
                        // class container
                        $container = $doc->createElement("div");
                        $doc->appendChild($container);
                        
                        
                        foreach($this->posts as $post)
                        {
                            //$N = $post->getCode();
                            //$T = $post->getTitle();
                            //$P = $post->getEscapedContent();
                            //echo(mb_detect_encoding($T) . "<br>");
                            //echo(mb_detect_encoding($P) . "<br>");
                            
                            $divPost = $doc->createElement("div");
                            $container->appendChild($divPost);
                            $postID = $doc->createAttribute("id");
                            $divPost->appendChild($postID);
                            $postID->value = "post" . (string)$post->getCode();
                            
                            $title = $doc->createElement("h3");
                            $divPost->appendChild($title);
                            $title->textContent = $post->getTitle();
                            
                            $p = $doc->createElement("p");
                            $divPost->appendChild($p);
                            $p->textContent = $post->getEscapedContent();
                            
                            continue;
                            
                            echo('<div id=post'. $N . '>');
                            echo('<h3>' . $T . '</h3>');
                            echo('<p>' . $P . '</p>');
                            echo '</div>';
                        }
                        
                        echo $doc->saveHTML();
                        
                    ?>

                    <!-- bottone per l'aggiunta di nuovi post -->
                    <div id="Inserimento">
                        <button id="addPost">Aggiungi un nuovo post</button>
                    </div>

                </div>
            </main>

            <?php if(TRUE)  // Più avanti bisognerà aggiungere il controllo per verificare l'esistnza del file
                { ?>
                <figure id="Fotografia">
                    <img class="profilo" id="FotoProfilo" src="/CoseBuffe/api/Me.php" alt="La tua foto profilo.">
                    <figcaption>Tu.</figcaption>
                </figure>
           <?php } ?>
        
        </div>
        
    </body>
</html>

