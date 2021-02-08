<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function inserisciFotoUtente()
{
    global $ppb, $paginaPersonale;
    $doc = new DOMDocument;
    $figure = $doc->createElement("figure");
    $doc->appendChild($figure);

    $figID = $doc->createAttribute("id");
    $figure->appendChild($figID);
    $figID->value = "Fotografia";

    $img = $doc->createElement("img");
    $figure->appendChild($img);

    $imgCLASS = $doc->createAttribute("class");
    $img->appendChild($imgCLASS);
    $imgCLASS->value = "profilo";

    $imgID = $doc->createAttribute("id");
    $img->appendChild($imgID);
    $imgID->value = "FotoProfilo";

    $imgALT= $doc->createAttribute("alt");
    $img->appendChild($imgALT);
    $imgALT->value = ($paginaPersonale? "La tua foto profilo." : $ppb->getUserName());

    $imgSRC= $doc->createAttribute("src");
    $img->appendChild($imgSRC);
    if($paginaPersonale)
    {
        $imgSRC->value = "../api/Me.php";
    } else {
        $imgSRC->value = "../api/Me.php?userid=" . $ppb->getOwnwerCode();
    }

    $figcaption = $doc->createElement("figcaption");
    $figure->appendChild($figcaption);
    $figcaption->appendChild($doc->createTextNode($paginaPersonale? "Tu." : $ppb->getUserName()));

    echo $doc->saveHTML();
    unset($doc);
}