"use strict";

var GESTORE = {

    /*
     * tbody della tabella che contiene
     */
    spazio_richieste: undefined,

    // richiesta globalmente sotto gestione al momento
    reqID: undefined,

    mappa_richieste: new Map(),

    fatalError: function()
    {
        // che si fa in caso di disastro?
        console.error("Fatal Error eccurred.");

        // In questo arco di tempo l'utente non può agire perché
        // la pagina non viene "liberata" fino al termine dell'esecuzuione
        // serve solo a
        var start = new Date().getTime();
        var end = start;
        while(end < start + 500)
            end = new Date().getTime();

        window.location.reload();
    },

    /**
     * Disabilita temporaneamente tutti i bottoni per la gestione delle richieste
     *  sarebbero: "accetta" "resping" "annulla"
     */
    disabilitaBottoni: function() {
        //throw new Error("NotImplementedException");
        var buttons = window.document.getElementsByClassName("bottoneGestione");
        for(var b of buttons)
            b.disabled = true;
    },
    
    /*
     * Complementare a quella sopra
     */
    abilitaBottoni: function() {
        //throw new Error("NotImplementedException");
        var buttons = window.document.getElementsByClassName("bottoneGestione");
        for(var b of buttons)
            b.disabled = false;
    },

    /**
     * Per la gestione delle richieste di iscrizione: accettazione e rigetto
     * @param {type} parameter
     */
    handlerRichiestaIscrizioneHTTP: function(e) {
        var req = e.target;
        //console.log(req);
        if(req.readyState == 2)
        {
            // sarebbe bello mettere qualche interfaccia che evidenzi
            // "elaborazione in corso" o cose così
        } else if(req.readyState == 4)
        {
            if(req.status == 200){
                window.location.reload();   // modo più semplice per gestire il tutto
            } else {
                throw "FailedRequestException";
            }
        }
    },

    /*
     * 
     * @param {type} e
     * @returns {undefined}
     */
    decidiRichiesta: function(reqID, sentenza)
    {
        var id = parseInt(reqID);
        if(isNaN(id) || id < 1)
        {
            console.log("Richiesta invalida");
            throw new Error("InvalidRequet");
        }

        var data = new FormData();
        data.append("reqID", reqID);

        switch (sentenza) {
            case "accetta":
                data.append("choice", "approved");
                break;
            case "respingi":
                data.append("choice", "rejected");
                break;

            default:
                console.log("Richiesta invalida");
                throw new Error("InvalidRequet");
                break;
        }

        var req = new XMLHttpRequest();
        req.open("POST", "../api/ManageSubscriptionRequest.php");
        req.onreadystatechange = (e) => this.handlerRichiestaIscrizioneHTTP(e);
        this.disabilitaBottoni();
        req.send(data);
    },

    /**
     * Accetta richiesta
     */
    acceptRequest: function(e) {
        e.preventDefault();
        this.decidiRichiesta(this.reqID, "accetta");
        //throw new Error("NotImplementedException");
    },

    /**
     * Respinge una richiesta
     */
    rejectRequest: function(e) {
        e.preventDefault();
        this.decidiRichiesta(this.reqID, "respingi");
        //throw new Error("NotImplementedException");
    },

    /**
     * Annulla gestione richiesta
     */
    annullaGestioneRichiesta: function(e) {
        e.preventDefault();
        this.reqID = undefined;
        var copertura = window.document.getElementById("copertura");
        var parent = copertura.parentNode;
        parent.removeChild(copertura);
    },

    /*
     * Aggiunge i dati alla tabella in questione
     * Serve per riempire facilmente la funzione per riempire le richieste
     * @returns {udefined}
     */
    addDataToTable: function(table, header, data)
    {
        var tr = table.insertRow();
        //var tr = window.document.createElement("tr");
        //table.appendChild(tr);
        var th = window.document.createElement("th");
        tr.appendChild(th);
        th.textContent = header;
        //var td = window.document.createElement("td");
        //tr.appendChild(td);
        tr.insertCell().textContent = data;
    },

    addNotesToTable: function(tbody, note)
    {
        var tr = tbody.insertRow();
        var th = window.document.createElement("th");
        tr.appendChild(th);
        th.textContent = "Note";
        var td = tr.insertCell();
        if(!note) {
            td.textContent = "Non ci sono note da leggere.";
        } else {
            var textarea = window.document.createElement("textarea");
            textarea.textContent = note;
            textarea.readOnly = true;
            textarea.classList.add("notes");
            td.appendChild(textarea);
        }
    },

    /*
     * Funzione associata ai bottoni delle richieste in attesa
     * @param {MouseEvent} e
     * @returns {undefined}
     */
    clickButton: function(e) {
        var button = e.target;  // bottone del click
        this.reqID = parseInt(button.id.substring(3));
        if(isNaN(this.reqID))
        {
            fatalError();
            throw "FatalErrorException";
        }
        var body = window.document.body;

        // Imposta lo sfondo (serve a impedire l'esecuzione di altri comandi che
        // lascerebbero la finestra in uno stato inconsistente)
        var copertura = window.document.createElement("div");
        copertura.setAttribute("id", "copertura");
        //copertura.style.position = "fixed";
        body.appendChild(copertura);
        // lo voglio
        //copertura.style.backgroundColor = "yellow";
        //copertura.style.background = "rgba(255, 255, 255, 0.5)";
        //copertura.style.top = 0;
        //copertura.style.bottom = 0;
        //copertura.style.left = 0;
        //copertura.style.right = 0;

        var selezione = window.document.createElement("div");
        selezione.name = "richiesta";
        copertura.appendChild(selezione);

        //selezione.style.position = "relative";
        //selezione.style.height = "50%";
        //selezione.style.width = "100%";
        //selezione.style.top = "25%";
        //selezione.style.backgroundColor = "white";

        // Crea il contenitore dove inserire ben ordinate tutti i dati della richiesta
        var gestione = window.document.createElement("div");
        selezione.appendChild(gestione);
        //gestione.setAttribute("class", "container");

        // metto una form
        // con una tabella con i dati della richiesta
        // con sotto 3 bottoni per "annullare", approvare o respingere la richiesta
        var form_gestione = window.document.createElement("form");
        gestione.appendChild(form_gestione);

        // riempie con i dati
        //form_gestione
        var contenitore_tabella = window.document.createElement("div");
        form_gestione.appendChild(contenitore_tabella);
        contenitore_tabella.id = "contenitore_tabella";

        var tabella_dati_richiesta = window.document.createElement("table");
        contenitore_tabella.appendChild(tabella_dati_richiesta);
        var reqUnderService = this.mappa_richieste.get(this.reqID);
        /*for(var x in reqUnderService){
            var tr = window.document.createElement("tr");
            tabella_dati_richiesta.appendChild(tr);
            var th = window.document.createElement("th");
            tr.appendChild(th);
        }*/

        tabella_dati_richiesta.createCaption().textContent = "Dati richiesta"
        
        var tbody = tabella_dati_richiesta.createTBody();

        this.addDataToTable(tbody, "Codice", reqUnderService.code);
        this.addDataToTable(tbody, "Nome", reqUnderService.name);
        this.addDataToTable(tbody, "Cognome", reqUnderService.surname);
        this.addDataToTable(tbody, "Username", reqUnderService.username);
        this.addDataToTable(tbody, "Email", reqUnderService.email);
        this.addDataToTable(tbody, "Compleanno", reqUnderService.birthday);
        this.addDataToTable(tbody, "Sesso", reqUnderService.sex);
        this.addDataToTable(tbody, "TS richiesta", reqUnderService.reqTS);

        // Gestione delle note
        this.addNotesToTable(tbody, reqUnderService.notes);
        
        // Lo stato non serve perché la richiesta è chiaramente in attesa

        //console.error("To Be continued");

        var button_accetta = window.document.createElement("button");
        button_accetta.textContent = "Accetta";
        button_accetta.classList.add("bottoneGestione");
        button_accetta.classList.add("accettazione");
        button_accetta.addEventListener("click", (e) => this.acceptRequest(e));

        var button_rifiuta = window.document.createElement("button");
        button_rifiuta.textContent = "Rifiuta";
        button_rifiuta.classList.add("bottoneGestione");
        button_rifiuta.classList.add("rifiuto");
        button_rifiuta.addEventListener("click", (e) => this.rejectRequest(e));


        var button_annulla = window.document.createElement("button");
        button_annulla.textContent = "Annulla";
        button_annulla.classList.add("bottoneGestione");
        button_annulla.classList.add("annullamento");
        button_annulla.addEventListener("click", (e) => this.annullaGestioneRichiesta(e));

        var divBottoni = window.document.createElement("div");
        divBottoni.classList.add("spazioBottoni");
        form_gestione.appendChild(divBottoni);

        divBottoni.appendChild(button_accetta);
        divBottoni.appendChild(button_rifiuta);
        divBottoni.appendChild(button_annulla);
    },

    /*
     * 
     * @param {SubscribeRequest} e
     * @returns {undefined}
     */
    addRequest: function(req)
    {
        this.mappa_richieste.set(parseInt(req.code), req);

        var row = window.document.createElement("tr");

        // Bisogna risparmiare spazio 
        /*var code = window.document.createElement("td");
        code.setAttribute("name", "code");
        code.textContent = req.code;
        row.appendChild(code);*/

        var name = window.document.createElement("td");
        name.setAttribute("name", "name");
        name.textContent = req.name;
        row.appendChild(name);

        var surname = window.document.createElement("td");
        surname.setAttribute("name", "surname");
        surname.textContent = req.surname;
        row.appendChild(surname);

        var email = window.document.createElement("td");
        email.setAttribute("name", "email");
        email.textContent = req.email;
        row.appendChild(email);

        var birthday = window.document.createElement("td");
        birthday.setAttribute("name", "birthday");
        birthday.textContent = req.birthday;
        row.appendChild(birthday);

        var sex = window.document.createElement("td");
        sex.setAttribute("name", "sex");
        sex.textContent = req.sex;
        row.appendChild(sex);

        /*var reqTS = window.document.createElement("td");
        reqTS.setAttribute("name", "reqTS");
        reqTS.textContent = req.reqTS;
        row.appendChild(reqTS);*/

        var reqDay = window.document.createElement("td");
        reqDay.setAttribute("name", "reqDay");
        reqDay.textContent = req.reqTS.split(' ')[0];
        row.appendChild(reqDay);

        var state = window.document.createElement("td");
        state.setAttribute("name", "state");
        //state.textContent = req.state;
        // state button
        var sButton = window.document.createElement("button");
        state.appendChild(sButton);

        sButton.classList.add("button");

        sButton.setAttribute("id", "req"+req.code);
        sButton.setAttribute("name", "stateButton");
        sButton.textContent = req.state;
        if("in attesa" != req.state)
        {
            sButton.disabled = true;
            sButton.classList.add(req.state);

        } else {
            sButton.disabled = false; // non è nemmeno necessario
            // assegna il gestore dei click
            sButton.addEventListener("click", (e) => this.clickButton(e));
        }
        row.classList.add(req.state.split(' ').pop());

        row.appendChild(state);
        this.spazio_richieste.appendChild(row);

        // per debugging
        return row;
    },

    /*
     * 
     * @param {DOMelement} el
     * @returns {undefined}
     */
    removeChildren: function(el)
    {
        while (el.firstChild  ) {
            el.removeChild(el.firstChild);
        }
    },

    /*
     * 
     * @param {type} e
     * @returns {undefined}
     */
    requestHandler: function(e)
    {
        var req = e.target;
        console.log(req);
        if(req.readyState == 1)
        {
            // ripulisce
            this.mappa_richieste.clear();
        } else if(req.readyState == 4)
        {
            if(req.status !== 200)
            {
                throw "FailedRequestException";
            }
            else
            {
                try{
                    // Analizza quello che dovrebbe essere il post caricato
                    var rQ = JSON.parse(req.responseText);
                    this.removeChildren(this.spazio_richieste);
                    for(var x of rQ)
                        this.addRequest(x);
                }
                catch(e)
                {
                    throw e;
                }
            }
        }
    },

    /*
     * Fa una "query" alla pagina corrispondente del db e scarica le informazioni
     *  corrispondenti alle richieste da gestire.
     */
    GetSubscriptionRequest: function()
    {
        var req = new XMLHttpRequest();
        req.onreadystatechange = (e) => this.requestHandler(e);
        req.open("GET", "../api/GetSubscriptionRequests.php");
        req.send();

        return req;
    },

    init: function() {
        this.spazio_richieste = window.document.getElementById("richieste");
        this.GetSubscriptionRequest();
    }

};

/*
 * Già al caricamento della pagina è il caso di scaricare le richieste dal
 * sito.
 */
window.addEventListener("load", () => GESTORE.init());

