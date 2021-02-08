"use strict";
/* 
 * Qui ci metto il codice per permettere di ordinare la tabella
 */
var GESTIONE_CLASSIFICA = {
    tabella: undefined,
    ordinatore: undefined,
    
    init: function(){
        var tab = window.document.getElementById("Classifica");
        this.tabella = tab;
        this.ordinatore = new tOrdinatore(tab);
    }
};

window.addEventListener("load", () => GESTIONE_CLASSIFICA.init());
