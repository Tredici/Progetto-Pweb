"use strict"

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/*
 * Questo oggetto va visto come un semplice namespace, ecco perché vanno bene le
 *  funzioni freccia
 */

var PARTITE = {
    /*
     * Scarica l'elenco delle partite associate all'utente usr, o a quello 
     *  corrente se usr non viene fornito o vale 0
     *  
     * La callback viene fornita dal chiamante
     */
    ottieniDatiPartite: function(usr, callback, error) {
        var req = new XMLHttpRequest();
        var data = new FormData();
        
        if(usr) data.append("userid", data);
        
        if(!callback)   callback    = ()=>{};
        if(!error)      error       = ()=>{};
        
        req.onreadystatechange = (e) =>{
            var req = e.target;
            if(req.readyState  == XMLHttpRequest.DONE)
            {
                if(req.status == 200)
                {
                    try {
                        var matches = JSON.parse(req.responseText);
                        callback(matches);
                    } catch (e) {
                        error(req);
                    }
                }
                else
                    error(req);
            }
        }
        
        req.open("POST", "../api/GetConfirmedMatches.php");
        req.send(data);
        return req;
    },
    
    /*
     * Costruisce la tabella con i risultati delle partite
     * @param {Partite} matches
     * @returns {undefined}
     */
    presentaDati: function(matches) {
        var div = window.document.getElementById("segnaposto");
        
        var container = window.document.createElement("div");
        container.id = "Partite";
        //container.classList.add("container");
        
        var table = window.document.createElement("table");
        container.appendChild(table);
        
        table.classList.add("partite");
        
        table.createCaption().textContent = "Le tue partite"
        var TRH = table.createTHead().insertRow();
        var th = window.document.createElement("th");
        TRH.appendChild(th);
        th.colSpan = 2; //th.rowspan = 2;
        th.textContent = "Squadra 1";
        th.classList.add("s1");

        th = window.document.createElement("th");
        TRH.appendChild(th);
        th.colSpan = 2; //th.rowspan = 2;
        th.textContent = "Squadra 2";
        th.classList.add("s2");

        th = window.document.createElement("th");
        TRH.appendChild(th);

        th.textContent = "Risultato";
        th.classList.add("risultato");
        th.colSpan = 2;

        var MapPartite = new Map();
        for(var p of matches)
        {
            if(MapPartite.has(p.Gruppo)) {
                var tbody = MapPartite.get(p.Gruppo);
            } else {
                var tbody = table.createTBody();
                tbody.dataset.gruppo = p.Gruppo;
            }
            var tr = tbody.insertRow();
            
            tr.dataset.partita = p.Codice;
                
            var inser_player = (codice, nome) => {
                var td = tr.insertCell();
                var a = window.document.createElement("a");
                td.appendChild(a);
                a.href = "PaginaPersonale.php?userid=" + String(codice);
                a.text = nome;
                return td;
            };
            inser_player(p["S1G1"], p["S1G1name"]).classList.add("s1");
            inser_player(p["S1G2"], p["S1G2name"]).classList.add("s1");
            inser_player(p["S2G1"], p["S2G1name"]).classList.add("s2");
            inser_player(p["S2G2"], p["S2G2name"]).classList.add("s2");
            
            if(this.userid == p.S1G1 || this.userid == p.S1G2)
            {
                if(p.PunteggioSquadra1 > p.PunteggioSquadra2)
                    tr.classList.add("vittoria");
                else
                    tr.classList.add("sconfitta");
            } else {
                if(p.PunteggioSquadra1 < p.PunteggioSquadra2)
                    tr.classList.add("vittoria");
                else
                    tr.classList.add("sconfitta");
            }
            
            var td = tr.insertCell();
            td.classList.add("risultato");
            td.textContent = p["PunteggioSquadra1"];
            td = tr.insertCell();
            td.classList.add("risultato");
            td.textContent = p["PunteggioSquadra2"];
        }

        div.parentNode.replaceChild(container, div);
    },
    
    /*
     * Se l'utente non ha giocato alcuna partita mette un div che lo avverte
     */
    nessunDato: () => {
        var div = window.document.getElementById("segnaposto");
        
        var nullDiv = window.document.createElement("div");
        nullDiv.id = "NoPartite";
        var span = window.document.createElement("span");
        nullDiv.appendChild(span);
        var text = window.document.createTextNode("Non hai partite registrate!")
        span.appendChild(text);

        var br = window.document.createElement("br");
        nullDiv.appendChild(br);

        span = window.document.createElement("span");
        nullDiv.appendChild(span);
        text = window.document.createTextNode("Corri a impiegare bene il tuo tempo!")
        span.appendChild(text);
        
        div.parentNode.replaceChild(nullDiv, div);
    },
    
    userid: undefined,

    init: function()
    {
        this.userid = parseInt(window.document.getElementById("container").dataset.me);

        console.log("PARTITE.init");
        var error = () => window.location.assign("Error.php");
        var success = (matches) => {
            if(matches.length) this.presentaDati(matches)
            else this.nessunDato();
        };
        this.ottieniDatiPartite(null, success, error);
    }
}

window.addEventListener("load", () => PARTITE.init());

