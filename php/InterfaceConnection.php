<?php

require_once 'ClassRegisteredUser.php';
require_once 'ClassSubscriptionReqeuest.php';

/**
 * Lo scopo di questa interfaccia è fornire un'interfaccia comune per tutti le
 *  classi che gestiranno i vari tipi di connessioni ai db
 * 
 * Fornisce i metodi per ottenere varie informazioni dal db utilizzabili nel 
 *  sito
 * 
 * @author marco
 */
interface InterfaceConnection {

    // gestione di alto livello delle transazioni
    public function startTransaction();
    public function commit();
    public function rollback();

    /*
     * int
     * livello di privilegio minimo necessario per eseguire operazioni come utente registrato
     */
    const privUser = 1000;
    /*
     * int
     * livello di privilegio minimo per eseguire le istruzioni come admin
     */
    const privAdmin = 100;
    
    // identificazione utenti
    public function getUserData(/*int*/ $code);
    public function getUserCode(/*string*/ $username);
    public function AutenticateUser(/*string*/ $credenziali, /*string*/ $password, /*string*/ $method);
    
    public function checkRequestState(/*string*/ $credenziali, /*string*/ $password, /*string*/ $method);
    
    //Ottenimento dati utenti
    public function getPersonalPage(ClassRegisteredUser $usr);
    public function getPersonalPosts(/*int*/ $page); // fornisce l'elenco dei post appartenenti alla pagina indicata
    public function deletePost(/*int*/ $code);
    public function getPost(/*int*/ $code);
    
    public function getRanking();
    public function getUserRank(/*int*/ $usr);
    
    //Gestione richieste di iscrizione
    public function getSubscriptionRequests(); // fornisce l'elenco delle richieste da valutare //array di ClassSubscriptionReqeuest
    public function acceptSubscriptionRequests(/*int*/ $req);
    public function rejectSubscriptionRequests(/*int*/ $req);

    //Esegue l'upload di un messaggio e restituire, se va bene, lo stesso 
    public function uploadPersonalPost(ClassPersonalPageBuilder $ppage, /*string*/ $title, /*string*/ $text);
    public function uploadSubscriptionRequest(ClassSubscriptionReqeuest $subrq);
    //Fornisce come stringa il contenuto dell'a risposta'immagine personale richiesta
    public function getPersonalPicture(/*int*/ $usr);
    public function uploadPicture(/*int*/ $usr, /*string*/ $mimetype, /*string*/ $content, /*bool*/ $personal, /*string*/ $filename /*(= NULL)? Magari lo faccio*/);
    //Gestione annunci
    public function createAnnouncement(/*int*/ $usr, /*string*/ $matchType, /*string*/ $date, /*string*/ $time, /*string*/ $note = null);
    public function subscribeAnnouncement(/*int*/ $announcement, /*int*/ $usr);
    public function unsubscribeAnnouncement(/*int*/ $announcement, /*int*/ $usr);
    public function deleteAnnouncement(/*int*/ $announcement);
    
    public function closeAnnouncement(/*int*/ $announcement);
    
    public function getAnnouncement(/*int*/ $code);
    
    public function getAvaibleAnnouncements($usr);
    
    //Generazione bacheca
    public function getPendingAnnouncements(/*int*/ $usr);
    public function getAnnouncementsReadyToAccept(/*int*/ $usr);
    public function getOpenSubscribedAnnouncements(/*int*/ $usr);
    public function getUserAnnouncements(/*int*/ $usr);
    
    public function deleteAnnouncementNotes($annuncio);
    public function updateAnnouncementNotes(/*int*/ $usr, /*string*/ $text);
    
    //public function getAcceptedAnnouncements(/*int*/ $usr);
    public function getClosedAnnouncements(/*int*/ $usr);

        // per la registrazione delle partite
    public function registerSingleMatch(/*int*/ $S1G1, /*int*/ $S1G2, /*int*/ $S2G1, /*int*/ $S2G2,
            /*int*/ $PunteggioS1, /*int*/ $PunteggioS2, /*int*/ $annuncio);  // per registrare una sola partita
    public function registerTripleMatch(/*int*/ $G1, /*int*/ $G2, /*int*/ $G3, /*int*/ $G4,
            /*int*/ $PunteggioP1S1, /*int*/ $PunteggioP1S2,
            /*int*/ $PunteggioP2S1, /*int*/ $PunteggioP2S2,
            /*int*/ $PunteggioP3S1, /*int*/ $PunteggioP3S2,
            /*int*/ $annuncio);  // per registrare un trittico

    public function getConfirmedMatches(/*int*/ $usr);
    
    public function announcementsMatches(/*int*/ $announcement);

    // per la conferma dei risultati
    public function confirmGroup(/*int*/ $usr, /*int*/ $group);
    public function hasConfirmedGroup(/*int*/ $usr, /*int*/ $group);
    public function rejectGroup(/*int*/ $usr, /*int*/ $group);
    
    // per verificare se i quattro nomi passati corrispondono a 4
    // giocatori che potrebbero confrontarsi tra loro - restituisce
    // i codici dei giocatori
    public function checkPlayers(/*string*/ $G1, /*string*/ $G2, /*string*/ $G3, /*string*/ $G4);
    
}
