<?php

/*
 * Aggiungr attributi di tipo "data-" al DOMElement passato
 * 
 * @args:
 *  $el     - l'elemento cui aggiungere i dati
 *  $data   - l'array associativo con i dati da aggiungere
 * 
 * @return:
 */
function addData($el, $data)
{
    $doc = $el->ownerDocument;
    foreach($data as $key => $value) {
        $attr = $doc->createAttribute("data-" . $key);
        $attr->value = $value;
        $el->appendChild($attr);
    }
    
    return $el;
}

function appendTH($doc, $tr, $text, $data = NULL)
{
    $th = $doc->createElement("th");
    $tr->appendChild($th);

    $textNode = $doc->createTextNode($text);
    $th->appendChild($textNode);
    
    if(!is_null($data)) addData($th, $data);
    return $th;
}

function appendTD($doc, $tr, $text, $data = NULL)
{
    $td = $doc->createElement("td");
    $tr->appendChild($td);

    $textNode = $doc->createTextNode($text);
    $td->appendChild($textNode);
    
    if(!is_null($data)) addData($td, $data);
    return $td;
}

function appendTDlink($doc, $tr, $text, $goto, $data = NULL)
{
    $td = $doc->createElement("td");
    $tr->appendChild($td);

    $a = $doc->createElement("a");
    $td->appendChild($a);

    $aHREF = $doc->createAttribute("href");
    $a->appendChild($aHREF);
    $aHREF->value = $goto;

    $textNode = $doc->createTextNode($text);
    $a->appendChild($textNode);
    
    if(!is_null($data)) addData($td, $data);
    return $td;
}