"use strict";

var REQ = {
    /**
     * Comment
     */
    button_feedback: undefined,
    
    initFeedback: function() {
        var request_form = window.document.getElementById("request_form");
        var div_form = request_form.parentNode;

        var div_feedback = window.document.createElement("div");
        div_feedback.setAttribute("id", "div_feedback");
        this.button_feedback = window.document.createElement("button");
        this.button_feedback.classList.add("BottoneAttesa");
        this.button_feedback.disabled = true;

        div_form.replaceChild(this.button_feedback, request_form);

        this.button_feedback.textContent = "Invio richiesta...";
    },

    phase2Feedback: function() {
        this.button_feedback.textContent = "Richiesta inviata...";
    },

    phase3Feedback: function() {
        this.button_feedback.textContent = "Ricezione risposta...";
    },

    /*
     * Si occupa di notificare all'utente che ha provato a fare la richiesta
     *  che tutto è andato bene
     */
    successFeedback: function() {
        var p = window.document.createElement("p");
        var text = "La tua richiesta è stata accolta con successo!";
        var textNode = window.document.createTextNode(text);
        p.appendChild(textNode);
        var newline = window.document.createElement("br");
        p.appendChild(newline);
        text = "Attendi che venga accettata e poi riprova a fare login!";
        textNode = window.document.createTextNode(text);
        p.appendChild(textNode);

        this.button_feedback.classList.add("BottoneSuccesso");

        var buttonParent = this.button_feedback.parentNode;
        buttonParent.insertBefore(p, this.button_feedback);

        this.button_feedback.addEventListener("click", 
                    () => window.location.assign("Login.php")
                    //() => window.location.href = "../php/Login.php"
                            );
        this.button_feedback.textContent = "Ritorna al Login!";
        this.button_feedback.disabled = false;
    },

    /*
     * Informa l'utente che ha provato a fare la richiesta che poteva andato meglio
     */
    failFeedback: function() {
        var p = window.document.createElement("p");
        var text = "OOOPS! La tua richiesta è stata respinta!";
        var textNode = window.document.createTextNode(text);
        p.appendChild(textNode);
        var newline = window.document.createElement("br");
        p.appendChild(newline);
        text = "Sei sicuro di non averne già fatta una in passato? Controlla e se non è così e il problema persiste contatta gli amministratori!";
        textNode = window.document.createTextNode(text);
        p.appendChild(textNode);

        this.button_feedback.classList.add("BottoneFallimento");

        var buttonParent = this.button_feedback.parentNode;
        buttonParent.insertBefore(p, this.button_feedback);

        this.button_feedback.addEventListener("click", 
                    () => window.location.assign("Login.php")
                    //() => window.location.reload()
                            );
        this.button_feedback.textContent = "Ritorna al Login!";
        this.button_feedback.disabled = false;
    },

    /*
     * 
     * @param {readystatechange event} e
     * @returns {undefined}
     */
    requestHandler: function(e)
    {
        var req = e.target;
        if(req.readyState === XMLHttpRequest.OPENED)
        {
            this.initFeedback();
        } else if(req.readyState === XMLHttpRequest.HEADERS_RECEIVED)
        {
            this.phase2Feedback();
        } else if(req.readyState === XMLHttpRequest.LOADING)
        {
            this.phase3Feedback();
        } else if(req.readyState === XMLHttpRequest.DONE)
        {
            if(req.status == 200)
            {
                // Metodo un po' più elaborato
                this.successFeedback();
                console.info("Richiesta caricata.");

                //window.location.reload(); 
                // ricarica così che torni alla pagina principale
                // lo stato della richiesta è salvato sul db
            } else {
                this.failFeedback();
                //throw "FailedRequestException";
            }
        }
    },


    pwdMismatch: function()
    {
        // inventarsi un messaggio di errore se l'utente ha sbagliato
        // a inserire due password

        // per ora mi accontento
        console.log("Attenzione! Le password non coincidono!")
    },

    /**
     * Controlla che la richiesta sia valida (pass1 == pass2)
     * 
     * @argument {HTMLFormElement: submit event} e
     *  L'evento che si genera al momento del submit
     */
    CheckRequest: function(e) {
        e.preventDefault();
        var form = e.target;
        if(form["password"].value !== form["password2"].value)
        {
            this.pwdMismatch();
            return;
        }

        // Inizio parte feedback ***************************************************
        // Spostato nell'handler sopra 
        //initFeedback();
        // Fine parte feedback *****************************************************

        var req = new XMLHttpRequest();
        req.onreadystatechange = (e) => this.requestHandler(e);

        var data = new FormData();
        var name = form.name.value;
        data.append("name", name);
        var surname = form.surname.value;
        data.append("surname", surname);
        var username = form.username.value;
        data.append("username", username);
        var email = form.email.value;
        data.append("email", email);

        var password = form.password.value;
        data.append("password", password);

        var birthday = form.birthday.value;
        data.append("birthday", birthday);

        var sex = form.sex.value;
        data.append("sex", sex);

        var note = form.note.value;
        if(!!note)
            data.append("note", note);
        //req.open("POST", "../api/UploadSubscriptionRequest.php");
        req.open("POST", "../api/UploadSubscriptionRequest.php");
        //req.setRequestHeader("Content-Type", "text/plain;charset=UTF-8");
        req.send(data);

        // per debugging
        return req;
    },

    init: function()
    {
        var form = document.getElementById("request_form");
        form.addEventListener("submit", (e) => this.CheckRequest(e));
    }
};
    
window.addEventListener("load", () => REQ.init());
