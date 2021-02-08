"use strict";

var GESTIONE_CONFERMA = {
    
    annuncio: undefined,
    gruppo: undefined,
    
    // per on readystatechange...
    callback: function(e){
        var req = e.target;
        if(req.readyState === XMLHttpRequest.DONE)
        {
            if(req.status === 200)
            {
                window.location.assign('..');   // ritorna alla Home
            } else {
                throw new Error("FatalErrorException");
            }
        }
    },
    
    conferma: function()
    {
        UTILITY_REGISTRAZIONE_PARTITE.confermaEsito(this.gruppo, this.callback);
    },
    
    sconfessa: function()
    {
        UTILITY_REGISTRAZIONE_PARTITE.sconfessaEsito(this.gruppo, this.callback);
    },
    
    init: function()
    {
        this.annuncio = parseInt(window.document.forms.confermaAnnuncio.dataset.annuncio);
        this.gruppo = parseInt(window.document.forms.confermaAnnuncio.dataset.gruppo);
        
        window.document.getElementById("bottoneSconfessa").addEventListener("click",
            () => this.sconfessa());
        window.document.getElementById("bottoneConferma").addEventListener("click",
            () => this.conferma());
    }
};

window.addEventListener("load", () => GESTIONE_CONFERMA.init());