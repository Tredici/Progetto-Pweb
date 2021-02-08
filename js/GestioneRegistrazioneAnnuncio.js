"use strict";
/* 
 * Gestione registrazione annuncio
 */

var ANNUNCIO = {
    
    form: undefined,
    
    controlloriDatiPartite: undefined,
    attaccaControlloriNomiGiocatori: function()
    {
        var partite = window.document.getElementById("datiPartite").getElementsByTagName("tbody");
        this.controlloriDatiPartite = [];
        for(var el of partite) this.controlloriDatiPartite.push( new cPartita(el) );
    },
    
    init: function()
    {
        this.attaccaControlloriNomiGiocatori();
        
        var form = {};
        this.form = form;
        form.form = window.document.forms.registrazioneAnnuncio;
        form.controllori = [];
        
        var tbodyIncontri = window.document.getElementsByName("tabellaIncontro");
        if(tbodyIncontri.length != form.form.dataset["partite"])
        {
            console.error("Inconsistenza tabella");
            throw new Error("FatalErrorException");
        }
        
        for(var tbody of tbodyIncontri)
            form.controllori.push(UTILITY_REGISTRAZIONE_PARTITE.controlloDatiIncontro(tbody));
        
        this.form.form.addEventListener("reset", (e) => {
            form.form.classList.remove("warning");
        });
        this.form.form.addEventListener("submit", (e) => {
            e.preventDefault();
            form.form.classList.remove("warning");
            if(e.target !== form.form)
            {
                console.error("Evento submit associato male.");
                throw new Error("Evento submit associato male");
            }

            var check = true;
            var partite = [];
            for(var c of form.controllori)
            {
                check = c.check() && check;
                // prende i dati della partita
                partite.push(c.ottieniDatiPartitaSingola());
            }

            if(!check)
            {
                form.form.classList.add("warning");
                console.error("Metti a posto");
                return;
            }
            
            // ricava il codice dell'annuncio
            var annuncio = parseInt(form.form.dataset["annuncio"]);
            if(!annuncio)
            {
                console.error("Manca il codice dell'annuncio nel form");
                throw new Error("MissingData");
            }
            
            var success = () => window.location.reload();
            var error = () => console.error("Molto malissimo!");
            
            form.form.classList.remove("warning");
            // ora ottiene i dati sulle partite
            // ma prima bisogna controllare quante ce ne siano
            switch(parseInt(form.form.dataset["partite"])) {
                case 1:
                    console.log("Tentativo di registrare una partita");
                    var datiPartita = partite[0];
                    UTILITY_REGISTRAZIONE_PARTITE.registraPartitaSingola_Oggetto(
                            datiPartita,
                            annuncio,
                            success, error);
                    break;
                    
                case 3:
                    console.log("Tentativo di registrare un trittico");
                    var datiTrittico = UTILITY_REGISTRAZIONE_PARTITE.ottieniDatiTrittico(...partite);
                    if(!datiTrittico)
                        form.form.classList.add("warning");
                    else
                        UTILITY_REGISTRAZIONE_PARTITE.registraTrittico_Oggetto(
                            datiTrittico,
                            annuncio,
                            success, error);
                    break;
                    
                default:
                    console.error("Le partite possono essere solo 1 o 3");
                    throw new Error("InvalidDataException");
                    break;
            }
        });
    }
}


window.addEventListener("load", () => ANNUNCIO.init());