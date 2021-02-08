<?php 
/*
 * Qui metterò tante funzioni per Riconoscere la situazione 
 * da gestire ed, eventualmente gestirla.
 * 
 * Ciascuna di queste funzioni dovrà restituire false se non riconosce
 * la situazione, cosicché il controllo sia passato alla successiva,
 * oppure true qualora sia in grado di gestire tutto correttamente.
 * 
 * Nel caso di input malformato bisognerà lanciare un'eccezione 
 * contentente il messaggio di errore.
 */

/*
 * Stampa i dati generali su un annuncio
 */
function stampaInfoAnnuncio($ann)
{
?>
<div class="datiAnnuncio">
    <table class="datiAnnuncio">
        <caption> Informazioni annuncio
        </thead>
        <!-- Dati generali sull'annuncio -->
        <tbody>
            <tr><th>Partita  <td> <?= $ann->getMatchType() ?>
            <tr><th> Giorno  <td> <?= $ann->getDay() ?>
            <tr><th> Orario  <td> <?= $ann->getStartTime() ?>

        <?php if(FALSE and $ann->getNotes()) { ?>
        <tbody>
            <tr><th colspan="2"> Note: 
            <tr><td colspan="2" class="note"> <?= htmlspecialchars($ann->getNotes()) ?>
        <?php } ?>
    </table>
</div>
<?php
}

// Dati sui sottoscrittori 
function stampaGiocatoriAnnuncio($ann)
{
?>
<div class="partecipantiAnnuncio">
    <table class="partecipantiAnnuncio" name="partecipanti">
        <caption>Giocatori
        <tbody>
            <tr><th>Proprietario
                <td><a href="PaginaPersonale.php?userid=<?=$ann->getOwnerCode()?>">
                        <?= htmlspecialchars($ann->getOwnerName()) ?> </a>
            <tr><th>G2
                <td><a href="PaginaPersonale.php?userid=<?=$ann->getG2Code()?>">
                        <?= htmlspecialchars($ann->getG2Name()) ?> </a>
            <tr><th>G3
                <td><a href="PaginaPersonale.php?userid=<?=$ann->getG3Code()?>">
                        <?= htmlspecialchars($ann->getG3Name()) ?> </a>
            <tr><th>G4
                <td><a href="PaginaPersonale.php?userid=<?=$ann->getG4Code()?>">
                        <?= htmlspecialchars($ann->getG4Name()) ?> </a>
    </table>
</div>
<?php
}


/*
 *  Caso base:
 * 
 * la funzione gestisce la situazione in cui bisogna registrare un nuovo
 * annuncio
 */
function completamentoAnnuncio()
{
    global $conn, $usr, $ann;
    
    if(!isset($_GET["announcement"]))
        return false;
    /*
    * Per la gestione degli annunci
    */
    $annCODE = $_GET["announcement"];
    try{
        $ann = $conn->getAnnouncement($annCODE);
    }
    catch(Exception $exc)
    {
        throw new RuntimeException("L'annuncio indicato è inesistente.");
    }
    
    //if(!$ann->isSubscriptor($usr))
    //    throw new RuntimeException("Gli annunci possono essere registrati solo dai sottoscrittori.");
    if($ann->getState() !== "chiuso")
        return FALSE;
    // Poiché adesso si usa la stessa pagina per confermare e registrare gli annunci
        
?>
<!-- data-partite contiene informazioni sul numero di partite -->
<!-- data-annuncio contiene informazioni sul codice dell'annuncio -->
<form name="registrazioneAnnuncio" 
      data-partite="<?=$ann->getMatchType()=='singola'?1:3 ?>"
      data-annuncio="<?=$ann->getCode()?>">

    <?php stampaGiocatoriAnnuncio($ann) ?>
    <?php stampaInfoAnnuncio($ann) ?>

<!-- Qui si infilano i dati del risutato dell'incontro -->
    <div id="datiPartite">
        <?php 
        // non sapevo come chiamarla
        // crea il tbody per registrare una partita
        function cosoPartita($ann)
        {
            static $n = 1;

            /*
             * Crea il selettore
             */
            $selettoreGiocatore = function($S, $G) use($n, $ann){ ?> 
                <select name="m<?=$n?>S<?=$S?>G<?=$G?>" class="giocatore" required>
                    <option value="<?=$ann->getOwnerCode()?>"><?=htmlspecialchars($ann->getOwnerName())?></option>
                    <option value="<?=$ann->getG2Code()?>"><?=htmlspecialchars($ann->getG2Name())?></option>
                    <option value="<?=$ann->getG3Code()?>"><?=htmlspecialchars($ann->getG3Name())?></option>
                    <option value="<?=$ann->getG4Code()?>"><?=htmlspecialchars($ann->getG4Name())?></option>
                </select>
            <?php }
        ?>
        <div class="risultatoIncontro">
            <table class="risultatoIncontro">
                <caption> Esito partita <?=$n?> di <?=($ann->getMatchType() === "trittico"?3:1)?>
                <!-- Una partita viene registrata sempre e comunque -->
                <tbody id="match<?=$n?>" class="match" name="tabellaIncontro">
                    <!-- Composizione squadre -->
                    <tr><th><th> Squadra 1: <th> Squadra 2:
                    <tr><th rowspan="2"> Giocatori
                        <td> <?php $selettoreGiocatore(1,1) ?> 
                        <td> <?php $selettoreGiocatore(2,1) ?>
                    <tr><td> <?php $selettoreGiocatore(1,2) ?>
                        <td> <?php $selettoreGiocatore(2,2) ?>

                    <tr><th> Risultato: 
                        <td> <input id="m<?=$n?>S1" name="m<?=$n?>S1" type="number" class="risultato" min="0" required>
                        <td> <input id="m<?=$n?>S2" name="m<?=$n?>S2" type="number" class="risultato" min="0" required>
                </tbody>
            </table>
        </div>
        <?php
            ++$n;
        }
        ?>
            <?php cosoPartita($ann); // crea il primo tbody ?>

            <?php
            // Se è un trittico crea spazio per altri due partite
            if($ann->getMatchType() === "trittico") {
                cosoPartita($ann);
                cosoPartita($ann);
            } 
            ?>
        
        <div id="avvertimento">
            <span>Controlla che i valori inseriti siano validi!</span>
        </div>
        
        <div class="Conferma">
            <input type="reset" value="Cancella" id="reset">
            <input type="submit" value="Conferma" id="submit">
        </div>  
    </div>
</form>
<script type="text/javascript" src="../js/GestioneRegistrazioneAnnuncio.js"></script>
<script type="text/javascript" src="../js/ControlloriFormRegistrazionePartite.js"></script>
<?php

    // costruisce il form
    // contenuti una table:
    // 1 thead
    // 1-3 tbody per i dati
    // 1 tfoot per conferma
    
    return true;
}

//***************************************************************************************************

function stampaDatiPartita($match)
{
    global $ann;
    static $n = 1;
?>
    <div class="risultatoIncontro">
        <table class="risultatoIncontro">
            <caption> Esito partita <?=$n?> di <?=($ann->getMatchType() === "trittico"?3:1)?>
            <!-- Una partita viene registrata sempre e comunque -->
            <tbody id="match<?=$n?>" class="match" name="tabellaIncontro">
                <!-- Composizione squadre -->
                <tr><th><th> Squadra 1: <th> Squadra 2:
                <tr><th rowspan="2"> Giocatori
                    <td> <?=htmlspecialchars($match["S1G1name"])?>
                    <td> <?=htmlspecialchars($match["S2G1name"])?>
                <tr><td> <?=htmlspecialchars($match["S1G2name"])?>
                    <td> <?=htmlspecialchars($match["S2G2name"])?>

                <tr><th> Risultato: 
                    <td><?=htmlspecialchars($match["PunteggioSquadra1"])?>
                    <td><?=htmlspecialchars($match["PunteggioSquadra2"])?>
            </tbody>
        </table>
    </div>
<!--
    <tbody id="match< ?=$n?>" class="match" name="tabellaIncontro">
        <tr><th rowspan="2">Squadra 1:</th><td>< ?=htmlspecialchars($match["S1G1name"])?></td>
        </tr>
        <tr><td>< ?=htmlspecialchars($match["S1G2name"])?></td>
        </tr>

        <tr><th rowspan="2">Squadra 2:</th><td>< ?=htmlspecialchars($match["S2G1name"])?></td>
        </tr>
        <tr><td>< ?=htmlspecialchars($match["S2G2name"])?></td>
        </tr>

        <tr><th><label for="m< ?=$n?>S1">Risultato prima squadra:</label></th>
            <td>< ?=htmlspecialchars($match["PunteggioSquadra1"])?></td>
        </tr>
        <tr><th><label for="m< ?=$n?>S2">Risultato seconda squadra:</label></th>
            <td>< ?=htmlspecialchars($match["PunteggioSquadra2"])?></td>
        </tr>
    </tbody>
-->
<?php
    ++$n;
}

/*
 * Serve a permettere di confermare l'esito registrato di un annuncio
 */
function confermaAnnuncio()
{
    global $conn, $usr, $ann;
    if(!isset($_GET["announcement"]))
        return false;
    $annCODE = $_GET["announcement"];
    try{
        $ann = $conn->getAnnouncement($annCODE);
    }
    catch(Exception $exc)
    {
        throw new RuntimeException("L'annuncio indicato è inesistente.");
    }
    
    if(!$ann->isSubscriptor($usr)) return FALSE;
        //throw new RuntimeException("Gli annunci possono essere registrati solo dai sottoscrittori.");
    if($ann->getState() !== "confermando" || 
            $conn->hasConfirmedGroup($usr, $ann->getGroup()))
        return FALSE;
    
    $partite = $conn->announcementsMatches($ann->getCode());
    if(sizeof($partite) == 0)
        throw new Exception("Mancano partite");

?>
<form name="confermaAnnuncio" 
      data-gruppo="<?=$partite[0]["Gruppo"]?>"
      data-annuncio="<?=$ann->getCode()?>">

    <?php stampaGiocatoriAnnuncio($ann); ?>
    <?php stampaInfoAnnuncio($ann); ?>

    <div class="risultatoGenerale">
        <h3>Esito incontro</h3>

    <?php
        foreach($partite as $match)
            stampaDatiPartita($match);
    ?>
        <div class="Conferma">
            <input type="button" id="bottoneSconfessa" value="Sconfessa" id="reset">
            <input type="button" id="bottoneConferma" value="Conferma" id="submit">
        </div>
    </div>
</form>
<script type="text/javascript" src="../js/GestioneConfermaAnnuncio.js"></script>
<?php
    return true;
}

//*******************************************************************************

/*
 * Per quando si vuole semplicemente rivedere quanto appena confermato
 */
function visionaAnnuncio()
{
    global $conn, $usr, $ann;
    if(!isset($_GET["announcement"]))
        return false;
    $annCODE = $_GET["announcement"];
    try{
        $ann = $conn->getAnnouncement($annCODE);
    }
    catch(Exception $exc)
    {
        throw new RuntimeException("L'annuncio indicato è inesistente.");
    }
    
    //if(!$ann->isSubscriptor($usr))
    //    throw new RuntimeException("Gli annunci possono essere registrati solo dai sottoscrittori.");
    if($ann->getState() !== "confermando" || 
            !$conn->hasConfirmedGroup($usr, $ann->getGroup()))
        return FALSE;
    
    $partite = $conn->announcementsMatches($ann->getCode());
    if(sizeof($partite) == 0)
        throw new Exception("Mancano partite");

?>
<div name="visioneAnnuncio">

    <?php stampaGiocatoriAnnuncio($ann); ?>
    <?php stampaInfoAnnuncio($ann); ?>

    <div class="risultatoGenerale">
        <h3>Esito incontro</h3>
        
    <?php
        foreach($partite as $match)
            stampaDatiPartita($match);
    ?>
    </div>
</div>
<?php
    return true;
}
?>
