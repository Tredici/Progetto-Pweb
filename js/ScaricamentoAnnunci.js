"use strict";
/* 
 * Inizia la sperimentazione per presentare gli annunci tutti insieme in forma 
 *  tabellare
 */

var SCARICAMENTO_ANNUNCI = {
    // array di annunci scaricati dal server
    annunci: undefined,
    
    defaultDownloadHandler: function(e)
    {
        var req = e.target;
        if(req.readystate === XMLHttpRequest.DONE)
        {
            if(req.state === 200)
            {
                console.info("Invocazione API terminata con successo");
                try {
                    this.annunci = JSON.parse(req.responseText);
                    console.info("Annunci scaricati correttamente e "+
                            "memorizzanti entro this.annunci");
                }
                catch (e) {
                    console.error("")
                    throw e;
                }

            } else {
                console.error("Scaricamento annunci fallito");
                throw new Error("FatalErrorException");
            }
        }
    },
    
    // invoca l'api per ottenere gli annunci dal server
    scarica: function(callback)
    {
        var req = new XMLHttpRequest();
        if(!callback) req.onreadystatechange = defaultDownloadHandler;
        else req.onreadystatechange = (e) => {
            var req = e.target;
            if(req.readyState === XMLHttpRequest.DONE)
            {
                if(req.status === 200)
                {
                    console.info("Invocazione API terminata con successo");
                    try {
                        this.annunci = JSON.parse(req.responseText);
                        console.info("Annunci scaricati correttamente e "+
                                "memorizzanti entro this.annunci");
                        callback(this.annunci);
                    }
                    catch (e) {
                        console.error("Unexpected fail!");
                        throw e;
                    }

                } else {
                    console.error("Scaricamento annunci fallito");
                    throw new Error("FatalErrorException");
                }
            }
        };
        req.open("GET", "../api/GetAvaibleAnnouncement.php");
        req.send();
        
        return req;
    }
}

