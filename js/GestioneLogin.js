"use strict";

var LOGIN = {
    
    formLogin: undefined,
    divLogin: undefined,
    
    disabilitaForm: function()
    {
        var buttons = this.divLogin.getElementsByClassName("messaggioErrore");
        for(var b of buttons)
            b.disabled = true;
    },
    
    /**
     * gestione della richiesta di login
     * @param {readystatechangeEvent} e
     */
    requestHandler: function(e)
    {
        var req = e.target;
        if(req.readyState == 4) {
            if(req.status == 200) {
                window.location.assign("../index.php");
            } else if(req.status == 403) {
                // Problema credenziali
                var errore = window.document.createElement("div");
                errore.id = "messaggioErrore";
                this.divLogin.appendChild(errore);
                errore.classList.add("messaggioErrore");
                
                var newline = window.document.createElement("br");
                var p = window.document.createElement("p");
                errore.appendChild(p);
                
                // inizia a scrivere un generico messaggio di errore
                var text = "Login fallito!";
                var textEl = window.document.createTextNode(text);
                p.appendChild(textEl);
                
                p.appendChild(newline);
                text = "Attenzione: " + req.responseText;
                textEl = window.document.createTextNode(text);
                p.appendChild(textEl);
            } else {
                console.error("Qualcosa di imprevisto è appena accaduto!");
                window.location.assign("Error.php");
                throw new Error("FatalErrorException");
            }
        }
    },
    
    /**
     * gestione del sumbit
     * @param {submitEvent} e
     */
    submitHandler: function(e)
    {
        e.preventDefault();
        var form = e.target;
        
        this.disabilitaForm();
        
        var username = form.username.value;
        var password = form.password.value;
        
        var data = new FormData();
        data.append("username", username);
        data.append("password", password);
        
        var req = new XMLHttpRequest();
        req.onreadystatechange = (e) => this.requestHandler(e);
        req.open("POST", "../api/Autentication.php");
        req.send(data);
    },

    ripulituraErrore: function()
    {
        var pulitore = () => {
            var divErrore = window.document.getElementById("messaggioErrore");
            if(divErrore) divErrore.parentNode.removeChild(divErrore);
        };
        for(var el of ["usr", "pass"])
            window.document.getElementById(el).addEventListener("change", pulitore);
    },
    
    init: function()
    {
        this.ripulituraErrore();

        this.formLogin = window.document.getElementById("formLogin");
        this.divLogin = window.document.getElementById("divLogin");
        
        this.formLogin.addEventListener("submit", (e) => this.submitHandler(e));
    }
};


window.addEventListener("load", () => LOGIN.init());
