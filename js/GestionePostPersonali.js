"use strict";

var POST = {
    /*
     * Riferimento che terrà il bottone
     */
    zona_inserimento: undefined,
    button_holder: undefined,
    poster_holder: undefined,

    /*
     * 
     * @param {Post} post
     * @returns {bool}
     */
    isPost: function(post)
    {
        return post.hasOwnProperty("codice") && post.hasOwnProperty("pagina") &&
            post.hasOwnProperty("autore") && post.hasOwnProperty("ts_creazione") &&
            post.hasOwnProperty("titolo") && post.hasOwnProperty("contenuto");
    },

    /*
     * Inserisce il post passato come argomento in coda agli altri già
     * presenti nella pagina
     * 
     * @param {Post} post
     * @returns {undefined}
     */
    addPersonalPost: function(post)
    {
        if(!this.isPost(post))
        {
            throw "InvalidArgumentException";
        }
        // crea il "contenitore" del post
        var newPost = window.document.createElement("div");
        newPost.setAttribute("id", "post"+post.codice.toString());

        newPost.dataset.code = post.codice.toString();

        newPost.classList.add("post");

        // imposta il titolo
        var title = window.document.createElement("h3");
        title.textContent = post.titolo;
        newPost.appendChild(title);

        // imposta il contenuto
        var content = window.document.createElement("p");
        content.textContent = post.contenuto;
        newPost.appendChild(content);

        var bottoneCancellazione = window.document.createElement("button");
        bottoneCancellazione.name = "bottoneCancellazione";
        bottoneCancellazione.addEventListener("click", (e) => this.APICancellazionePost(e));
        newPost.appendChild(bottoneCancellazione);
        bottoneCancellazione.textContent = "Rimuovi";
        bottoneCancellazione.classList.add("button");
        bottoneCancellazione.classList.add("cancellazione");

        // inserisce in coda
        var container = this.zona_inserimento.parentElement;
        container.insertBefore(newPost, this.zona_inserimento);
    },

    APICancellazionePost: function(e)
    {
        var button = e.target;
        button.disabled = true;
        button.textContent = "Rimozione in corso...";
        button.style.display = "inline";
        var post = button.parentElement;
        var code = post.dataset.code;
        
        var req = new XMLHttpRequest();
        var data = new FormData();
        data.append("code", code);

        req.onreadystatechange = (e) => {
            var req = e.target;
            if(req.readyState === XMLHttpRequest.DONE)
            {
                if(req.status === 200)
                {
                    console.info("Removing:");
                    console.dir(JSON.parse(req.responseText));
                    post.parentElement.removeChild(post);
                    console.info("Post " + code + " eliminato con successo");
                } else {
                    console.error("Impossibile eliminare il Post: " + code);
                    throw new Error("FatalErrorException");
                }
            }
        };
        req.open("POST", "../api/DeletePersonalPosts.php");
        req.send(data);

        // per debugging
        return req;
    },

    /*
     * 
     * @param {type} title
     * @param {type} text
     * @returns {undefined}
     */
    requestHandler: function(e)
    {
        var req = e.target;
        //console.log(req);
        if(req.readyState == 4)
        {
            if(req.status !== 200)
            {
                throw "FailedRequestException";
            }
            else
            {
                try{
                    // Analizza quello che dovrebbe essere il post caricato
                    var uPost = JSON.parse(req.responseText);
                    this.addPersonalPost(uPost);
                }
                catch(e)
                {
                    throw e;
                }
            }
        }
    },

    /*
     * Carica sul sito un nuovo post personale
     */
    CaricaNuovoPost: function(title, text)
    {
        // elimina i trailing space
        title = title.trim();
        text = text.trim();
        if(title.length == 0 || text.length == 0)
        {
            throw "InvalidArgumentException";
        }

        var req = new XMLHttpRequest();

        var data = new FormData();
        data.append("title", title);
        data.append("text", text);

        req.onreadystatechange = (e) => this.requestHandler(e);
        req.open("POST", "../api/UploadPersonalPost.php");
        req.send(data);

        // per debugging
        return req;
    },

    /*
     * Scarica l'elenco dei post appartenenti alla propsia pagine utente.
     */
    GetPersonalPosts: function()
    {
        var req = new XMLHttpRequest();

        //req.open("GET", "../api/GetPersonalPost.php");
        req.open("GET", "api/GetPersonalPost.php");
        req.setRequestHeader("Content-type", "application/json");

        req.send();

        // questo serve solo per i test
        return req;
    },

    /*
     * Funzione invocata quando l'utente richiede di caricare un nuovo post
     * @returns {undefined}
     */
    preparaInserimento: function()
    {
        var parent = this.button_holder.parentElement;
        parent.replaceChild(this.poster_holder, this.button_holder);
    },

    /*
     * Funzione che viene invocata ogni qual volta l'utente prova
     * a inviare un post "vuoto", ovvero composto solo di caratteri non visibili.
     * 
     * @param {type} e
     * @returns {undefined}
     */
    invisibleInputError: function()
    {

    },

    /*
     * Funzione invocata quando l'utente prova a inviare un nuovo post
     * @param {type} e
     * @returns {undefined}
     */
    confermaInserimento: function(e) {
        var form = e.target;
        //throw "NotImplementedException";
        // qui bisognerà inserire tutta la logica di gestione dell'upload del nuovo
        // messaggio

        for(var b of form.querySelectorAll("input.button"))
            b.disabled = true;

        var title = form.title.value;
        var content = form.content.value;

        // attiva effettivamente l'upload
        try{
            this.CaricaNuovoPost(title, content);
        }
        catch(exc) { // nel caso il problema siano le stringhe vuote
            this.invisibleInputError();
            return;
        }
        finally {
            e.preventDefault();
        }

        // Il form di inserimento viene eliminato comunque
        //var parent = this.poster_holder.parentElement;
        //parent.replaceChild(this.button_holder, this.poster_holder);

        // ora la form va effettivamente svuotata
        // invoca l'evento submit sulla form
        form.reset();
    },

    /*
     * Funzione invocata quando l'utente decide di annullare l'invio di un nuovo post
     * @param {type} e
     * @returns {undefined}
     */
    annullaInserimento: function(e) {
        var form = e.target;
        for(var b of form.querySelectorAll("input.button"))
            b.disabled = false;
        form.reset();
        var parent = this.poster_holder.parentElement;
        parent.replaceChild(this.button_holder, this.poster_holder);
    },

    inizializzaBottoniCancellazionePost: function()
    {
        var bottoni = window.document.getElementsByName("bottoneCancellazione");
        for(var b of bottoni) {
            b.addEventListener("click", (e) => this.APICancellazionePost(e));
        }
    },

    /*
     * Inizializza tutto
     */
    OLD_init: function()
    {   
        this.inizializzaBottoniCancellazionePost();

        this.button_holder = window.document.getElementById("addPost");
        this.button_holder.addEventListener("click", () => this.preparaInserimento());

        this.zona_inserimento = this.button_holder.parentElement;

        /*
         * Prepara il form di inserimento per i nuovi post
         */
        this.poster_holder = window.document.createElement("div");
        this.poster_holder.setAttribute("id", "inserimento");
        //this.poster_holder.style.width = "100%";
        //this.poster_holder.style.maxHeight = "7cm";
        //this.poster_holder.style.textAlign = "left";
        var form = window.document.createElement("form");
        this.poster_holder.appendChild(form);
        form.id = "NuovoPost";

        var dataDiv = window.document.createElement("div");
        dataDiv.classList.add("contenuto");

        form.appendChild(dataDiv);
        
        /*
         * Per l'inserimento del titolo del nuovo post
         */
        var title_div = window.document.createElement("div");
        title_div.classList.add("textDiv");
        var title_label = window.document.createElement("label");
        title_div.appendChild(title_label);

        var title = window.document.createElement("input");
        title_div.appendChild(title);
        title_label.textContent = "Titolo del post: ";
        
        title.addEventListener("change", (e) => {
            var input = e.target;
            input.value = input.value.trim();
            var value = input.value;
            console.log("title: \"" + value + '"');
        });

        title.setAttribute("required", ""); // richiesto

        title.setAttribute("id", "NPtitle");
        title.setAttribute("name", "title");
        title.setAttribute("type", "text");
        title_label.setAttribute("for", "NPtitle");
        dataDiv.appendChild(title_div);

        //form.appendChild(window.document.createElement("br"));
        /*
         * Per l'inserimento del contenuto del post
         */
        // Label guida
        var text_div = window.document.createElement("div");
        text_div.classList.add("textDiv");
        var text_label = window.document.createElement("label");
        text_label.textContent = "Contenuto del post:";
        text_div.appendChild(text_label);
        // Spazio per il testo
        text_div.appendChild(window.document.createElement("br"));
        var text = window.document.createElement("textarea");
        text.setAttribute("required", ""); // richiesto
        text.setAttribute("name", "content");
        /*text.style.height = "5cm";
        text.style.width = "95%";
        text.style.resize = "none";*/

        text_div.appendChild(text);
        dataDiv.appendChild(text_div);

        text.addEventListener("change", (e) => {
            var textarea = e.target;
            textarea.value = textarea.value.trim();
            var value = textarea.value;
            console.log("title: \"" + value + '"');
        });

        //form.appendChild(window.document.createElement("br"));

        var buttons_div = window.document.createElement("div")
        buttons_div.classList.add("buttons");
        //buttons_div.style.height = "1.3cm";

        var sub = window.document.createElement("input");
        sub.type = "submit";
        sub.value = "Invia";
        sub.classList.add("button");
        //sub.style.width = "50%";
        //sub.style.height = "0.95cm";
        var res = window.document.createElement("input");
        res.type = "reset";
        res.value = "Annulla";
        res.classList.add("button");
        //res.style.width = "50%";
        //res.style.height = "0.95cm";

        buttons_div.appendChild(res);
        buttons_div.appendChild(sub);

        form.appendChild(buttons_div);

        form.addEventListener("reset", (e) => this.annullaInserimento(e));
        form.addEventListener("submit", (e) => this.confermaInserimento(e));

    },
    
    /*
     * Nuova versione - con una table
     *
    init: function()
    {   
        this.button_holder = window.document.getElementById("addPost");
        this.button_holder.addEventListener("click", () => this.preparaInserimento());

        this.zona_inserimento = this.button_holder.parentElement;

        // Prepara il form di inserimento per i nuovi post
         
        var creaForm = () => {
            this.poster_holder = window.document.createElement("div");
            this.poster_holder.setAttribute("id", "inserimento");
            //this.poster_holder.style.width = "100%";
            //this.poster_holder.style.maxHeight = "7cm";
            //this.poster_holder.style.textAlign = "left";
            var form = window.document.createElement("form");
            this.poster_holder.appendChild(form);
            return form;
        }
        var form = creaForm();
        
        /*this.poster_holder = window.document.createElement("div");
        this.poster_holder.setAttribute("id", "inserimento");
        this.poster_holder.style.width = "100%";
        this.poster_holder.style.maxHeight = "7cm";
        this.poster_holder.style.textAlign = "left";
        this.poster_holder.appendChild(form);
        
        var creaTable = () => {
            var table = window.document.createElement("table");
            
            var creaTHead = () => {
                var thead = table.createTHead();
                var tr = thead.insertRow();
                var th = window.document.createElement("th");
                tr.appendChild(th);
                th.rowspan = 2;
                th.textContent = "Inserisci i dati per un nuovo post";
                return thead;
            };
            creaTHead();
            
            // Per l'inserimento del titolo del nuovo post
            var creaSpazioTitolo = () => {
                var tbody = table.createTBody()
                var tr = tbody.insertRow();
                var th = window.document.createElement("th");
                tr.appendChild(th);
                
                var title_label = window.document.createElement("label");
                th.appendChild(title_label);

                var title = window.document.createElement("input");
                tr.insertCell().appendChild(title);
                title_label.textContent = "Titolo del post: ";
                title.setAttribute("required", ""); // richiesto
                title.id = "NPtitle";
                title.name = "title";
                title.type = "text";
                title_label.for = "NPtitle";

                return tbody;
            };
            creaSpazioTitolo();
            
            // Per l'inserimento del contenuto del post
            var creaSpazioTesto = () => {
                var tbody = table.createTBody()
                var tr = tbody.insertRow();
                var th = window.document.createElement("th");
                th.rowspan = 2;
                tr.appendChild(th);
                
                var text_label = window.document.createElement("label");
                th.appendChild(text_label);
                text_label.textContent = "Contenuto del post:";
                
                var text = window.document.createElement("textarea");
                text.required = true; // richiesto
                text.name = "content";
                //text.style.height = "5cm";
                //text.style.width = "95%";
                //text.style.resize = "none";

                var td = tbody.insertRow().insertCell();
                td.rowspan = 2;
                td.appendChild(text);

                return tbody;
            };
            creaSpazioTesto();
            
            var creaSpazioBottoni = () => {
                var tfoot = table.createTFoot();
                var tr = tfoot.insertRow();
                
                var creaBottoneForm = (type, value) => {
                    var but = window.document.createElement("input");
                    but.type = type;
                    but.value = value;
                    but.classList.add("button");
                    //but.style.width = "50%";
                    //but.style.height = "0.95cm";
                    return but;
                };
                
                var sub = creaBottoneForm("submit", "Invia");
                var res = creaBottoneForm("reset","Annulla");
                
                tr.insertCell().appendChild(sub);
                tr.insertCell().appendChild(res);
                return tfoot;
            };
            creaSpazioBottoni();
            
            return table;
        }
        
        var table = creaTable();
        form.appendChild(table);
        
        form.addEventListener("reset", (e) => this.annullaInserimento(e));
        form.addEventListener("submit", (e) => this.confermaInserimento(e));

    }*/
};

window.addEventListener("load", () => POST.OLD_init());