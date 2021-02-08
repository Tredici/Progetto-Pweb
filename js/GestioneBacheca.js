"use strict";

var BACHECA = {
    annunciFissati: undefined,
    annunciPronti: undefined,
    annunciPropri: undefined,
    annunciSottoscritti: undefined,
    annunciLiberi: undefined,
    
    selettore: undefined,
    
    mostraAnnunci: function(tipo)
    {
        var container = window.document.getElementById("container");
        
        function rimuoviAnnunci()
        {
            //var divAnnunci = container.getElementsByName("divAnnunci");
            var divAnnunci = window.document.getElementsByName("divAnnunci");
            while (divAnnunci.length)
            {
                var el = divAnnunci[0];
                el.parentNode.removeChild(el)
            }
        }
        
        switch (tipo) {
            case "fissati":
                rimuoviAnnunci();
                container.appendChild(this.annunciFissati);
                break;

            case "pronti":
                rimuoviAnnunci();
                container.appendChild(this.annunciPronti);
                break;

            case "liberi":
                rimuoviAnnunci();
                container.appendChild(this.annunciLiberi);
                break;

            case "propri":
                rimuoviAnnunci();
                container.appendChild(this.annunciPropri);
                break;

            case "sottoscritti":
                rimuoviAnnunci();
                container.appendChild(this.annunciSottoscritti);
                break;

            default:
                throw new Error("InvalidArgumentException");
                break;
        }
    },
    
    bottoneNuovoAnnuncio: undefined,
    
    generatoreFormAggiuntaAnnuncio: function(e)
    {
        var button = e.target;
        var container = button.parentNode;
        container.removeChild(button);
        
        var form = window.document.createElement("form");
        container.appendChild(form);
        form.setAttribute("id", "formNuovoPosto");
        
        var table = window.document.createElement("table");
        form.appendChild(table);
        
        var thead = window.document.createElement("thead");
        table.appendChild(thead);
        
        var tbody = window.document.createElement("tbody");
        table.appendChild(tbody);
        
        var tfoot = window.document.createElement("tfoot");
        table.appendChild(tfoot);
        
        function addTHead()
        {
            var tr = window.document.createElement("tr");
            thead.appendChild(tr);
            
            var th = window.document.createElement("th");
            tr.appendChild(th);
            th.setAttribute("colspan", 2);
            th.textContent = "Nuovo annuncio!";
        }
        addTHead();
        
        function addBottoni()
        {
            var reset = window.document.createElement("input");
            reset.setAttribute("type", "reset");
            reset.setAttribute("value", "Annulla");
            reset.classList.add("bottoneForm");

            var submit = window.document.createElement("input");
            submit.setAttribute("type", "submit");
            submit.setAttribute("value", "Conferma");
            submit.classList.add("bottoneForm");

            // la gestione di reset e submit è associata direttamente alla form
            
            var tr = window.document.createElement("tr");
            tfoot.appendChild(tr);
            
            var td = window.document.createElement("td");
            tr.appendChild(td);
            td.appendChild(reset);
            
            var td = window.document.createElement("td");
            tr.appendChild(td);
            td.appendChild(submit);
        }
        addBottoni();
        
        function addRow(labelString, input)
        {
            var label = window.document.createElement("label");
            label.textContent = labelString;
            
            label.setAttribute("for", input.id);
            var tr = window.document.createElement("tr");
            tbody.appendChild(tr);
            var th = window.document.createElement("th");
            tr.appendChild(th);
            th.appendChild(label);
            
            var td = window.document.createElement("td");
            tr.appendChild(td);
            td.appendChild(input);
        }
        
        // giorno
        var inputGiorno = window.document.createElement("input");
        inputGiorno.setAttribute("type", "date");
        inputGiorno.setAttribute("name", "Giorno");
        inputGiorno.setAttribute("id", "Giorno");
        inputGiorno.setAttribute("required", "");
        
        function oggi()
        {
            var d = new Date();
            return `${d.getFullYear()}-${d.getMonth()+1<10?"0"+(d.getMonth()+1):d.getMonth()+1}-${d.getDate()<10?"0"+d.getDate():d.getDate()}`;
        }
        
        inputGiorno.setAttribute("min", oggi());
        
        addRow("Inserisci il giorno in cui vorresti giocare:", inputGiorno);
        
        // ora inizio
        var inputOrario = window.document.createElement("input");
        inputOrario.setAttribute("type", "time");
        inputOrario.setAttribute("name", "Orario");
        inputOrario.setAttribute("id", "Orario");
        inputOrario.setAttribute("step", 60*5);
        inputOrario.setAttribute("required", "");
        
        addRow("Inserisci l'orario in cui vorresti giocare:", inputOrario);
        
        //ENUM('partita singola', 'trittico')
        var inputPartita = window.document.createElement("select");
        inputPartita.setAttribute("name", "TipoPartita");
        inputPartita.setAttribute("id", "TipoPartita");
        
        var singola = window.document.createElement("option");
        inputPartita.appendChild(singola);
        singola.textContent = "Partita singola";
        singola.setAttribute("value", "singola");
        var trittico = window.document.createElement("option");
        inputPartita.appendChild(trittico);
        trittico.textContent = "Trittico";
        trittico.setAttribute("value", "trittico");
        
        addRow("Inserisci il tipo di partita proposto:", inputPartita);
        
        form.addEventListener("reset", (e) => {
                        e.preventDefault();
                        container.removeChild(form);
                        container.appendChild(BACHECA.bottoneNuovoAnnuncio);
                    }
                );
        
        // Gestione del submit
        form.addEventListener("submit", (e) => {
                        e.preventDefault();
                        var handler = (e) => {
                                var req = e.target;
                                if(req.readyState == XMLHttpRequest.DONE)
                                {
                                    if(req.status == 200) {
                                        window.location.reload();
                                    } else {
                                        throw "FatalErrorException";
                                    }
                                }
                            };
                        
                        var req = new XMLHttpRequest();
                        req.onreadystatechange = handler;
                        req.open("POST", "../api/AddAnnouncement.php");
                        
                        var data = new FormData();
                        data.append("TipoPartita", form.TipoPartita.value);
                        data.append("Giorno", form.Giorno.value);
                        data.append("Orario", form.Orario.value);
                        
                        req.send(data);
                    }
                );
    },
    
    /*
     * Si occupa di individuare tutti i bottoni per cancellare i propri annunci
     */
    eventoCancellaAnnuncio: function()
    {
        // trova tutti i bottoni di cancellazione e ci attacca l'event handler
        var btns = window.document.getElementsByName("BottoneCancellazione");
        
        for(var el of btns)
        {
            el.addEventListener("click", (e) => {
                    var button = e.target;
                    var code = button.dataset.code;
                    console.log("Cancellazione annuncio " + code);
                    
                    var req = new XMLHttpRequest();
                    req.onreadystatechange = (e) => {
                            var req = e.target;
                            if(req.readyState == XMLHttpRequest.DONE)
                            {
                                if(req.status == 200)
                                {
                                    window.location.reload();
                                }
                                else
                                {
                                    throw "FatalErrorException";
                                }
                            }
                        };
                    
                    req.open("POST", "../api/RemoveAnnouncement.php");
                    
                    var data = new FormData();
                    data.append("code", code);
                    
                    req.send(data);
                });
        }
    },
    
    /*
     * Si occupa di individuare tutti i bottoni per sottoscrivere gli annunci altrui
     */
    eventoSottoscriviAnnuncio: function()
    {   
        var btns = window.document.getElementsByName("BottoneSottoscrizione");
        
        for(var el of btns)
        {
            el.addEventListener("click", (e) => {
                    var button = e.target;
                    var code = button.dataset.code;
                    console.log("Sottoscrizione annuncio " + code);
                    
                    var req = new XMLHttpRequest();
                    req.onreadystatechange = (e) => {
                            var req = e.target;
                            if(req.readyState == XMLHttpRequest.DONE)
                            {
                                if(req.status == 200)
                                {
                                    window.location.reload();
                                }
                                else
                                {
                                    throw "FatalErrorException";
                                }
                            }
                        };
                    
                    req.open("POST", "../api/SubscribeAnnouncement.php");
                    
                    var data = new FormData();
                    data.append("code", code);
                    
                    req.send(data);
                });
        }
    },
    
    /*
     * Se un annuncio può essere sottoscritto allora si deve poter anche 
     *  cancellare la sottoscrizione
     */
    eventoCancellaSottoscrizioneAnnuncio: function()
    {
        var btns = window.document.getElementsByName("BottoneDisiscrizione");
        
        for(var el of btns)
        {
            el.addEventListener("click", (e) => {
                    var button = e.target;
                    var code = button.dataset.code;
                    console.log("Cancellazione sottoscrizione annuncio " + code);
                    
                    var req = new XMLHttpRequest();
                    req.onreadystatechange = (e) => {
                            var req = e.target;
                            if(req.readyState == XMLHttpRequest.DONE)
                            {
                                if(req.status == 200)
                                {
                                    window.location.reload();
                                }
                                else
                                {
                                    throw "FatalErrorException";
                                }
                            }
                        };
                    
                    req.open("POST", "../api/UnsubscribeAnnouncement.php");
                    
                    var data = new FormData();
                    data.append("code", code);
                    
                    req.send(data);
                });
        }
    },
    
    /*
     * Chi può creare gli annunci li puè anche cancellare
     */
    eventoCancellazioneAnnuncio: function()
    {
        var btns = window.document.getElementsByName("BottoneCancellazione");
        
        for(var el of btns)
        {
            el.addEventListener("click", (e) => {
                    var button = e.target;
                    var code = button.dataset.code;
                    console.log("Cancellazione annuncio " + code);
                    
                    var req = new XMLHttpRequest();
                    req.onreadystatechange = (e) => {
                            var req = e.target;
                            if(req.readyState == XMLHttpRequest.DONE)
                            {
                                if(req.status == 200)
                                {
                                    window.location.reload();
                                }
                                else
                                {
                                    throw "FatalErrorException";
                                }
                            }
                        };
                    
                    req.open("POST", "../api/DeleteAnnouncement.php");
                    
                    var data = new FormData();
                    data.append("code", code);
                    
                    req.send(data);
                });
        }
    },
    
    /*
     * Gli annunci possono essere chiusi dall'autore una volta che sono stati
     *  sottoscritti da 3 persone
     */
    eventoChiusuraAnnuncio: function()
    {
        var btns = window.document.getElementsByName("BottoneAccettazione");
        
        for(var el of btns)
        {
            el.addEventListener("click", (e) => {
                    var button = e.target;
                    var code = button.dataset.code;
                    console.log("Cancellazione annuncio " + code);
                    
                    var req = new XMLHttpRequest();
                    req.onreadystatechange = (e) => {
                            var req = e.target;
                            if(req.readyState == XMLHttpRequest.DONE)
                            {
                                if(req.status == 200)
                                {
                                    window.location.reload();
                                }
                                else
                                {
                                    throw "FatalErrorException";
                                }
                            }
                        };
                    
                    req.open("POST", "../api/CloseAnnouncement.php");
                    
                    var data = new FormData();
                    data.append("code", code);
                    
                    req.send(data);
                });
        }
    },
    
    init: function()
    {
        this.eventoCancellaAnnuncio();
        this.eventoSottoscriviAnnuncio();
        this.eventoCancellaSottoscrizioneAnnuncio();
        this.eventoCancellazioneAnnuncio();
        this.eventoChiusuraAnnuncio();
        
        this.bottoneNuovoAnnuncio = window.document.getElementById("buttonAggiuntaAnnunci");
        this.bottoneNuovoAnnuncio.addEventListener("click", (e) => this.generatoreFormAggiuntaAnnuncio(e));
        
        this.annunciFissati = window.document.getElementById("annunciFissati");
        this.annunciLiberi = window.document.getElementById("annunciLiberi");
        this.annunciLiberi.parentNode.removeChild(this.annunciLiberi);
        
        this.annunciPronti = window.document.getElementById("annunciPronti");
        this.annunciPronti.parentNode.removeChild(this.annunciPronti);
        
        this.annunciPropri = window.document.getElementById("annunciPropri");
        this.annunciPropri.parentNode.removeChild(this.annunciPropri);
        
        this.annunciSottoscritti = window.document.getElementById("annunciSottoscritti");
        this.annunciSottoscritti.parentNode.removeChild(this.annunciSottoscritti);
        
        this.selettore = window.document.getElementById("selettore");
        var handler = (e) => this.mostraAnnunci(e.target.value);
        this.selettore.addEventListener("change", handler);
    }
};


window.addEventListener("load", () => BACHECA.init());

