<?php
    session_start();
    if(!isset($_SESSION["user"])) {
        header("Location: ../index.php");
        exit;
    }
?>
<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <title>Home</title>
        
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        
        <link rel="icon" href="../logo.bmp"  type="image/bmp">
        
        <link rel="stylesheet" type="text/css" href="../css/StilePrincipale.css">
        <link rel="stylesheet" type="text/css" href="../css/StileHome.css">
        
    </head>
    <body>
        <div class="wrapper">
            <header>
                <h1>Home</h1>
            </header>
            <?php require_once 'inline/Navbar.php'; ?>

            <div id="container" class="container">
                <section class="introduzione">
                    <h2>Presentazione del sito</h2>
                    <p>
                        Questo sito è stato realizzato con il fine di aiutare
                        l'organizzazione delle partite di biliardino per ridurre al
                        minimo le situazioni in cui tante persone si presentano nella
                        sala con i biliardini e debbano aspettare a lungo senza 
                        poter giocare perché sono tutti occupati. Inoltre, per porre
                        finalmente fine alle lunghe dispute su chi sia il secondo
                        giocatore più forte è stato implementato anche un sistema di
                        ranking, che immagino sarà subito criticato, per stilare una
                        <a href="Classifica.php">classifica</a>.
                        <br>
                        Vedere la pagina dell'<a href="PaginaPersonale.php?userid=1"
                        >amministratore</a> per ulteriori informazioni.
                        <br>
                        Per la <strong>Documentazione</strong> clicka <a href="Documentazione.html">QUI</a>.
                    </p>
                </section>
                <section class="regolamento">
                    <h2>Regolamento</h2>
                    <p>
                        Seguono i principi e il regolamento da seguire per 
                        l'organizzazione delle partite e le regole da rispettare 
                        durante le stesse.
                    </p>
                    <div class="organizzazione" class="regolamento">
                        <h3>Annunci</h3>
                        <p>
                            Seguono i principi cui attenersi nel postare annunci e
                            registrare l'esito delle partite giocate.
                        </p>
                        <div>
                            <h4>Chi può postare un annuncio?</h4>
                            <p>
                                Può farlo chiunque ed è inoltre possibile postare più
                                annunci. Non arrivate al punto di postare decine di
                                annunci impedendo agli altri di giocare che vi banno.
                            </p>
                        </div>
                        <div>
                            <h4>Per quando si possono postare gli annunci?</h4>
                            <p>
                                Al momento è possibile postare annunci per 72 ore a 
                                partire dal momento corrente. Ricordate come è 
                                finita l'ultima volta che si è provato a permettere
                                di prenotarsi per un'intera settimana.
                            </p>
                        </div>
                        <div>
                            <h4>Come funziona il sistema di gestione degli annunci?</h4>
                            <p>
                                Ogni annuncio può attraversare i seguenti stati:
                            <dl>

                                <dt>Creazione
                                    <dd>L'annuncio effettivamente non esiste ancora ma sta
                                        venendo creato da un utente.

                                    <dt>Attivo
                                    <dd>L'annuncio è valido è può essere sottoscritto
                                        da altri giocatori.

                                    <dt>Cancellato
                                    <dd>L'autore ha deciso di rimuovere l'annuncio.

                                    <dt>Scaduto
                                    <dd>Il momento in cui si intendeva iniziare a 
                                        giocare è arrivato senza che 3 giocatori
                                        sottoscrivessero l'annuncio. L'annuncio è quindi
                                        scaduto e si ha perso la priorità per giocare.

                                    <dt>Pronto
                                    <dd>3 giocatori hanno sottoscritto l'annuncio; 
                                        all'autore non resta che prendere la decisione
                                        definitiva se chiuderlo o cancellarlo.

                                    <dt>Chiuso
                                    <dd>
                                        3 giocatori hanno sottoscritto l'annuncio e
                                        l'autore ha fissato l'impegno per tutti di 
                                        giocare all'orario stabilito; si attende solo
                                        la registrazione del risultato.

                                    <dt>Confermando
                                    <dd>Il risultato dell'incontro è stato registrato,
                                        si attende conferma da tutti e 4 i giocatori.
                                        Se uno di loro disconosce il risultato segnato
                                        si ritorna allo stato precedente.

                                    <dt>Concluso
                                    <dd>Il risultato è stato convalidato da tutti i 
                                        giocatori e le partite giocate appaiono 
                                        finalmente nella classifica.
                            </dl>
                        </div>

                        </div>
                    <div class="gioco" class="regolamento">
                        <h3>Gioco</h3>
                        <p>
                            Le regole sono quelle di sempre: una partita è vinta 
                            dalla prima squadra che raggiunge i 6 punti e distanzia
                            gli avversari di almeno 2. Le condizioni in cui si passa
                            sotto le sapete.
                            <br>
                            Come avrete notato però, ci sono delle zone grigie che il
                            nostro regolamento, tramandato finora per via unicamente
                            orale (perché la scrittura è una brutta invenzione), non
                            considera come, ad esempio, quali siano i parametri per
                            definire cosa sia una girella. Per provare quindi a 
                            limitare il rifiorire delle solite periodiche polemiche
                            su cosa sia legittimo e cosa no sarà il caso di iniziare
                            a discutere e a <strong>stilare</strong> un regolamento
                            che sia accettato o almeno riconosciuto da tutti.
                            <br>
                            Tale regolamento sarà esposto qui di seguito affinché 
                            tutti possano prendere visione dell'ultima versione in
                            ogni momento.
                            <br>
                            Mi chiedo quando lo stileremo.
                        </p>
                    </div>
                </section>
            </div>
            <div class="push"></div>
        </div>
        <?php require_once 'inline/Footer.php'; ?>
    </body>
</html>
