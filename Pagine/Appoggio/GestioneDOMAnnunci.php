<?php
// Funcioni usate per creare gli annunci nella bacheca

/*
 * @params
 *      $place:     DOMDocument
 * 
 * @return
 *  DOMElement - il div creato
 * 
 * Crea il div che conterrà bottone che dovrebbe poi essere usato per 
 *      aggiungere nuovi post (gestito tramite js)
 */
function addButtonNewAnnouncements($li)
{
    $doc = $li->ownerDocument;
    $div = $doc->createElement("div");
    $li->appendChild($div);
    
    $divID = $doc->createAttribute("id");
    $div->appendChild($divID);
    $divID->value = "divAggiuntaAnnunci";
    
    $button = $doc->createElement("button");
    $div->appendChild($button);
    $button->textContent = "Aggiungi un annuncio";
    
    $buttonID = $doc->createAttribute("id");
    $button->appendChild($buttonID);
    $buttonID->value = "buttonAggiuntaAnnunci";
    
    return $div;
}

/*
 * @params
 *      $place:     DOMElement
 *      $message:   string
 * 
 * @return
 *  DOMElement - il div creato
 * 
 * Crea un div che mostra un messaggio di avvertimento
 */
function addDivWarning($place, $message)
{
    $doc = $place->ownerDocument;
    $div = $doc->createElement("div");
    $place->appendChild($div);
    
    $divNAME = $doc->createAttribute("name");
    $div->appendChild($divNAME);
    $divNAME->value = "Warning";
    
    $p = $doc->createElement("p");
    $div->appendChild($p);
    
    $p->textContent = $message;
    return $div;
}

/*
 * @params
 *      $doc:       DOMDocument
 *      $listID:    string
 * 
 * @return
 *  void
 * 
 * Crea un div che dovrebbe contenere l'elenco degli annunci
 */
function createList($doc, $listID)
{
    // classe contenitore
    $div = $doc->createElement("div");
    $doc->appendChild($div);    // Questo crea una dipendenza bruttina, ma qui possiamo tollerarla

    $divID = $doc->createAttribute("id");
    $div->appendChild($divID);
    $divID->value = $listID;

    $divNAME = $doc->createAttribute("name");
    $div->appendChild($divNAME);
    $divNAME->value = "divAnnunci";
    
    $lista = $doc->createElement("div");
    $div->appendChild($lista);

    $listaNAME = $doc->createAttribute("class");
    $lista->appendChild($listaNAME);
    $listaNAME->value = "listaAnnunci";

    return $lista;
}

/*
 * @params
 *      $divAnn:    DOMElement
 *      $Gcode:     int
 *      $Gname:     string
 * 
 * @return
 *  void
 * 
 * Aggiunge al div che rappresenta il post una lista con
 * i giocatori iscritti
 */
function addPlayer($ann, $Gcode, $Gname)
{
    $doc = $ann->ownerDocument; // serve per manipolare il resto
    $list = $ann->getElementsByTagName("ul");

    if($list->length == 0) {
        $div = $ann->getElementsByTagName("div");
        $partecipanti = $div[0];
        
        $sottoscrittori = $doc->createElement("ul");
        $partecipanti->appendChild($sottoscrittori);

        $annNAME = $doc->createAttribute("name");
        $sottoscrittori->appendChild($annNAME);
        $annNAME->value = "sottoscrittori";
    } else {
        $sottoscrittori = $list->item(0); // di lista ce ne può essere una sola
    }

    // accoda
    $usr = $doc->createElement("li");
    $sottoscrittori->appendChild($usr);

    $a = $doc->createElement("a");
    $usr->appendChild($a);
    $a->textContent = $Gname;

    $aHREF = $doc->createAttribute("href");
    $a->appendChild($aHREF);
    $aHREF->value = "PaginaPersonale.php?userid=" . urlencode((String)$Gcode);
}

/*
 * @params
 *      $divAnn:    DOMElement
 *      $ann:       ClassAnnouncement
 * 
 * @return
 *  void
 * 
 * Aggiunge al div che rappresenta il post una lista con
 * i giocatori iscritti
 */
function addPlayers($divAnn, $ann)
{
    if($ann->getG2Code())
    {
        addPlayer($divAnn, $ann->getG2Code(), $ann->getG2Name());
        if($ann->getG3Code())
        {
            addPlayer($divAnn, $ann->getG3Code(), $ann->getG3Name());
            if($ann->getG4Code())
            {
                addPlayer($divAnn, $ann->getG4Code(), $ann->getG4Name());
            }
        }
    }
}

/*
 * @params
 *      $divAnn:    DOMElement
 *      $button:    DOMElement
 * 
 * @return
 *  int - il codice
 * 
 * Aggiunge al bottone del post l'attributo del div dell'annuncio che ne riporta
 *  il codice
 */
function addAnnCodeToButton($divAnn, $button)
{
    $doc = $divAnn->ownerDocument;
    // codice dell'annuncio in formato stringa
    $CODE = $divAnn->getAttribute("data-code");
    $annCODE = $doc->createAttribute("data-code");
    $button->appendChild($annCODE);
    $annCODE->value = $CODE;

    return $CODE;
}

/*
 * @params
 *      $divAnn:    DOMElement
 * 
 * @return
 *  DOMElement - il bottone
 * 
 * Aggiunge al div che rappresenta il post un bottone per "accettare"
 *  l'annuncio, ovvero per dire "gente, si gioca!"
 */
function addAcceptButton($divAnn)
{
    $doc = $divAnn->ownerDocument;

    $button = $doc->createElement("button");
    $divAnn->appendChild($button);
    $button->textContent = "Accetta";

    $bName = $doc->createAttribute("name");
    $button->appendChild($bName);
    $bName->value = "BottoneAccettazione";
    
    addAnnCodeToButton($divAnn, $button);
    
    // l'aggiunta della callback si farà tramite js
    return $button;
}

/*
 * @params
 *      $divAnn:    DOMElement
 * 
 * @return
 *  DOMElement - il bottone
 * 
 * Aggiunge al div che rappresenta il post un bottone per sottoscrivere
 *  l'annuncio
 */
function addSubButton($divAnn)
{
    $doc = $divAnn->ownerDocument;

    $button = $doc->createElement("button");
    $divAnn->appendChild($button);
    $button->textContent = "Sottoscrivi";

    $bName = $doc->createAttribute("name");
    $button->appendChild($bName);
    $bName->value = "BottoneSottoscrizione";
    
    addAnnCodeToButton($divAnn, $button);
    
    // l'aggiunta della callback si farà tramite js
    return $button;
}

/*
 * @params
 *      $divAnn:    DOMElement
 * 
 * @return
 *  DOMElement - il bottone
 * 
 * Aggiunge al div che rappresenta il post un bottone per cancellare
 *  l'annuncio
 */
function addDeleteButton($divAnn)
{
    $doc = $divAnn->ownerDocument;
    
    $button = $doc->createElement("button");
    $divAnn->appendChild($button);
    $button->textContent = "Cancella";

    $bName = $doc->createAttribute("name");
    $button->appendChild($bName);
    $bName->value = "BottoneCancellazione";
    
    addAnnCodeToButton($divAnn, $button);
    
    // l'aggiunta della callback si farà tramite js
    return $button;
}

/*
 * @params
 *      $divAnn:    DOMElement
 * 
 * @return
 *  DOMElement - il bottone
 * 
 * Aggiunge al div che rappresenta il post un bottone per cancellare la
 *  sottoscrizione a un annuncio
 */
function addUnSubButton($divAnn)
{
    $doc = $divAnn->ownerDocument;

    $button = $doc->createElement("button");
    $divAnn->appendChild($button);
    $button->textContent = "Disiscriviti";

    $bName = $doc->createAttribute("name");
    $button->appendChild($bName);
    $bName->value = "BottoneDisiscrizione";
    
    addAnnCodeToButton($divAnn, $button);
    
    // l'aggiunta della callback si farà tramite js
    return $button;
}

/*
 * @params
 *      $divAnn:    DOMElement
 * 
 * @return
 *  DOMElement - il bottone
 * 
 * Aggiunge al div che rappresenta il post un bottone per passare
 *  alla pagina dove si 
 */
function addResultButton($divAnn)
{
    $doc = $divAnn->ownerDocument;
    
    $a = $doc->createElement("a");
    $aHref = $doc->createAttribute("href");
    $a->appendChild($aHref);
    
    $divAnn->appendChild($a);

    $button = $doc->createElement("button");
    $a->appendChild($button);
    $button->textContent = "Registra risultato";

    $bName = $doc->createAttribute("name");
    $button->appendChild($bName);
    $bName->value = "BottoneRisultato";
    
    $annCode = addAnnCodeToButton($divAnn, $button);
    
    $aHref->value = "RegistrazionePartite.php?announcement=" . (string)$annCode;

    // l'aggiunta della callback si farà tramite js
    return $button;
}

/*
 * Dopo che un utente ha registrato il risultato bisogna cambiare il
 *  messaggio da mostrargli
 */
function addConfirmButton($divAnn)
{
    $doc = $divAnn->ownerDocument;
    
    $a = $doc->createElement("a");
    $aHref = $doc->createAttribute("href");
    $a->appendChild($aHref);
    
    $divAnn->appendChild($a);

    $button = $doc->createElement("button");
    $a->appendChild($button);
    $button->textContent = "Conferma risultato";

    $bName = $doc->createAttribute("name");
    $button->appendChild($bName);
    $bName->value = "BottoneRisultato";
    
    $annCode = addAnnCodeToButton($divAnn, $button);
    
    $aHref->value = "RegistrazionePartite.php?announcement=" . (string)$annCode;

    // l'aggiunta della callback si farà tramite js
    return $button;
}

// In seguito bisogna anche permettergli di osservare
//  quello che aveva segnato
function addWatchButton($divAnn)
{
    $doc = $divAnn->ownerDocument;
    
    $a = $doc->createElement("a");
    $aHref = $doc->createAttribute("href");
    $a->appendChild($aHref);
    
    $divAnn->appendChild($a);

    $button = $doc->createElement("button");
    $a->appendChild($button);
    $button->textContent = "Visiona risultato";

    $bName = $doc->createAttribute("name");
    $button->appendChild($bName);
    $bName->value = "BottoneRisultato";
    
    $annCode = addAnnCodeToButton($divAnn, $button);
    
    $aHref->value = "RegistrazionePartite.php?announcement=" . (string)$annCode;

    // l'aggiunta della callback si farà tramite js
    return $button;
}


/*
 * @params
 *      $lista:     DOMElement  - lista cui accodare l'annuncio
 *      $ann:       ClassAnnouncement   - classe contenente i dati dell'annuncio
 * 
 * @return
 *  DOMElement - il div dell'annuncio creato
 * 
 * Aggiunge un div alla lista che rappresenta un l'annuncio 
 */
function createAnnBase($lista, $ann)
{
    $doc = $lista->ownerDocument;

    $annuncio = $doc->createElement("div");
    $lista->appendChild($annuncio);
    
    $annNAME = $doc->createAttribute("name");
    $annuncio->appendChild($annNAME);
    $annNAME->value = "annuncio";
    
    $annCODE = $doc->createAttribute("data-code");
    $annuncio->appendChild($annCODE);
    $annCODE->value = (string)$ann->getCode();
    
    $annCLASS = $doc->createAttribute("class");
    $annuncio->appendChild($annCLASS);
    $annCLASS->value = "annuncio " . (!$ann->getG2Code()? "mancano3" :
                                        (!$ann->getG3Code()? "mancano2":
                                            (!$ann->getG4Code()? "mancano1": "mancano0")
                                            )  );

    $annID = $doc->createAttribute("id");
    $annuncio->appendChild($annID);
    $annID->value = "annuncio" . (string)$ann->getCode();

    $annTITLE = $doc->createElement("h4");
    $annuncio->appendChild($annTITLE);
    $annTITLE->textContent = "Cercasi giocatori";

    $dati = $doc->createElement("table");
    $annuncio->appendChild($dati);

    $annNAME = $doc->createAttribute("name");
    $dati->appendChild($annNAME);
    $annNAME->textContent = "descrizione";

    $thead = $doc->createElement("thead");
    $dati->appendChild($thead);
    $tbody = $doc->createElement("tbody");
    $dati->appendChild($tbody);

    $tr = $doc->createElement("tr");
    $thead->appendChild($tr);

    appendTH($doc, $tr, "Autore");
    appendTH($doc, $tr, "Partita");
    appendTH($doc, $tr, "Giorno");
    appendTH($doc, $tr, "Orario");

    $tr = $doc->createElement("tr");
    $tbody->appendChild($tr);
    appendTDlink($doc, $tr, $ann->getOwnerName(),
            "PaginaPersonale.php?username=" . urlencode($ann->getOwnerName())
            );
    appendTD($doc, $tr, $ann->getMatchType());
    appendTD($doc, $tr, $ann->getDay());
    appendTD($doc, $tr, $ann->getStartTime());

    $divPartecipanti = $doc->createElement("div");
    $annuncio->appendChild($divPartecipanti);
    
    $divNAME = $doc->createAttribute("name");
    $divPartecipanti->appendChild($divNAME);
    $divNAME->value = "partecipanti";
    
    return $annuncio;
}