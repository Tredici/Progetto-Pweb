/* 
 * Metto qui gli oggetti per controllare l'organizzazione dei nomi dove si segnano le partite
 * Di fatto sono solo cose per migliorare l'interazione, non strettamente necessarie
 */


function cPartita(partita)
{
    this.giocatori = partita.getElementsByTagName("select");
    if(this.giocatori.length != 4)
    {
        console.error("Dovrebbero esserci 4 spazi per i giocatori!");
        throw new Error("FatalErrorException");
    }
    var player_checker = (e) =>{
        var select = e.target;
        var newVal = select.value;
        var oldVal = select.dataset.old;
        var toUpdate = undefined;
        for(var el of this.giocatori)
        {
            if(el.dataset.old == newVal)
            {
                toUpdate = el;
                break;
            }
        }
        if(!toUpdate)
        {
            console.error("Qualcosa non va nell'aggiornamento!");
            throw new Error("FatalErrorException");
        }
        toUpdate.value = oldVal;
        toUpdate.dataset.old = oldVal;
        select.dataset.old = newVal;
    };
    for(var i=0; i!=4; ++i)
    {
        this.giocatori[i].value = this.giocatori[i].options[i].value;
        this.giocatori[i].dataset.old = this.giocatori[i].value;
        this.giocatori[i].addEventListener("change", player_checker);
    }

    this.risultato = partita.getElementsByTagName("input");
    if(this.risultato.length != 2)
    {
        console.error("Dovrebbero esserci 2 spazi per i risultati!");
        throw new Error("FatalErrorException");
    }
    
    if(!!UTILITY_REGISTRAZIONE_PARTITE)
    {
        var result_checker = (e) => {
            var S1 = this.risultato[0].value;
            var S2 = this.risultato[1].value;
            if("" === S1 || "" === S2 ||
                UTILITY_REGISTRAZIONE_PARTITE.checkResult(S1,S2)) {
                this.risultato[0].classList.remove("warning");
                this.risultato[1].classList.remove("warning");
            } else {
                this.risultato[0].classList.add("warning");
                this.risultato[1].classList.add("warning");
            }
        };
        for(var el of this.risultato)
        {
            el.addEventListener("change", result_checker);
        }
    } else {
        console.warn("File di utiliti assente!");
    }
};