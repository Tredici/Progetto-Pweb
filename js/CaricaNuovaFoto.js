"use strict";
/* 
 * Serve a permettere a un utente di cambiare la sua foto profilo
 */

var FOTO = {
    /*
     * Riferimento alla form per inserire una nuova foto profilo
     */
    form_upload_foto: undefined,

    status_button: undefined,
    
    //eventClick: (e) => this.ClickFotoProfilo(e),
    eventClick: undefined,
    
    /**
     * Comment
     * @param {type} parameter
     */
    uploadHandler: function(e) {
        var req = e.target;

        if(req.readyState == 1) {
            var form_container = this.form_upload_foto.parentNode;
            this.status_button = window.document.createElement("div");
            form_container.replaceChild(this.status_button, this.form_upload_foto);
            var span = window.document.createElement("span");
            this.status_button.appendChild(span);
            span.textContent = "Invio richiesta...";
            this.status_button.id = "caricamentoFoto";
            //this.status_button.disabled = true;

        } else if(req.readyState == 2) {
            this.status_button.textContent = "Ricezione risposta...";
        } else if(req.readyState == 4) {
            if(req.status == 200){
                this.status_button.textContent = "Fatto!";
                var button_container = this.status_button.parentNode;

                var foto = window.document.getElementById("FotoProfilo");
                foto.addEventListener("click", this.eventClick);
                // forza il caricamento della nupva immagine
                foto.src = foto.src;
                button_container.removeChild(this.status_button);
                //window.location.reload();   // modo più semplice per gestire il tutto
            } else {
                var button_container = this.status_button.parentNode;
                button_container.removeChild(this.status_button);
                var foto = window.document.getElementById("FotoProfilo");
                foto.addEventListener("click", this.eventClick);
                throw "FailedRequestException";
            }
        }
    },

    /**
     * Funzione destinata all'upload della nuovo foto provilo
     */
    uploadNewPic: function(e) {
        e.preventDefault();
        var form = e.target;

        var data = new FormData();
        data.append("personal", "true");
        var file = form.foto.files[0];
        data.append("picture", file);

        var req = new XMLHttpRequest();
        req.onreadystatechange = (e) => this.uploadHandler(e);
        //req.open("POST", "../api/UploadPicture.php");
        req.open("POST", "../api/UploadPicture.php");
        req.send(data);
    },

    ClickFotoProfilo: function(e)
    {
        //if(!e) return;
        var foto = window.document.getElementById("FotoProfilo");
        //if(!foto) throw new Error("FatalErrorException!");
        foto.removeEventListener("click", this.eventClick);

        var contenitore_fotografia = foto.parentNode;
        this.form_upload_foto = window.document.createElement("form");
        this.form_upload_foto.id = "NuovaFoto";
        contenitore_fotografia.insertAdjacentElement('afterend', this.form_upload_foto);

        this.form_upload_foto.addEventListener("submit", (e) => this.uploadNewPic(e));

        var label = window.document.createElement("label");
        this.form_upload_foto.appendChild(label);
        label.textContent = "La tua nuova foto:";

        var file_input = window.document.createElement("input");
        this.form_upload_foto.appendChild(file_input);
        file_input.setAttribute("name", "foto");
        file_input.setAttribute("type", "file");
        file_input.setAttribute("id", "FotoFile");
        file_input.required = true; //setAttribute("required", "");
        file_input.setAttribute("accept", "image/*");

        label.setAttribute("for", "FotoFile");

        // A capo
        var newline = window.document.createElement("br");
        this.form_upload_foto.appendChild(newline);

        // Bottone submit
        var submit = window.document.createElement("input");
        submit.setAttribute("type", "submit");
        submit.setAttribute("value", "Invia!");

        // Bottone annulla
        var bottone_annulla = window.document.createElement("input");
        bottone_annulla.setAttribute("type", "reset");
        bottone_annulla.value = "Annulla!";

        //bottone_annulla.setAttribute("novalidate", "");

        bottone_annulla.addEventListener("click", (e) => {
            e.preventDefault();
            this.form_upload_foto.parentNode.removeChild(this.form_upload_foto);
            foto.addEventListener("click", this.eventClick);
        });

        submit.classList.add("button");
        bottone_annulla.classList.add("button");

        this.form_upload_foto.appendChild(bottone_annulla);
        this.form_upload_foto.appendChild(submit);
    },
    
    NEW_ClickFotoProfilo: function(e)
    {
        //if(!e) return;
        var foto = window.document.getElementById("FotoProfilo");
        //if(!foto) throw new Error("FatalErrorException!");
        foto.removeEventListener("click", this.eventClick);

        var contenitore_fotografia = foto.parentNode;
        this.form_upload_foto = window.document.createElement("form");
        contenitore_fotografia.insertAdjacentElement('afterend', this.form_upload_foto);
        
        var label = window.document.createElement("label");
        label.textContent = "La tua nuova foto:";
        label.setAttribute("for", "FotoFile");
        
        var file_input = window.document.createElement("input");
        file_input.setAttribute("name", "foto");
        file_input.setAttribute("type", "file");
        file_input.setAttribute("id", "FotoFile");
        file_input.required = true; //setAttribute("required", "");
        file_input.setAttribute("accept", "image/*");

        // Bottone submit
        var submit = window.document.createElement("input");
        submit.setAttribute("type", "submit");
        submit.setAttribute("value", "Invia!");

        // Bottone annulla
        var bottone_annulla = window.document.createElement("input");
        bottone_annulla.setAttribute("type", "reset");
        bottone_annulla.value = "Annulla!";

        var table = window.document.createElement("table");
        table.createCaption().textContent = "La tua nuova immagine profilo:";
        var tbody = table.createTBody();
        var tr = tbody.insertRow();
        
        var tfoot = table.createTFoot();
        var tfoot = table.createTFoot();
        
        this.form_upload_foto.appendChild(table);
        

        this.form_upload_foto.appendChild(label);
        this.form_upload_foto.appendChild(file_input);
        this.form_upload_foto.appendChild(submit);
        this.form_upload_foto.appendChild(bottone_annulla);
        
        //bottone_annulla.setAttribute("novalidate", "");
        this.form_upload_foto.addEventListener("submit", (e) => this.uploadNewPic(e));
        this.form_upload_foto.addEventListener("reser",  (e) => {
            e.preventDefault();
            this.form_upload_foto.parentNode.removeChild(this.form_upload_foto);
            foto.addEventListener("click", this.eventClick);
        });

    },

    FormFoto: false,
    init: function(){
        this.eventClick = (e) => this.ClickFotoProfilo(e);
        var foto = window.document.getElementById("FotoProfilo");
        foto.addEventListener("click", this.eventClick);
    }
};


window.addEventListener("load", () => FOTO.init());