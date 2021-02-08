"use strict";

var UTILITY_REGISTRAZIONE_PARTITE = {

    /*
     * Verifica che il risultato della partita
     * sia valido:
     *  -true: il risultato è valido
     *  -false: il risultato non è valido
     *  
     * 
     * @param {int} S1
     * @param {int} S2
     * @returns {Boolean}
     */
    checkResult: function(S1, S2)
    {
        return !(Math.min(S1,S2)<0 || 
            Math.max(S1,S2)<6 || 
            Math.abs(S1-S2) < 2 ||
            (Math.max(S1,S2) > 6 && Math.abs(S1-S2) != 2)
        );
    },

    /**
     * 
     * @param {string} G1 
     * @param {string} G2 
     * @param {string} G3 
     * @param {string} G4 
     * 
     * @param {function} callback 
     */
    checkPlayers: function(G1, G2, G3, G4, callback)
    {
        if(arguments.length <4)
            throw new Error("MissingArgumentsException");

        var req = new XMLHttpRequest();

        var data = new FormData();
        data.append("G1",G1);
        data.append("G2",G2);
        data.append("G3",G3);
        data.append("G4",G4);

        if(callback) req.onreadystatechange = callback;

        req.open("POST", "../api/CheckPlayers.php");

        req.send(data);
        return req;
    },
    
    /*
     * Per confermare che il risultato di un annuncio segnato sia quello giusto
     * 
     * @argument {int} group
     * @argument {callback} callback
     */
    confermaEsito: function(group, callback)
    {
        var req = new XMLHttpRequest();

        var data = new FormData();
        data.append("group",group);

        if(callback) req.onreadystatechange = callback;

        req.open("POST", "../api/ConfirmGroup.php");

        req.send(data);
        return req;
    },
    
    /*
     * Per confermare che il risultato di un annuncio segnato sia quello giusto
     * 
     * @argument {int} group
     * @argument {callback} callback
     */
    sconfessaEsito: function(group, callback)
    {
        var req = new XMLHttpRequest();

        var data = new FormData();
        data.append("group",group);

        if(callback) req.onreadystatechange = callback;

        req.open("POST", "../api/RejectGroup.php");

        req.send(data);
        return req;
    },
    
    /*
     * Questa funzione serve ad assicurarsi che l'esito di una partita indicato
     *  sia valido.
     *  
     * Richiede come argomento un riferimento al TBody che contiene i dati 
     *  sull'incontro e vi ritorna un controllore che si occupa di verificare,
     *  in seguito all'invocazione di un metodo "check" che tutto sia apposto:
     *      - i giocatori siano tutti diversi e il punteggio sia valido
     *  Il metodo restituirà true se tutto è ok e false altrimenti.
     *  Potrebbe far comparire anche un messaggio di errore in fondo all'elemento
     *  
     */
    controlloDatiIncontro: function(tbody)
    {
        function matchController(tbody)
        {
            if(tbody.tagName !== "TBODY")
            {
                console.error("Serve un TBODY");
                throw new Error("Serve un TBODY");
            }
            
            this.tbody = tbody;
            this.selettori = tbody.getElementsByTagName("select");
            if(this.selettori.length != 4)
            {
                throw new Error("Manca lo spazio per segnare i giocatori");
            }
            
            this.risultati = tbody.getElementsByTagName("input");
            if(this.risultati.length != 2)
            {
                throw new Error("Manca lo spazio per segnare i punteggi");
            }
        }
            
        matchController.prototype.check = function(){
            var ans = true;
            // controllo sui giocatori
            var usr = new Set();
            for(var el of this.selettori)
                if(el.value)
                    usr.add(el.value);
            if(usr.size != 4) {
                ans = false;
                console.warn("Giocatori duplicati");
                for(var el of this.selettori)
                    el.classList.add("warning")
            } else {
                for(var el of this.selettori)
                    el.classList.remove("warning")
            }
            // controllo sui risultati
            var S1 = this.risultati[0].value;
            var S2 = this.risultati[1].value;
            /*
             * I punteggi debbono essere positivi, almeno uno deve raggiungere 6
             *  la differenza tra i punteggi deve essere almeno 2 punti di 
             *  scarto
             */
            if(!UTILITY_REGISTRAZIONE_PARTITE.checkResult(S1,S2))
            {
                this.risultati[0].value = "";
                this.risultati[1].value = "";
                ans = false;
                console.warn("Punteggi inconsistenti");
                for(var el of this.risultati)
                    el.classList.add("warning")
            } else {
                for(var el of this.risultati)
                    el.classList.remove("warning")
            }

            return ans;
        }
        
        /*
        * Da usare nel caso in cui si voglia registrare una partita singola
        * 
        * @param {DOMElement} tbody
        *      :prende un tbody che contiene i dati di una partita
        *      
        *  @return {object}
        *      :ottiene i dati sulla partita assegnati ai campi
        *      {
        *          S1G1
        *          S1G2
        *          S2G1
        *          S2G2
        *          PunteggioS1
        *          PunteggioS2
        *      }
        */
       matchController.prototype.ottieniDatiPartitaSingola = function()
       {
            var ans = {};
            ans.S1G1 = this.selettori[0].value;
            ans.S1G2 = this.selettori[2].value;
            ans.S2G1 = this.selettori[1].value;
            ans.S2G2 = this.selettori[3].value;
           
            ans.PunteggioS1 = this.risultati[0].value;
            ans.PunteggioS2 = this.risultati[1].value;
           
            return ans;
       };
        
        return new matchController(tbody);
    },
    
    
    /*
     * Nel caso della registrazione dei trittici vengono inseriti i dati
     *  di 3 partite e bisogna verificarne poi la consistenza, oltreché 
     *  restituire una struttura dati, di fatto un oggetto, che ne riassuma
     *  le informazioni in modo che possano essere inviate al server 
     *  chiamando "registraTrittico" sotto
     *  
     *  @argument {ret matchController.prototype.ottieniDatiPartitaSingola} P1
     *  @argument {ret matchController.prototype.ottieniDatiPartitaSingola} P2
     *  @argument {ret matchController.prototype.ottieniDatiPartitaSingola} P3
     *      : si assume che i dati ricevuti siano consistenti
     *      
     *  @return {object} - on success
     *      :riassume i dati sull'esito del trittico
     *      {
     *          G1
     *          G2
     *          G3
     *          G4
     *          PunteggioP1S1
     *          PunteggioP1S2
     *          
     *          PunteggioP2S1
     *          PunteggioP2S2
     *          
     *          PunteggioP3S1
     *          PunteggioP3S2
     *      }
     *  @return {null} - on fail
     *      :se vengono le partite indicate non rappresentano tutte le possibili
     *       combinazioni da seguire nel trittico
     *  
     *  @throws {Error}
     *      : nel caso di dati inconsistenti 
     *  
     */
    ottieniDatiTrittico: function(P1, P2, P3)
    {
        /*
         * Controlla che due Set siano uguali
         *  assume che siano della stessa dimensione
         */
        var equals = (S1, S2) => {
            for(var el in S2)
                if(!S1.has(el))
                    return false;
            return true;
        };
        /*
         * Ritorna il codice del compagno di squadra del 
         *  giocatore G estraendolo dall'argomento G
         */
        var compagnoG = (P,G) => {
            if(P.S1G1 === G)
                return P.S1G2;
            else if(P.S1G2 === G)
                return P.S1G1;
            else if(P.S2G1 === G)
                return P.S2G2;
            else if(P.S2G2 === G)
                return P.S2G1;
            throw new Error("Succedono cose brutte");
        };
        /*
         * Ottiene il punteggio della squadra del giocatore
         */
        var punteggioG = (P,G) => {
            if(P.S1G1 === G || P.S1G2 === G)
                return P.PunteggioS1;
            else if(P.S2G1 === G || P.S2G2 === G)
                return P.PunteggioS2;
            throw new Error("Succedono cose brutte");
        };
        /*
         * Ottiene il punteggio degli avversari
         */
        var punteggioAvversariG = (P,G) => {
            if(P.S1G1 === G || P.S1G2 === G)
                return P.PunteggioS2;
            else if(P.S2G1 === G || P.S2G2 === G)
                return P.PunteggioS1;
            throw new Error("Succedono cose brutte");
        };
        
        // giocatori partita
        var giocatori = (P) => new Set().add(P.S1G1).add(P.S1G2).add(P.S2G1).add(P.S2G2);
        var GP1 = giocatori(P1);
        var GP2 = giocatori(P2);
        if(!equals(GP1, GP2))
        {
            console.error("ottieniDatiTrittico: P1 e P2 inconsistenti");
            throw new Error("InvalidArguments");
        }            
        var GP3 = giocatori(P3);
        if(!equals(GP1, GP3))
        {
            console.error("ottieniDatiTrittico: P1 e P3 inconsistenti");
            throw new Error("InvalidArguments");
        }
        
        var ans = {
            G1: P1.S1G1,
            G2: P1.S1G2,
            G3: compagnoG(P2, P1.S1G1),
            G4: compagnoG(P3, P1.S1G1),
            
            PunteggioP1S1: P1.PunteggioS1,
            PunteggioP1S2: P1.PunteggioS2,
            
            PunteggioP2S1: punteggioG(P2, P1.S1G1),
            PunteggioP2S2: punteggioAvversariG(P2, P1.S1G1),
            
            PunteggioP3S1: punteggioG(P3, P1.S1G1),
            PunteggioP3S2: punteggioAvversariG(P3, P1.S1G1)
        };
        
        // se non vengono fatte tutte e 3 le possibili combinazioni
        // ritorna null
        if(new Set().add(ans.G1).add(ans.G2)
            .add(ans.G3).add(ans.G4).size != 4)
        {
            console.error("Non sono state provate tutte le combinazioni del trittico");
            return null;
        }
        else return ans;
    },
    
    /*
     * Chiama l'API per registrare una partita singola gestendo tutta la 
     *  richiesta HTTP dietro, in seguito, a seconda del risultato, invoca una 
     *  delle due callback fornite come parametri
     * 
     * @param {callback} success
     *  
     * @param {callback} error
     */
    registraPartitaSingola: function(
            S1G1, S1G2, S2G1, S2G2,
            PunteggioS1, PunteggioS2, 
            annuncio,
            success, error)
    {
        if(!success) success = () => console.info("Partita singola registrata");
        if(!error) error = () => console.error("Errore registrazione partita singola");
        
        if(arguments.length < 6)
        {
            console.error("Chiamata 'registraPartitaSingola' senza abbastanza argomenti");
            throw new Error("Missing arguments");
        }
        
        var data = new FormData();
        
        // dati giocatori
        data.append("S1G1", S1G1);
        data.append("S1G2", S1G2);
        data.append("S2G1", S2G1);
        data.append("S2G2", S2G2);
        // esito partita
        data.append("PunteggioS1", PunteggioS1);
        data.append("PunteggioS2", PunteggioS2);
        
        // eventualmente salva l'annuncio
        if(annuncio) data.append("annuncio", annuncio);
        
        var req = new XMLHttpRequest();
        req.onreadystatechange = (e) => {
            var req = e.target;
            if(req.readyState === XMLHttpRequest.DONE)
            {
                if(req.status == 200)  {
                    success();
                } else {
                    error();
                }
            }
        };
        
        req.open("POST", "../api/RegisterSingleMatch.php");
        req.send(data);
        return req;
    },
    
    /*
     * Semplifica la registrazione di partite singole
     *  risparmiando di dover chiamare direttamente
     *  "registraPartitaSingola" permettendo di utilizzare
     *  come parametro l'oggetto ritornato da
     *          matchController.ottieniDatiPartitaSingola
     */
    registraPartitaSingola_Oggetto: function(
            PartitaObj,
            annuncio,
            success, error)
    {
        return this.registraPartitaSingola(
            PartitaObj.S1G1, PartitaObj.S1G2, 
            PartitaObj.S2G1, PartitaObj.S2G2,
            PartitaObj.PunteggioS1, PartitaObj.PunteggioS2, 
            annuncio,
            success, error);
    },
    
    
    /*
     * Simile a sopra, invoca l'API per registrare i trittici.
     * 
     * @param {callback} success
     *  
     * @param {callback} error
     */
    registraTrittico: function(
            G1, G2, G3, G4,
            PunteggioP1S1, PunteggioP1S2,
            PunteggioP2S1, PunteggioP2S2,
            PunteggioP3S1, PunteggioP3S2,
            annuncio,
            success, error)
    {
        if(!success) success = () => console.info("Trittico registrato");
        if(!error)   error = () => console.error("Errore registrazione registrato");
        
        if(arguments.length < 8)
        {
            console.error("Chiamata 'registraTrittico' senza abbastanza argomenti");
            throw new Error("Missing arguments");
        }
        
        var data = new FormData();
        
        // dati giocatori
        data.append("G1", G1);
        data.append("G2", G2);
        data.append("G3", G3);
        data.append("G4", G4);
        // esito partita 1
        data.append("PunteggioP1S1", PunteggioP1S1);
        data.append("PunteggioP1S2", PunteggioP1S2);
        // esito partita 2
        data.append("PunteggioP2S1", PunteggioP2S1);
        data.append("PunteggioP2S2", PunteggioP2S2);
        // esito partita 3
        data.append("PunteggioP3S1", PunteggioP3S1);
        data.append("PunteggioP3S2", PunteggioP3S2);
        
        // eventualmente salva l'annuncio
        if(annuncio) data.append("annuncio", annuncio);
        
        var req = new XMLHttpRequest();
        req.onreadystatechange = (e) => {
            var req = e.target;
            if(req.readyState === XMLHttpRequest.DONE)
            {
                if(req.status == 200)  {
                    success();
                } else {
                    error();
                }
            }
        };
        
        req.open("POST", "../api/RegisterTripleMatch.php");
        req.send(data);
        return req;
    },
    
    /*
     * Corrispondente a registraPartitaSingola_Oggetto ma per
     *  registrare i trittici.
     * 
     * Permette di utilizzare come argomento l'oggetto restituito
     * da
     *          this.ottieniDatiTrittico
     * e semplifica l'utilizzo di
     *          this.registraPartitaSingola
     */
    registraTrittico_Oggetto: function(
            tritticoObj,
            annuncio,
            success, error)
    {
        return this.registraTrittico(
            tritticoObj.G1, tritticoObj.G2,
            tritticoObj.G3, tritticoObj.G4,
            
            tritticoObj.PunteggioP1S1, tritticoObj.PunteggioP1S2,
            tritticoObj.PunteggioP2S1, tritticoObj.PunteggioP2S2,
            tritticoObj.PunteggioP3S1, tritticoObj.PunteggioP3S2,
    
            annuncio,
            success, error);
    },
    
}