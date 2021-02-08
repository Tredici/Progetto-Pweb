"use strict";

var BACHECA = {
    tabella: undefined,
    rigaInserimento: undefined,
    
    mapAnnunci: undefined,

    userid: undefined,
    
    bottoneNuovoAnnuncio: undefined,
    
    aggiungiLinkUtente: function(code, name){
        var a = window.document.createElement("a");
        a.text = name;
        a.href = "PaginaPersonale.php?userid=" + code.toString();
        return a;
    },
    
    aggiungiSpazioNote: function(ann) {
        var ansNode = undefined;
        if(!!ann.Note) {
            ansNode = window.document.createElement("details");
            var summary = window.document.createElement("summary");
            ansNode.appendChild(summary);
            summary.textContent = "Leggi le note";
            var textarea = window.document.createElement("textarea");
            textarea.classList.add("notes");
            ansNode.appendChild(textarea);
            textarea.readOnly = true;
            
            textarea.textContent = ann.Note;
        } else {
            var text = "Non ci sono note";
            ansNode = window.document.createTextNode(text);
        }
        return ansNode;
    },
    
    // L'utente attuale è il proprietario dell'annuncio?
    isOwner: function(ann) {
        return this.userid == ann.Creatore;
    },

    isSubscriptor: function(ann) {
        return !!(this.userid == ann.G2
            || this.userid == ann.G3
            || this.userid == ann.G4);
    },

    /*
     * Crea un bottone da inserire nella tabella
     *  che mostra il testo "text", associato 
     *  all'annuncio "ann" e che quando viene 
     *  premuto invoca la funzione "callback"
     */
    creaBottoneTabella(ann, text, callback){
        var button = window.document.createElement("button");
        button.classList.add("button");
        button.addEventListener("click", callback);
        button.textContent = text;
        button.dataset.code = ann.Codice;
        return button;
    },

    /*
     * Riunisce la logica comune a tutte le invocazioni
     *  di API dalla pagina, ovvero:
     *      -ottenimento del codice dell'annuncio
     *      -invocazione dell'API
     *      -gestione della risposta
     *
     *  Poiché in caso di insuccesso le spiegazioni possono
     *  essere molteplici ma le più plausibili sono 
     *
     * @args:
     *  APIname:    il nome dell'API da invoca
     *  callback:   
     */
    APIcaller: function(APIname, callback) {
        return (e) => {
            var button = e.target;
            button.disabled = true;
            var code = button.dataset.code;
            console.log("Operazione annuncio " + code);

            var req = new XMLHttpRequest();
            req.onreadystatechange = (e) => {
                    var req = e.target;
                    if(req.readyState == XMLHttpRequest.DONE)
                    {
                        button.disabled = false;
                        if(req.status == 200)
                        {
                            var data = JSON.parse(req.responseText);
                            callback(button, data);
                        } else {
                            console.error("Problema con l'API!");
                            window.location.reload();
                            //throw "FatalErrorException";
                        }
                    }
                };
                req.open("POST", APIname);
                var data = new FormData();
                data.append("code", code);
                req.send(data);
            };
    },

    eliminaRigaAnnuncio: function(code) {
        if(!this.mapAnnunci.has(parseInt(code)))
        {
            console.error("Annuncio " + code + " inesistente!");
            throw new Error("FatalErrorException");
        }
        var tr = this.mapAnnunci.get(parseInt(code));
        tr.parentElement.removeChild(tr);
        this.mapAnnunci.delete(parseInt(code));
    },

    /*
     * Spstituisce la riga del vecchio annuncio individuato dal codice ann
     *  con la riga generata dalle informazioni dell'oggetto ann, 
     *  rappresentante a sua volta un annuncio
     */
    rimpiazzaRigaAnnuncio: function(code, ann) {
        if(!this.mapAnnunci.has(parseInt(code)))
        {
            console.error("Annuncio " + code + " inesistente!");
            throw new Error("FatalErrorException");
        }
        var tr = this.mapAnnunci.get(parseInt(code));
        // è la funzione stessa a memorizzare dentro "this.mapAnnunci"
        var trNew = window.document.createElement("tr");
        this.costruisciRigaAnnuncio(trNew, ann);
        tr.parentElement.replaceChild(trNew, tr);
        if(code != ann.Codice)
            this.mapAnnunci.delete(parseInt(code));
    },

    bottoneChiusuraAnnuncio: function(td, ann){
        td.appendChild(this.creaBottoneTabella(ann, "Chiudi", 
            this.APIcaller("../api/CloseAnnouncement.php", (button, data) => {
                var code = button.dataset.code;
                this.rimpiazzaRigaAnnuncio(code, data);
            })));
    },

    bottoneCancellaAnnuncio: function(td, ann){
        td.appendChild(this.creaBottoneTabella(ann, "Cancella", 
            this.APIcaller("../api/RemoveAnnouncement.php", (button, data) => {
                var code = data.Codice;
                this.eliminaRigaAnnuncio(code);
            })));
    },

    bottoneCancellaSottoscrizioneAnnuncio: function(td, ann){
        td.appendChild(this.creaBottoneTabella(ann, "Lascia", 
            this.APIcaller("../api/UnsubscribeAnnouncement.php", (button, data) => {
                // Codice del vecchio annuncio che di fatto corrisponde
                // con quello ritornato dall'API
                var code = button.dataset.code;
                this.rimpiazzaRigaAnnuncio(code, data);
            })));
    },

    bottoneSottoscrizioneAnnuncio: function(td, ann){
        td.appendChild(this.creaBottoneTabella(ann, "Unisciti", 
            this.APIcaller("../api/SubscribeAnnouncement.php", (button, data) => {
                // Codice del vecchio annuncio che di fatto corrisponde
                // con quello ritornato dall'API
                var code = button.dataset.code;
                this.rimpiazzaRigaAnnuncio(code, data);
            })));
    },

    bottoneRegistrazioneRisultato: function(td, ann){
        var button = this.creaBottoneTabella(ann, "Registra", (e) => {
            var button = e.target;
            button.disabled = true;
            var code = button.dataset.code;
            console.log("Operazione annuncio " + code);
            window.location.assign("RegistrazionePartite.php?announcement=" + ann.Codice);
        })
        td.appendChild(button);
        return button;
    },

    bottoneConfermaRisultato: function(td, ann){
        td.appendChild(this.creaBottoneTabella(ann, "Conferma", (e) => {
            var button = e.target;
            button.disabled = true;
            var code = button.dataset.code;
            console.log("Operazione annuncio " + code);
            window.location.assign("RegistrazionePartite.php?announcement=" + ann.Codice);
        }));
    },

    aggiungiBottoniControllo: function(td, ann) {
        switch(ann.Stato) {
            case 'attivo':
                if(this.isOwner(ann))
                    this.bottoneCancellaAnnuncio(td, ann);
                else if(this.isSubscriptor(ann))
                    this.bottoneCancellaSottoscrizioneAnnuncio(td, ann);
                else
                    this.bottoneSottoscrizioneAnnuncio(td, ann);
                break;
            case 'pronto':
                if(this.isOwner(ann))
                {
                    this.bottoneChiusuraAnnuncio(td, ann);
                    this.bottoneCancellaAnnuncio(td, ann);
                }
                else if(this.isSubscriptor(ann))
                    this.bottoneCancellaSottoscrizioneAnnuncio(td, ann);
                break;
            case 'chiuso':
                this.bottoneRegistrazioneRisultato(td, ann);
                break;
            case 'confermando':
                var button = this.bottoneRegistrazioneRisultato(td, ann);
                if(ann.data == "confirmed")
                    button.textContent = "Visiona";
                else
                    button.textContent = "Conferma";
                break;
        }
    },
    
    costruisciRigaAnnuncio: function(tr, ann) {
        this.mapAnnunci.set(parseInt(ann.Codice), tr);
        var th = window.document.createElement("th");
        
        if(this.userid == ann.Creatore)
            tr.classList.add("proprietario");

        if(ann.Stato == "scaduto") tr.style.display = "none";

        tr.dataset.code = ann.Codice;

        tr.appendChild(th);
        th.appendChild(this.aggiungiLinkUtente(ann.Creatore, ann.UsernameCreatore));
        // Tipo partita:
        var td = tr.insertCell();
        td.textContent = ann.TipoPartita;
        // Data e ora
        td = tr.insertCell();
        td.textContent = ann.Giorno + ' ' + ann.Inizio.substr(0, ann.Inizio.length-3);
        // Note
        td = tr.insertCell();
        td.appendChild(this.aggiungiSpazioNote(ann));
        
        // lista giocatori
        td = tr.insertCell();
        var ul = window.document.createElement("ul");
        td.appendChild(ul);
        ul.classList.add("giocatori");
        for(var i=2; i<=4; ++i)
            if(!!ann["G"+i]){
                var li = window.document.createElement("li");
                li.appendChild(this.aggiungiLinkUtente(ann["G"+i], ann["UsernameG"+i]));
                ul.appendChild(li);
            }

        // Stato
        td = tr.insertCell();
        td.textContent = ann.Stato;
        tr.classList.add(ann.Stato);
        tr.classList.add("annuncio");

        // Sezione con i bottoni per il controllo
        td = tr.insertCell();
        this.aggiungiBottoniControllo(td, ann);
        
        return tr;
    },
    
    appendiAnnuncio: function(ann) {
        var tbody = this.rigaInserimento.parentElement;
        var index = this.rigaInserimento.rowIndex;
        var tr = tbody.insertRow(index-1);
        this.costruisciRigaAnnuncio(tr,ann);
    },
    
    costruisciTabella: function(annunci) {
        for(var ann of annunci) {
            this.appendiAnnuncio(ann);
        }
    },
    
    initForm: function(){
        this.form = window.document.getElementById("RegistrazioneNuovoAnnuncio");
        
        Date.prototype.addDay = function(days) {
            var d = new Date(this.valueOf());
            d.setDate(d.getDate() + days);
            return d;
        };

        Date.prototype.addMin = function(days) {
            var d = new Date(this.valueOf());
            d.setMinutes(d.getMinutes() + days);
            return d;
        }

        Date.prototype.round5 = function() {
            var d = new Date(this.valueOf());
            d.setMinutes(parseInt((d.getMinutes()/5)+1)*5);
            d.setSeconds(0);
            d.setMilliseconds(0);
            return d;
        }

        this.form.date.min = new Date(Date.now()).addMin(5).round5().toISOString().substr(0,10);
        this.form.date.max = new Date(Date.now()).addMin(5).addDay(3).round5().toISOString().substr(0,10);

        var controllore = (e) => {
            var date = this.form.date.value;
            var time = this.form.time.value;

            if(date == new Date(Date.now()).toISOString().substr(0,10)) {
                var oraScelta = new Date();
                oraScelta.setHours(time.substr(0,2), time.substr(3,5));
                if(new Date() >= oraScelta) {
                    this.form.time.value = "";
                    console.error("Orario problematico!");
                    return false;
                }
            }
            return true;
        };

        window.setInterval(controllore, 60000);

        this.form.addEventListener("submit", (e) => {
            e.preventDefault();
            var form = e.target;

            if(!controllore()) {
                form.reportValidity();
                return;
            }
            
            var data = new FormData(form);
            var date = data.get("date");
            var time = data.get("time");
            data.delete("date");
            data.delete("time");
            data.append("Giorno", date);
            data.append("Orario", time);
            
            if("" == data.get("Notes")) data.delete("Notes");

            var req = new XMLHttpRequest();
            req.onreadystatechange = (e) => {
                var req = e.target;
                if(req.readyState === XMLHttpRequest.DONE)
                {
                    form.reset();
                    if(req.status === 200)
                    {
                        console.info("Nuovo annuncio creato.");
                        var ann = JSON.parse(req.responseText);
                        this.appendiAnnuncio(ann);
                        console.info("Annuncio appeso.");
                    } else {
                        console.error("Fallita creazione di un nuovo annuncio");
                        window.location.reload();
                        throw new Error("FatalErrorException");
                    }
                }
            };
            req.open("POST", "../api/AddAnnouncement.php");

            req.send(data);            
        });
    },
 
    init: function()
    {
        this.tabella = window.document.getElementById("Bacheca");
        this.rigaInserimento = this.tabella.tBodies[0].rows[0];
        
        if(!this.rigaInserimento.dataset.me){
            console.error("Manca il codice dell'utente");
            throw new Error("FatalErrorException");
        } else {
            this.userid = this.rigaInserimento.dataset.me;
        }
        this.mapAnnunci = new Map();
        this.initForm();
        
        SCARICAMENTO_ANNUNCI.scarica((data) => this.costruisciTabella(data));
        
    }
};


window.addEventListener("load", () => BACHECA.init());

