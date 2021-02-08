<?php
    session_start();
    if(!isset($_SESSION["user"])) {
        header("Location: ../index.php");
        exit;
    }
    
    require_once '../php/ClassConnectionBuilder.php';
    require_once '../php/Utility.php';
    
    try{
        $conn = ClassConnectionBuilder::buildDefaultConnection();
        $ranking = $conn->getRanking();
    } catch (Exception $ex) {
        header($_SERVER["SERVER_PROTOCOL"] . " 500 Internal Server Error");
        header("Location: Error.php");
        echo "Something went bad.";
        exit;
    }
?>
<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <title>Classifica</title>
        
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        
        <link rel="stylesheet" type="text/css" href="../css/StileClassifica.css">
        <link rel="stylesheet" type="text/css" href="../css/StilePrincipale.css">
        
        <link rel="icon" href="../logo.bmp"  type="image/bmp">
        
        <script type="text/javascript" src="../js/GestioneClassifica.js"></script>
        <script type="text/javascript" src="../js/OrdinatoreTabella.js"></script>
    </head>
    <body>
        <div class="wrapper">
            <header>
                <h1>Classifica</h1>
            </header>
            <?php require_once 'inline/Navbar.php'; ?>

            <div class="container">

                <div class="descrizione">
                    <h3>Descrizione</h3>
                    <p>
                        Segue la classifica attuale. La classifica è aggiornata ogni
                        volta che viene registrata una nuova partita.
                    </p>
                </div>

                <div class="info">
                    <h3>Assegnazione del punteggio</h3>
                    <p>
                        Al momento il punteggio è dato dalla curiosa formula 
                        <span>Punteggio = Vittorie^2/Partite giocate</span>
                        Non ho idea di quanto funzioni bene ma mi è parsa un buon 
                        primo compromesso per tenere conto sia dell'esperienza sia
                        del peso delle sconfitte. Non appena capiremo che funziona 
                        troppo male la cambieremo.
                    </p>
                </div>

                </p>
                <?php
                    $doc = new DOMDocument;
                    $table = $doc->createElement("table");
                    $doc->appendChild($table);

                    $tCaption = $doc->createElement("caption");
                    $table->appendChild($tCaption);
                    $tCaption->textContent = "La classifica dei giocatori";

                    $tableName = $doc->createAttribute("id");
                    $table->appendChild($tableName);
                    $tableName->value = "Classifica";

                    $thead = $doc->createElement("thead");
                    $table->appendChild($thead);
                    $tbody = $doc->createElement("tbody");
                    $table->appendChild($tbody);

                    // Preparazione Head
                    $tr = $doc->createElement("tr");
                    $thead->appendChild($tr);

                    appendTH($doc, $tr, "Posizione", ["type" => "numeric"]);
                    appendTH($doc, $tr, "Username", ["type" => "string"]);
                    appendTH($doc, $tr, "Vittorie", ["type" => "numeric"]);
                    appendTH($doc, $tr, "Sconfitte", ["type" => "numeric"]);

                    appendTH($doc, $tr, "Umiliazioni Inflitte", ["type" => "numeric"]);
                    appendTH($doc, $tr, "Umiliazioni Subite", ["type" => "numeric"]);

                    appendTH($doc, $tr, "Percentuale Vittorie", ["type" => "numeric"]);
                    appendTH($doc, $tr, "Punteggio", ["type" => "numeric"]);

                    $i = 0;

                    // Preparazione tBody
                    foreach($ranking as $el)
                    {
                        $tr = $doc->createElement("tr");
                        $tbody->appendChild($tr);

                        appendTD($doc, $tr, $el->getRank(), ["val" => $el->getRank()]);
                        appendTDlink($doc, $tr, $el->getUsername(),
                                "PaginaPersonale.php?username=" . urlencode($el->getUsername()),
                                ["val" => $el->getUsername()]
                                );

                        appendTD($doc, $tr, $el->getVictories(), ["val" => $el->getVictories()]);
                        appendTD($doc, $tr, $el->getDefeats(), ["val" => $el->getDefeats()]);

                        appendTD($doc, $tr, $el->getInflictedHumiliations(), ["val" => $el->getInflictedHumiliations()]);
                        appendTD($doc, $tr, $el->getSufferedHumiliations(), ["val" => $el->getSufferedHumiliations()]);


                        $percentualeVittorie = $el->getVictories()+$el->getDefeats()!=0 ? 
                                        $el->getVictories()/($el->getVictories()+$el->getDefeats()) 
                                        : 0;
                        appendTD($doc, $tr, substr((string)($percentualeVittorie*100), 0, 5), ["val" => $percentualeVittorie]);
                        appendTD($doc, $tr, $el->getScore()?: "N.D.", ["val" => $el->getScore()?: "N.D."]);

                        ++$i;
                    }

                    echo $doc->saveHTML();
                ?>
            </div>
            <div class="push"></div>
        </div>
        
        <?php require_once 'inline/Footer.php'; ?>
    </body>
</html>
