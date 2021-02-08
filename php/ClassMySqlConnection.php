<?php

require_once 'InterfaceConnection.php';
require_once 'ClassRegisteredUser.php';
require_once 'ClassPersonalPageBuilder.php';
require_once 'ClassSubscriptionReqeuest.php';
require_once 'ClassDataFile.php';
require_once 'ClassUserRank.php';
require_once 'ClassAnnouncement.php';


/**
 * Questa classe è concepita per nascondere tutta la logica dietro alla 
 *  connessione con il database.
 *
 * @author marco
 */
class ClassMySqlConnection implements InterfaceConnection {
    
    // gestione di alto livello delle transazioni
    public function startTransaction()
    {
        if(!$this->conn->begin_transaction(MYSQLI_TRANS_START_WITH_CONSISTENT_SNAPSHOT))
            throw new Exception("Can't start transaction.");
    }
    public function commit()
    {
        if(!$this->conn->commit())
            throw new Exception("Can't commit transaction.");
    }
    public function rollback()
    {
        if(!$this->conn->rollback())
            throw new Exception("Can't rollback.");
    }


    /*
     * mysqli   -   per gestire la connessione con il server
     */
    private $conn;

    /*
     * 
     */
    public function __construct(mysqli $_conn) {
        $this->conn = $_conn;
        if($_conn === NULL)
        {
            throw new RuntimeException('$_conn cannot be null.');
        }
    }
    
    public function __destruct() {
        if($this->conn !== NULL)
        {
            $this->conn->close();
            $this->conn = NULL;
        }   
    }
    
    /*
     * @params
     * $code -> int
     * 
     * @return
     * ClassRegisteredUser
     * 
     * Restituisce le informazioni su un utente estratte dalla tabella
     *  biliardino.utenti
     * 
     * @throws
     *  RuntimeException
     */
    public function getUserData(/*int*/ $code)
    {
        $query = "call biliardino.estrai_informazioni_utente(?)";
        $stmt = $this->conn->prepare($query);
        
        if($stmt === NULL)
        {
            throw new RuntimeException("Cannot create prepare statement.");
        }
        
        if($stmt->bind_param("i", $code) === FALSE)
        {
            throw new RuntimeException("Cannot bind param to prepare statement.");
        }
        
        $stmt->execute();
        $stmt->store_result();
        
        if($stmt->errno != 0)
        {
            throw new RuntimeException("Error executing prepared statement." . "errno:" . $stmt->errno . ";error:" . $stmt->error);
        }
        
        if($stmt->num_rows != 1)
        {
            throw new RuntimeException("No result found. Lines retrieved: " . $stmt->num_rows);
        }
        
        $stmt->bind_result(
            $_codice, 
            $_name,
            $_surname,
            $_username,
            $_email,
            $_birthday,
            $_sex,
            $_ts_registration,
            $_description,
            $_priviledge);
        
        $stmt->fetch();
        $stmt->close();
        
        $usr = new ClassRegisteredUser(
            $_codice, 
            $_name,
            $_surname,
            $_username,
            $_email,
            $_birthday,
            $_sex,
            $_ts_registration,
            $_description,
            $_priviledge);
        
        
        return $usr;
    }
    
    /*
     * @params
     * $username -> int
     * 
     * @return
     * int
     * 
     * Restituisce il codice dell'utente il cui username è fornito.
     * Il dato è ricavato dalla procedura
     *  biliardino.ottieni_codice_utente
     * 
     * @throws
     *  RuntimeException
     */
    public function getUserCode(/*string*/ $username)
    {
        $query = "call biliardino.ottieni_codice_utente(?)";
        $stmt = $this->conn->prepare($query);
        
        if($stmt === NULL)
        {
            throw new RuntimeException("Cannot create prepare statement.");
        }
        
        if($stmt->bind_param("s", $username) === FALSE)
        {
            throw new RuntimeException("Cannot bind param to prepare statement.");
        }
        
        $stmt->execute();
        $stmt->store_result();
        
        if($stmt->errno != 0)
        {
            throw new RuntimeException("Error executing prepared statement." . "errno:" . $stmt->errno . ";error:" . $stmt->error);
        }
        
        if($stmt->num_rows != 1)
        {
            throw new RuntimeException("No user found.");
        }
        
        $stmt->bind_result($_code);
        
        $stmt->fetch();
        $stmt->close();
        
        return $_code;
    }
    
    /*
     * @params
     * $credenziali -> string
     * $password -> string
     * $method -> string
     * 
     * @return
     * int -> on success
     * 
     * Restituisce, se le credenziali fornite sono corrette, il codice di 
     *      un utente estratto dalla tabella
     *  biliardino.utenti
     * 
     * @throws
     *  RuntimeException
     */
    public function AutenticateUser(
            /*string*/ $credenziali, 
            /*string*/ $password, 
            /*string*/ $method)
    {
        $query = "call biliardino.login(?, ?, ?);";
        $stmt = $this->conn->prepare($query);
        
        if($stmt === NULL)
        {
            throw new RuntimeException("Cannot create prepare statement.");
        }
        
        if($stmt->bind_param("sss", $credenziali, $password, $method) === FALSE)
        {
            throw new RuntimeException("Cannot bind params to prepare statement.");
        }
        
        $stmt->execute();
        $stmt->store_result();
        
        if($stmt->errno != 0)
        {
            throw new RuntimeException("Error executing prepared statement." . "errno:" . $stmt->errno . ";error:" . $stmt->error);
        }
        
        if($stmt->num_rows != 1)
        {
            throw new RuntimeException("No result found. Lines retrieved: " . $stmt->num_rows);
        }
        
        $stmt->bind_result($_codice);
        $stmt->fetch();
        $stmt->close();
        
        return $_codice;
    }
    
    /*
     * @params
     * $credenziali -> string
     * $password -> string
     * $method -> string
     * 
     * @return
     * int -> on success
     * 
     * Restituisce, se le credenziali fornite sono corrette, il codice di 
     *      un utente estratto dalla tabella
     * 
     * Viene utilizzata la seguente procedura
     *  biliardino.pseudologin
     * 
     * @throws
     *  RuntimeException
     */
    public function checkRequestState(
            /*string*/ $credenziali, 
            /*string*/ $password, 
            /*string*/ $method)
    {
        $query = "call biliardino.pseudologin(?, ?, ?);";
        $stmt = $this->conn->prepare($query);
        
        if($stmt === NULL)
        {
            throw new RuntimeException("Cannot create prepare statement.");
        }
        
        if($stmt->bind_param("sss", $credenziali, $password, $method) === FALSE)
        {
            throw new RuntimeException("Cannot bind params to prepare statement.");
        }
        
        $stmt->execute();
        $stmt->store_result();
        
        if($stmt->errno != 0 || $stmt->num_rows != 1)
        {
            throw new RuntimeException("Error executing prepared statement." . "errno:" . $stmt->errno . ";error:" . $stmt->error);
        }
        
        $stmt->bind_result($_stato);
        $stmt->fetch();
        $stmt->close();
        
        return $_stato;
    }
    
    /*
     * @params
     * $usr -> ClassRegisteredUser
     * 
     * @return
     * ClassPersonalPageBuilder
     * 
     * Restituisce, date le informazioni di utente, un'istanza di
     *  InterfacePageBuilder per costruire la pagina personale dell'utente in
     *  questione. I dati sono ricavati dalle tabelle
     *      biliardino.pagine_personali
     *      biliardino.post_personali
     * 
     * @throws
     *  RuntimeException
     */
    public function getPersonalPage(ClassRegisteredUser $usr)
    {
        if($usr->getPriviledge() > self::privUser)
        {
            throw new Exception("Utente con grado di privilegio troppo basso per eseguire l'operazione.");
        }
        
        $query = "call biliardino.estrai_informazioni_pagina_utente(?);";
        $stmt = $this->conn->prepare($query);
        
        if($stmt === NULL)
        {
            throw new RuntimeException("Cannot create prepare statement.");
        }
        
        $code = $usr->getCode();
        
        if($stmt->bind_param("i", $code) === FALSE)
        {
            throw new RuntimeException("Cannot bind param to prepare statement.");
        }
        
        $stmt->execute();
        $stmt->store_result();
        
        if($stmt->errno != 0)
        {
            throw new RuntimeException("Error executing prepared statement." . "errno:" . $stmt->errno . ";error:" . $stmt->error);
        }
        
        if($stmt->num_rows != 1)
        {
            throw new RuntimeException("No result found. Lines retrieved: " . $stmt->num_rows);
        }
        
        
        $stmt->bind_result(
            $_codice, 
            $_codice_proprietario,
            //$_foto_profilo,   post BLOB
            $_commento);
        
        $stmt->fetch();
        $stmt->close();
        
        $ppb = new ClassPersonalPageBuilder(
            $_codice,
            $_codice_proprietario,
            $usr, 
            //$_foto_profilo,   post BLOB
            $_commento);
        
        // Ora bisogna aggiungere la parte per estrarre tutti i post personali
        
        $_list = $this->getPersonalPosts($ppb->getCode());
        
        $ppb->addPosts($_list);
        return $ppb;
    }
    
    
    /*
     * @params
     * $page -> int
     *      -il codice della pagina di cui si desiderano i post
     * 
     * @return
     * Array<ClassPersonalPost>
     * 
     * Restituisce, dato il codice di una pagina, un array con i post presenti
     *  nella pagina in questione.
     *  I dati sono ricavati dalle tabelle
     *      biliardino.post_personali
     * 
     * @throws
     *  RuntimeException
     */
    public function getPersonalPosts(/*int*/ $page)
    {
        $query = "call biliardino.estrai_post_pagina_utente(?);";
        $stmt = $this->conn->prepare($query);
        
        if($stmt === NULL)
        {
            throw new RuntimeException("Cannot create prepare statement.");
        }
        
        if($stmt->bind_param("i", $page) === FALSE)
        {
            throw new RuntimeException("Cannot bind param to prepare statement.");
        }
        
        $stmt->execute();
        $stmt->store_result();
        
        if($stmt->errno != 0)
        {
            throw new RuntimeException("Error executing prepared statement." . "errno:" . $stmt->errno . ";error:" . $stmt->error);
        }
        
        $posts = [];
        for($i = 0, $n = $stmt->num_rows; $i!=$n; ++$i)
        {
        
            $stmt->bind_result(
                $_codice,
                $_pagina, 
                $_autore,
                $_ts_creazione,
                $_ts_ultima_modifica,
                $_titolo, 
                $_contenuto);

            $stmt->fetch();

            $posts[] = new ClassPersonalPost(
                $_codice,
                $_pagina, 
                $_autore,
                $_ts_creazione,
                $_ts_ultima_modifica,
                $_titolo, 
                $_contenuto);

        }
        $stmt->close();
        
        return $posts;
    }
    
    /*
     * @params
     * $code    -> int
     *      -il codice del post richiesto
     * 
     * @return
     * ClassPersonalPost
     *      :on success
     * NULL
     *      :on fail (not found)
     * 
     * Restituisce il post richiesto.
     *  I dati sono ricavati tramite la procedura
     *      biliardino.ottieni_post
     * 
     * @throws
     *  RuntimeException
     */
    public function getPost(/*int*/ $code)
    {
        $query = "call biliardino.ottieni_post(?);";
        $stmt = $this->conn->prepare($query);
        
        if($stmt === NULL)
        {
            throw new RuntimeException("Cannot create prepare statement.");
        }
        
        if($stmt->bind_param("i", $code) === FALSE)
        {
            throw new RuntimeException("Cannot bind param to prepare statement.");
        }
        
        $stmt->execute();
        $stmt->store_result();
        
        if($stmt->errno != 0)
        {
            throw new RuntimeException("Error executing prepared statement." . "errno:" . $stmt->errno . ";error:" . $stmt->error);
        }
        
        $post = NULL;
        if($stmt->num_rows != 0)
        {
        
            $stmt->bind_result(
                $_codice,
                $_pagina, 
                $_autore,
                $_ts_creazione,
                $_ts_ultima_modifica,
                $_titolo, 
                $_contenuto);

            $stmt->fetch();

            $post = new ClassPersonalPost(
                $_codice,
                $_pagina, 
                $_autore,
                $_ts_creazione,
                $_ts_ultima_modifica,
                $_titolo, 
                $_contenuto);

        }
        $stmt->close();
        
        return $post;
    }
    
    /*
     * @params
     * $code    -> int
     *      :il codice del post da cancellare
     * 
     * @return
     * void
     * 
     * Restituisce il post richiesto.
     *  Viene usata la procedura
     *      biliardino.cancella_post_personale
     * 
     * @throws
     *  RuntimeException
     */
    public function deletePost(/*int*/ $code)
    {
        $query = "call biliardino.cancella_post_personale(?);";
        $stmt = $this->conn->prepare($query);
        
        if($stmt === NULL)
        {
            throw new RuntimeException("Cannot create prepare statement.");
        }
        
        if($stmt->bind_param("i", $code) === FALSE)
        {
            throw new RuntimeException("Cannot bind param to prepare statement.");
        }
        
        $stmt->execute();
        $stmt->store_result();
        
        if($stmt->errno != 0)
        {
            throw new RuntimeException("Error executing prepared statement." . "errno:" . $stmt->errno . ";error:" . $stmt->error);
        }
        
        $stmt->close();
    }
            
    /*
     * @params
     * 
     * @return
     * Array<ClassUserRank>
     * 
     * Restituisce la classifica degli utenti registrati al sito.
     *  I dati sono ottenuti tramite la procedura
     *      biliardino.ottieni_classifica
     * 
     * @throws
     *  RuntimeException
     */
    public function getRanking()
    {
        $query = "call biliardino.ottieni_classifica();";
        $stmt = $this->conn->prepare($query);
        
        if($stmt === NULL)
        {
            throw new RuntimeException("Cannot create prepare statement.");
        }
        
        $stmt->execute();
        $stmt->store_result();
        
        if($stmt->errno != 0)
        {
            throw new RuntimeException("Error executing prepared statement." . "errno:" . $stmt->errno . ";error:" . $stmt->error);
        }
        
        $ranking = [];
        
        $posizione = 0;
        $punteggio_precedente = -1;
        
        for($i = 0, $n = $stmt->num_rows; $i!=$n; ++$i)
        {
        /*
         *  C.`Giocatore`,
            U.`NomeUtente`,
            C.`Vittorie`,
            C.`Sconfitte`,
            C.`Umiliazioni`,
            C.`GoalFatti`,
            C.`GoalSubiti`,
            C.`RapportoVittorieSconfitte`,
            C.`RapportoGoalFattiSubiti`,
            C.`Punteggio`
         */
            $stmt->bind_result(
                $_Giocatore,
                $_NomeUtente,
                $_Vittorie,
                $_Sconfitte,
                $_UmiliazioniInflitte,
                $_UmiliazioniSubite,
                $_GoalFatti,
                $_GoalSubiti,
                $_RapportoVittorieSconfitte,
                $_RapportoGoalFattiSubiti,
                $_Punteggio
            );

            $stmt->fetch();

            if($_Punteggio != $punteggio_precedente)
            {
                $posizione = $i+1;
                $punteggio_precedente = $_Punteggio;
            }
            
            $ranking[] = new ClassUserRank(
                $posizione,
                //$i+1,
                $_Giocatore,
                $_NomeUtente,
                $_Vittorie,
                $_Sconfitte,
                $_UmiliazioniInflitte,
                $_UmiliazioniSubite,
                $_GoalFatti,
                $_GoalSubiti,
                $_RapportoVittorieSconfitte,
                $_RapportoGoalFattiSubiti,
                $_Punteggio
            );

        }
        $stmt->close();
        
        return $ranking;
    }
    
    /*
     * @params
     * 
     * @return
     * ClassUserRank
     * 
     * Ottiene i dati in classifica di un solo utente
     *  I dati sono ottenuti tramite la procedura
     *      biliardino.ottieni_utente_classifica
     * 
     * @throws
     *  RuntimeException
     */
    public function getUserRank(/*int*/ $usr)
    {
        $query = "call biliardino.ottieni_utente_classifica(?);";
        $stmt = $this->conn->prepare($query);
        
        if($stmt === NULL)
        {
            throw new RuntimeException("Cannot create prepare statement.");
        }
        
        if($stmt->bind_param("i", $usr) === FALSE) {
            throw new RuntimeException("Cannot bind param to prepare statement.");
        }
        
        $stmt->execute();
        $stmt->store_result();
        
        if($stmt->errno != 0)
        {
            throw new RuntimeException("Error executing prepared statement." . "errno:" . $stmt->errno . ";error:" . $stmt->error);
        }
        
        if($stmt->num_rows == 0)
        {
            throw new RuntimeException("No data Found");
        }
                
        $stmt->bind_result(
            $_Giocatore,
            $_NomeUtente,
            $_Vittorie,
            $_Sconfitte,
            $_UmiliazioniInflitte,
            $_UmiliazioniSubite,
            $_GoalFatti,
            $_GoalSubiti,
            $_RapportoVittorieSconfitte,
            $_RapportoGoalFattiSubiti,
            $_Punteggio
        );

        $stmt->fetch();

        $rank = new ClassUserRank(
            $i+1,
            $_Giocatore,
            $_NomeUtente,
            $_Vittorie,
            $_Sconfitte,
            $_UmiliazioniInflitte,
            $_UmiliazioniSubite,
            $_GoalFatti,
            $_GoalSubiti,
            $_RapportoVittorieSconfitte,
            $_RapportoGoalFattiSubiti,
            $_Punteggio
        );
        $stmt->close();
        
        return $rank;
    }
    
    /*
     * @params
     * 
     * @return
     * Array<ClassSubscriptionReqeuest>
     * 
     * Restituisce l'elenco delle richieste di iscrizione giunte al sito.
     *  I dati sono ricavati dalle tabelle
     *      biliardino.post_personali
     * 
     * @throws
     *  RuntimeException
     */
    public function getSubscriptionRequests() //array di ClassSubscriptionReqeuest
    {
        $query = "call biliardino.ottieni_dati_richieste();";
        $stmt = $this->conn->prepare($query);
        
        if($stmt === NULL)
        {
            throw new RuntimeException("Cannot create prepare statement.");
        }
        
        $stmt->execute();
        $stmt->store_result();
        
        if($stmt->errno != 0)
        {
            throw new RuntimeException("Error executing prepared statement." . "errno:" . $stmt->errno . ";error:" . $stmt->error);
        }
        
        $join_req = [];
        for($i = 0, $n = $stmt->num_rows; $i!=$n; ++$i)
        {
        
            $stmt->bind_result(
                /*int*/ $_codice,
            /*string*/ $_nome,
            /*string*/ $_cognome,
            /*string*/ $_username,
            /*string*/ $_email,
            /*string*/ $_bithday,
            /*string*/ $_sex,
            /*string*/ $_requestTS,
            /*string*/ $_notes,
            /*string*/ $_state);

            $stmt->fetch();

            $req = new ClassSubscriptionReqeuest(
                ///*int*/ $_codice,
            /*string*/ $_nome,
            /*string*/ $_cognome,
            /*string*/ $_username,
            /*string*/ $_email,
            /*string*/ $_bithday,
            /*string*/ $_sex,
            ///*string*/ $_requestTS,
            /*string*/ $_notes
            //,/*string*/ $_state
                    );
            
            $req->setCode($_codice);
            $req->setRequestTS($_requestTS);
            $req->setState($_state);
            
            $join_req[] = $req;
        }
        $stmt->close();
        
        return $join_req;
    }
    
    /*
     * @params
     * 
     * $req -> int 
     *      :codice della richiesta da gestire
     * 
     * @return
     * void
     * 
     * permette di accettare una richiesta
     *  Viene richiamata la seguente funzione
     *      biliardino.accetta_richiesta
     * 
     * @throws
     *  RuntimeException    -   se la richiesta non può essere approvata
     */
    public function acceptSubscriptionRequests(/*int*/ $req)
    {
        $query = "CALL biliardino.accetta_richiesta(?);";
        $stmt = $this->conn->prepare($query);
        
        if($stmt === NULL) {
            throw new RuntimeException("Cannot create prepare statement.");
        }
        
        if($stmt->bind_param("i", $req) === FALSE) {
            throw new RuntimeException("Cannot bind param to prepare statement.");
        }
        
        $stmt->execute();
        
        if($stmt->errno) {
            throw new RuntimeException("Error executing prepared statement." . "errno:" . $stmt->errno . ";error:" . $stmt->error);
        }
        
        $stmt->close();
    }
            
    /*
     * @params
     * 
     * $req -> int 
     *      :codice della richiesta da gestire
     * 
     * @return
     * void
     * 
     * permette di respingere una richiesta
     *  Viene richiamata la seguente funzione
     *      biliardino.respingi_richiesta
     * 
     * @throws
     *  RuntimeException    -   se la richiesta non può essere respinta
     */
    public function rejectSubscriptionRequests(/*int*/ $req)
    {
        $query = "CALL biliardino.respingi_richiesta(?);";
        $stmt = $this->conn->prepare($query);
        
        if($stmt === NULL) {
            throw new RuntimeException("Cannot create prepare statement.");
        }
        
        if($stmt->bind_param("i", $req) === FALSE) {
            throw new RuntimeException("Cannot bind param to prepare statement.");
        }
        
        $stmt->execute();
        
        if($stmt->errno) {
            throw new RuntimeException("Error executing prepared statement." . "errno:" . $stmt->errno . ";error:" . $stmt->error);
        }
        
        $stmt->close();
    }
    
    /*
     * @params
     * 
     * $ppage -> ClassPersonalPageBuilder 
     *      :contiene tutte le informazioni sulla pagina da aggiornare
     *      :codice dell'utente proprietario e codice della pagina
     * $title -> string 
     *      :il titolo del post da inserire, dovrebbe essere di al più 128
     *          caratteri
     * $text  -> string 
     *      :contenuto effettivo del post che si desidera inserire
     * 
     * @return
     * void
     * 
     * Esegu l'upload di un post sulla pagina utente.
     *  Il post è memorizzato nella tabella
     *      biliardino.post_personali
     * 
     * @throws
     *  RuntimeException
     */
    public function uploadPersonalPost(
            ClassPersonalPageBuilder $ppage,
            /*string*/ $title,
            /*string*/ $text
            )
    {
        $query = "call biliardino.aggiungi_post_pagina_personale(?, ?, ?, ?);";
        $stmt = $this->conn->prepare($query);
        
        if($stmt === NULL)
        {
            throw new RuntimeException("Cannot create prepare statement.");
        }
        $code = $ppage->getCode();
        $owner = $ppage->getOwnwerCode();
        if($stmt->bind_param("iiss", $code, $owner, $title, $text) === FALSE)
        {
            throw new RuntimeException("Cannot bind param to prepare statement.");
        }
        
        $stmt->execute();
        
        if($stmt->errno)
        {
            throw new RuntimeException("Error executing prepared statement." . "errno:" . $stmt->errno . ";error:" . $stmt->error);
        }
        
        $stmt->store_result();
        if($stmt->num_rows != 1)
        {
            throw new RuntimeException("Something bad happend." . "errno:" . $stmt->errno . ";error:" . $stmt->error);
        }
        $stmt->bind_result(
            $_codice,
            $_pagina, 
            $_autore,
            $_ts_creazione,
            $_ts_ultima_modifica,
            $_titolo, 
            $_contenuto);

        $stmt->fetch();

        $newPost = new ClassPersonalPost(
            $_codice,
            $_pagina, 
            $_autore,
            $_ts_creazione,
            $_ts_ultima_modifica,
            $_titolo, 
            $_contenuto);
        
        $stmt->close();
        
        return $newPost;
    }
    
    
    
    /*
     * @params
     * 
     * $ppage -> ClassPersonalPageBuilder 
     *      :contiene tutte le informazioni sulla pagina da aggiornare
     *      :codice dell'utente proprietario e codice della pagina
     * $title -> string 
     *      :il titolo del post da inserire, dovrebbe essere di al più 128
     *          caratteri
     * $text  -> string 
     *      :contenuto effettivo del post che si desidera inserire
     * 
     * @return
     * void
     * 
     * Esegu l'upload di un post sulla pagina utente.
     *  Il post è memorizzato nella tabella
     *      biliardino.post_personali
     * 
     * @throws
     *  RuntimeException
     */
    public function uploadSubscriptionRequest(
            ClassSubscriptionReqeuest $subrq
            )
    {
        //Param sql
        //(<{in Nome varchar(64)}>, 
        //<{in Cognome varchar(64)}>, 
        //<{in NomeUtente varchar(64)}>, 
        //<{in IndirizzoEmail varchar(64)}>, 
        //<{in Password varchar(64)}>, 
        //<{in DataDiNascita date}>, 
        //<{in Sesso enum('M','F')}>, 
        //<{in Note text}>);
        $query = "CALL biliardino.genera_richiesta(?, ?, ?, ?, ?, ?, ?, ?);";
        $stmt = $this->conn->prepare($query);
        
        if($stmt === NULL)
        {
            throw new RuntimeException("Cannot create prepare statement.");
        }
        
        $name = $subrq->getName();
        $surname = $subrq->getSurname();
        $username = $subrq->getUsername();
        $email = $subrq->getEmail();
        $pwd = $subrq->getPassword();
        $birthday = $subrq->getBithday();
        $sex = $subrq->getSex();
        $notes = $subrq->getNotes();
        
        if($stmt->bind_param("ssssssss", 
                $name,
                $surname,
                $username,
                $email,
                $pwd,
                $birthday,
                $sex,
                $notes
                    ) === FALSE)
        {
            throw new RuntimeException("Cannot bind param to prepare statement.");
        }
        
        $stmt->execute();
        
        if($stmt->errno != 0)
        {
            throw new RuntimeException("Error executing prepared statement." . "errno:" . $stmt->errno . ";error:" . $stmt->error);
        }
        
        $stmt->close();
    }
    
    /*
     * @params
     * 
     * $usr -> int
     *      :codice dell'utente di cui si desidera la foto profilo
     * 
     * @return
     * string   foto profilo dell'utente salvata come stringa di byte
     * 
     * Scarica dal database la foto profilo dell'utente in questione
     *      biliardino.post_personali
     * 
     * @throws
     *  RuntimeException
     */
    public function getPersonalPicture(/*int*/ $usr)
    {
        $query = "CALL biliardino.ottieni_foto_profilo(?);";
        $stmt = $this->conn->prepare($query);
        
        if($stmt === NULL)
        {
            throw new RuntimeException("Cannot create prepare statement.");
        }
        
        if($stmt->bind_param("i",$usr) === FALSE)
        {
            throw new RuntimeException("Cannot bind param to prepare statement.");
        }
        
        $stmt->execute();
        
        if($stmt->errno != 0)
        {
            throw new RuntimeException("Error executing prepared statement." . "errno:" . $stmt->errno . ";error:" . $stmt->error);
        }
        
        $ans = NULL;
        $stmt->store_result();
        if($stmt->num_rows)
        {
            $mime = NULL;
            $data = NULL;
            $stmt->bind_result($mime, $data);
            $stmt->fetch();
            $ans = new ClassDataFile($mime, $data);
        }
        
        $stmt->close();
        
        return $ans;
    }
    
    /*
     * @params
     * 
     * $usr -> int
     *      :codice dell'utente di cui si desidera caricare una foto
     * $mimetype    -> string
     *      :mime dell'immagine che si desidera caricare
     * $content     -> string
     *      :contenuto in byte della foto
     * $content     -> string
     *      :contenuto in byte della foto
     * $filename    -> string
     *      :nome da associare al file che si desidera caricare
     * 
     * @return
     * void
     * 
     * Carica nel database la foto fornita dell'utente $usr
     * Precisamente viene utilizzata la funzione
     *      biliardino.immagini
     * 
     * @throws
     *  RuntimeException
     */
    public function uploadPicture(/*int*/ $usr, /*string*/ $mimetype, /*string*/ $content, /*bool*/ $personal,  /*string*/ $filename /*(= NULL)? Magari lo faccio*/)
    {
        $query = "CALL biliardino.carica_foto(?,?,?,?,?);";
        $stmt = $this->conn->prepare($query);
        
        if($stmt === NULL) {
            throw new RuntimeException("Cannot create prepare statement.");
        }
        
        $null = NULL;
        if($stmt->bind_param("sisbi", $filename, $usr, $mimetype, $null, $personal) === FALSE) {
            throw new RuntimeException("Cannot bind param to prepare statement.");
        }
        
        if(!$stmt->send_long_data(3, $content)) {
            throw new RuntimeException("Error sending long data." . "errno:" . $stmt->errno . ";error:" . $stmt->error);
        }
        
        $stmt->execute();
        
        if($stmt->errno) {
            throw new RuntimeException("Error executing prepared statement." . "errno:" . $stmt->errno . ";error:" . $stmt->error);
        }
        
        $stmt->close();
    }
    
    
    /*
     * @params
     * 
     * $usr -> int
     *      :codice dell'utente che intende creare un nuovo annuncio
     * $announcement    -> int
     *      :codice dell'annuncio da sottoscrivere
     *
     * @return
     * int: il codice del nuovo annuncio
     * 
     * Fa creare a un utente un nuovo annuncio
     * Precisamente viene utilizzata la funzione
     *      biliardino.registra_annuncio
     * 
     * @throws
     *  RuntimeException
     */
    public function createAnnouncement(/*int*/ $usr, /*string*/ $matchType, /*string*/ $date, /*string*/ $time, /*string*/ $note = null)
    {
        $query = "call biliardino.registra_annuncio(?, ?, ?, ?, ?);";
        $stmt = $this->conn->prepare($query);
        
        if($stmt === NULL)
        {
            throw new RuntimeException("Cannot create prepare statement.");
        }
        
        if($stmt->bind_param("issss", $usr, $matchType, $date, $time, $note) === FALSE)
        {
            throw new RuntimeException("Cannot bind params to prepare statement.");
        }
        
        $stmt->execute();
        if($stmt->errno != 0)
        {
            throw new RuntimeException("Error executing prepared statement." . "errno:" . $stmt->errno . ";error:" . $stmt->error);
        }
        
        $stmt->store_result();
        if($stmt->num_rows != 1)
        {
            throw new RuntimeException("Error executing prepared statement." . "errno:" . $stmt->errno . ";error:" . $stmt->error);
        }
        
        $stmt->bind_result($annCode);
        $stmt->fetch();
        
        $stmt->close();
        
        return $annCode;
    }
    
    /*
     * @params
     * 
     * $usr -> int
     *      :codice dell'utente che intende sottoscrivere un annuncio
     * $announcement    -> int
     *      :codice dell'annuncio da sottoscrivere
     *
     * @return
     * void
     * 
     * Fa sottoscrivere a un utente un annuncio
     * Precisamente viene utilizzata la funzione
     *      biliardino.sottoscrivi_annuncio
     * 
     * @throws
     *  RuntimeException
     */
    public function subscribeAnnouncement(/*int*/ $announcement, /*int*/ $usr)
    {
        $query = "call biliardino.sottoscrivi_annuncio(?, ?);";
        $stmt = $this->conn->prepare($query);
        
        if($stmt === NULL)
        {
            throw new RuntimeException("Cannot create prepare statement.");
        }
        
        if($stmt->bind_param("ii", $announcement, $usr) === FALSE)
        {
            throw new RuntimeException("Cannot bind params to prepare statement.");
        }
        
        $stmt->execute();
        if($stmt->errno != 0)
        {
            throw new RuntimeException("Error executing prepared statement." . "errno:" . $stmt->errno . ";error:" . $stmt->error);
        }
        $stmt->close();
    }
    
    /*
     * @params
     * 
     * $usr -> int
     *      :codice dell'utente che intende cancellare la sottoscrizione a un annuncio
     * $announcement    -> int
     *      :codice dell'annuncio in questione
     *
     * @return
     * void
     * 
     * Elimina la sottoscrizione di un utente a un annuncio
     * Precisamente viene utilizzata la funzione
     *      biliardino.cancella_sottoscrizione_annuncio
     * 
     * @throws
     *  RuntimeException
     */
    public function unsubscribeAnnouncement(/*int*/ $announcement, /*int*/ $usr)
    {
        $query = "call biliardino.cancella_sottoscrizione_annuncio(?, ?);";
        $stmt = $this->conn->prepare($query);
        
        if($stmt === NULL)
        {
            throw new RuntimeException("Cannot create prepare statement.");
        }
        
        if($stmt->bind_param("ii", $announcement, $usr) === FALSE)
        {
            throw new RuntimeException("Cannot bind params to prepare statement.");
        }
        
        $stmt->execute();
        if($stmt->errno != 0)
        {
            throw new RuntimeException("Error executing prepared statement." . "errno:" . $stmt->errno . ";error:" . $stmt->error);
        }
        $stmt->close();
    }
    
    /*
     * @params
     * 
     * $announcement    -> int
     *      :codice dell'annuncio da cancellare
     *
     * @return
     * void
     * 
     * Elimina un annuncio
     * Precisamente viene utilizzata la funzione
     *      biliardino.cancella_annuncio
     * 
     * @throws
     *  RuntimeException
     */
    public function deleteAnnouncement(/*int*/ $announcement)
    {
        $query = "call biliardino.cancella_annuncio(?);";
        $stmt = $this->conn->prepare($query);
        
        if($stmt === NULL)
        {
            throw new RuntimeException("Cannot create prepare statement.");
        }
        
        if($stmt->bind_param("i", $announcement) === FALSE)
        {
            throw new RuntimeException("Cannot bind params to prepare statement.");
        }
        
        $stmt->execute();
        if($stmt->errno != 0)
        {
            throw new RuntimeException("Error executing prepared statement." . "errno:" . $stmt->errno . ";error:" . $stmt->error);
        }
        $stmt->close();
    }
    
    /*
     *
     * @params
     * 
     * $announcement    -> int
     *      :annuncio da chiudere
     *
     * @return
     * void
     * 
     * Permette di chiudere un annuncio
     * Si serve della procedura
     *      biliardino.chiudi_annuncio
     * 
     * @throws
     *  RuntimeException
     */
    public function closeAnnouncement(/*int*/ $announcement) {
        $query = "call biliardino.chiudi_annuncio(?);";
        $stmt = $this->conn->prepare($query);
        
        if($stmt === NULL)
        {
            throw new RuntimeException("Cannot create prepare statement.");
        }
        
        if($stmt->bind_param("i", $announcement) === FALSE)
        {
            throw new RuntimeException("Cannot bind params to prepare statement.");
        }
        
        $stmt->execute();
        if($stmt->errno != 0)
        {
            throw new RuntimeException("Error executing prepared statement." . "errno:" . $stmt->errno . ";error:" . $stmt->error);
        }
        $stmt->close();
    }

    /*
     *
     * @params
     * 
     * $query   -> int
     *      :query da eseguire
     * $usr     -> int
     *      :l'utente in questione
     *
     * @return
     * Array<ClassAnnouncement>
     * 
     * Riunisce le parti comuni a tutte le funzioni che gestiscono annunci
     * 
     * @throws
     *  RuntimeException
     */
    private function ricavaAnnunci(/*string*/ $query, /*int*/ $usr)
    {
        $stmt = $this->conn->prepare($query);
        
        if($stmt === NULL)
        {
            throw new RuntimeException("Cannot create prepare statement.");
        }
        
        if($stmt->bind_param("i", $usr) === FALSE)
        {
            throw new RuntimeException("Cannot bind params to prepare statement.");
        }
        
        $stmt->execute();
        $stmt->store_result();
        
        if($stmt->errno != 0)
        {
            throw new RuntimeException("Error executing prepared statement." . "errno:" . $stmt->errno . ";error:" . $stmt->error);
        }
        
        $pending_announcements = [];
        for($i = 0, $n = $stmt->num_rows; $i!=$n; ++$i)
        {
        
            $stmt->bind_result(
                $Codice,
                $Creatore,
                $UsernameCreatore,
                $TipoPartita,
                $Giorno,
                $Inizio,
                $Note,
                $G2,
                $UsernameG2,
                $G3,
                $UsernameG3,
                $G4,
                $UsernameG4,
                $GruppoPartite,
                $Stato);

            $stmt->fetch();

            $ann = new ClassAnnouncement(
                $Codice,
                $Creatore,
                $UsernameCreatore,
                $TipoPartita,
                $Giorno,
                $Inizio,
                $Note,
                $G2,
                $UsernameG2,
                $G3,
                $UsernameG3,
                $G4,
                $UsernameG4,
                $GruppoPartite,
                $Stato);
            
            $pending_announcements[] = $ann;
        }
        $stmt->close();

        return $pending_announcements;
    }
    
    /*
     * @params
     * 
     * $usr    -> int
     *      :codice dell'utente che richiede gli annunci pendenti
     *
     * @return
     * Array<ClassAnnouncement>
     * 
     * individua tutti gli annunci che possono essere sottoscritti da un untente
     *  dato
     * Precisamente viene utilizzata la funzione
     *      biliardino.ottieni_annunci_sottoscrivibili
     * 
     * @throws
     *  RuntimeException
     */
    public function getPendingAnnouncements(/*int*/ $usr)
    {
        $query = "call biliardino.ottieni_annunci_sottoscrivibili(?);";
        return $this->ricavaAnnunci($query, $usr);
    }
    
    /*
     * @params
     * 
     * $usr    -> int
     *      :codice dell'utente che richiede gli annunci pronti per essere da lui 
     *          accettati, ovvero quelli di cui è autore e sottoscritti da altre tre persone
     *
     * @return
     * Array<ClassAnnouncement>
     * 
     * individua tutti gli annunci afflitti dall'utente che sono pronti per essere
     *  accettati
     * Precisamente viene utilizzata la funzione
     *      biliardino.ottieni_annunci_pendenti
     * 
     * @throws
     *  RuntimeException
     */
    public function getAnnouncementsReadyToAccept(/*int*/ $usr)
    {
        $query = "call biliardino.ottieni_annunci_pronti(?);";
        return $this->ricavaAnnunci($query, $usr);
    }
    
    /*
     * @params
     * 
     * $usr    -> int
     *      :codice dell'utente che richiede gli annunci ancora aperti da lui sottoscritti
     *
     * @return
     * Array<ClassAnnouncement>
     * 
     * individua tutti gli annunci che possono essere sottoscritti da un untente
     *  dato
     * Precisamente viene utilizzata la funzione
     *      biliardino.ottieni_annunci_pendenti
     * 
     * @throws
     *  RuntimeException
     */
    public function getOpenSubscribedAnnouncements(/*int*/ $usr)
    {
        $query = "call biliardino.ottieni_annunci_sottoscritti_correnti(?);";
        return $this->ricavaAnnunci($query, $usr);        
    }
    
    /*
     * @params
     * 
     * $usr    -> int
     *      :codice dell'utente che richiede gli annunci da lui postati, ancora
     *          aperti e non ancora sottoscritti da 3 persone
     *
     * @return
     * Array<ClassAnnouncement>
     * 
     * individua tutti gli annunci che possono essere sottoscritti da altri untente
     *  creati da quello di cui si fornisce il codice
     * Precisamente viene utilizzata la funzione
     *      biliardino.ottieni_annunci_propri_in_coda
     * 
     * @throws
     *  RuntimeException
     */
    public function getUserAnnouncements(/*int*/ $usr)
    {
        $query = "call biliardino.ottieni_annunci_propri_in_coda(?);";
        return $this->ricavaAnnunci($query, $usr);
    }
    
    /*
     * @params
     * 
     * $announcement    -> int
     *      :codice dell'annuncio di cui si desidera cancellare le note
     *
     * @return
     * void
     * 
     * Permette di modificare le note associate a un annuncio
     *      biliardino.modifica_note_annuncio
     * 
     * @throws
     *  RuntimeException
     */
    public function deleteAnnouncementNotes(/*int*/ $announcement)
    {
        $query = "call biliardino.cancella_note_annuncio(?);";
        $stmt = $this->conn->prepare($query);
        
        if($stmt === NULL)
        {
            throw new RuntimeException("Cannot create prepare statement.");
        }
        
        if($stmt->bind_param("i", $announcement) === FALSE)
        {
            throw new RuntimeException("Cannot bind params to prepare statement.");
        }
        
        $stmt->execute();
        $stmt->store_result();
        
        if($stmt->errno != 0)
        {
            throw new RuntimeException("Error executing prepared statement." . "errno:" . $stmt->errno . ";error:" . $stmt->error);
        }

        $stmt->close();
    }
    
    /*
     * @params
     * 
     * $announcement    -> int
     *      :codice dell'annuncio di cui si desidera modificare le note
     * $text            -> string
     *      :nuovo valore che si desidera assegnare alle note
     *
     * @return
     * void
     * 
     * Permette di modificare le note associate a un annuncio
     *      biliardino.modifica_note_annuncio
     * 
     * @throws
     *  RuntimeException
     */
    public function updateAnnouncementNotes(/*int*/ $announcement, /*string*/ $text)
    {
        $query = "call biliardino.modifica_note_annuncio(?,?);";
        $stmt = $this->conn->prepare($query);
        
        if($stmt === NULL)
        {
            throw new RuntimeException("Cannot create prepare statement.");
        }
        
        if($stmt->bind_param("is", $announcement, $text) === FALSE)
        {
            throw new RuntimeException("Cannot bind params to prepare statement.");
        }
        
        $stmt->execute();
        $stmt->store_result();
        
        if($stmt->errno != 0)
        {
            throw new RuntimeException("Error executing prepared statement." . "errno:" . $stmt->errno . ";error:" . $stmt->error);
        }

        $stmt->close();
    }
    
    /*
     * @params
     * 
     * $usr    -> int
     *      :codice dell'utente che richiede gli annunci cui è autore o 
     *          sottoscrittore per i quali ci si è preso l'impegno di fare una
     *          partita
     *
     * @return
     * Array<ClassAnnouncement>
     * 
     * individua tutti gli annunci che divrebbero essere seguiti da una partita
     *      biliardino.ottieni_annunci_conclusi
     * 
     * @throws
     *  RuntimeException
     */
    // ex - getAcceptedAnnouncements
    public function getClosedAnnouncements(/*int*/ $usr)
    {
        $query = "call biliardino.ottieni_annunci_chiusi(?);";
        return $this->ricavaAnnunci($query, $usr);
    }
    
    
    /*
     * @params
     * 
     * $code    -> int
     *      :codice dell'annuncio desiderato
     *
     * @return
     * ClassAnnouncement
     * 
     * Ottiene l'annuncio il cui codice è fornito
     *      biliardino.ottieni_annuncio
     * 
     * @throws
     *  RuntimeException
     */
    public function getAnnouncement(/*int*/ $code)
    {
        $query = "call biliardino.ottieni_annuncio(?);";
        $stmt = $this->conn->prepare($query);
        
        if($stmt === NULL)
        {
            throw new RuntimeException("Cannot create prepare statement.");
        }
        
        if($stmt->bind_param("i", $code) === FALSE)
        {
            throw new RuntimeException("Cannot bind params to prepare statement.");
        }
        
        $stmt->execute();
        $stmt->store_result();
        
        if($stmt->errno != 0)
        {
            throw new RuntimeException("Error executing prepared statement." . "errno:" . $stmt->errno . ";error:" . $stmt->error);
        }
        
        $stmt->bind_result(
            $Codice,
            $Creatore,
            $UsernameCreatore,
            $TipoPartita,
            $Giorno,
            $Inizio,
            $Note,
            $G2,
            $UsernameG2,
            $G3,
            $UsernameG3,
            $G4,
            $UsernameG4,
            $GruppoPartite,
            $Stato);

        $stmt->fetch();

        $ann = new ClassAnnouncement(
            $Codice,
            $Creatore,
            $UsernameCreatore,
            $TipoPartita,
            $Giorno,
            $Inizio,
            $Note,
            $G2,
            $UsernameG2,
            $G3,
            $UsernameG3,
            $G4,
            $UsernameG4,
            $GruppoPartite,
            $Stato);

        $stmt->close();
        
        return $ann;
    }
    
    /*
     * @params
     * $usr     -> int
     *      :codice dell'utente che richiede gli annunci
     * 
     * @return
     * Array<ClassAnnouncement>
     * 
     * Ottiene tutti gli annunci con cui l'utente $usr potrebbe interagire
     *      biliardino.ottieni_annunci_disponibili
     * 
     * Nota: al momento il parametro $usr è assolutamente inutile e serve
     *  solo per compatibilità per sfruttare la funzione "ricavaAnnunci"
     * 
     * @throws
     *  RuntimeException
     */
    public function getAvaibleAnnouncements($usr)
    {
        $query = "call biliardino.ottieni_annunci_disponibili(?);";
        return $this->ricavaAnnunci($query, $usr);
    }

    /*
     * @params
     * 
     * $G1      -> string
     * $G2      -> string
     * $G3      -> string
     * $G4      -> string
     *      :codice dell'annuncio desiderato
     *
     * @return
     * ClassAnnouncement
     * 
     * Ottiene l'annuncio il cui codice è fornito
     *      biliardino.ottieni_annuncio
     * 
     * @throws
     *  RuntimeException
     */
    public function checkPlayers(/*string*/ $G1, /*string*/ $G2, /*string*/ $G3, /*string*/ $G4)
    {
        $query = "call biliardino.controlla_compatibilita_giocatori(?,?,?,?);";
        $stmt = $this->conn->prepare($query);
        
        if($stmt === NULL)
        {
            throw new RuntimeException("Cannot create prepare statement.");
        }
        
        if($stmt->bind_param("ssss", $G1, $G2, $G3, $G4) === FALSE)
        {
            throw new RuntimeException("Cannot bind params to prepare statement.");
        }
        
        $stmt->execute();
        $stmt->store_result();
        
        /*if($stmt->errno != 0)
        {
            throw new RuntimeException("Error executing prepared statement." . "errno:" . $stmt->errno . ";error:" . $stmt->error);
        }*/
        
        $stmt->bind_result(
            $username,
            $Codice);
        
        $tizi = [];
        for($i=0, $n=$stmt->num_rows; $i!=$n; ++$i)
        {
            $stmt->fetch();
            $tizi[$username] = $Codice;
        }

        $stmt->close();
        return $tizi;
    }
    
    /*
     * @params
     * 
     * $S1G1    -> int
     * $S1G2    -> int
     * $S2G1    -> int
     * $S2G2    -> int
     *      :giocatori e squadra di appartenenza
     * 
     * $PunteggioS1 -> int
     * $PunteggioS2 -> int
     *
     * @return
     * void
     * 
     * Registra l'esito di una partita singola
     *      biliardino.registra_partita_singola
     * 
     * @throws
     *  RuntimeException
     */
    public function registerSingleMatch(
            /*int*/ $S1G1, /*int*/ $S1G2, /*int*/ $S2G1, /*int*/ $S2G2, 
            /*int*/ $PunteggioS1, /*int*/ $PunteggioS2, 
            /*int*/ $annuncio)
    {
        $query = "call biliardino.registra_partita_singola(?,?,?,?,?,?,?);";
        $stmt = $this->conn->prepare($query);
        
        if($stmt === NULL)
        {
            throw new RuntimeException("Cannot create prepare statement.");
        }
        
        if($stmt->bind_param("iiiiiii", $S1G1, $S1G2, $S2G1, $S2G2, $PunteggioS1, $PunteggioS2, $annuncio) === FALSE)
        {
            throw new RuntimeException("Cannot bind params to prepare statement.");
        }
        
        $stmt->execute();
        $stmt->store_result();
        
        if($stmt->errno != 0)
        {
            throw new RuntimeException("Error executing prepared statement." . "errno:" . $stmt->errno . ";error:" . $stmt->error);
        }
        
        $stmt->close();
    }
    
    /*
     * @params
     * 
     * $G1      -> int
     * $G2      -> int
     * $G3      -> int
     * $G4      -> int
     *      :giocatori - guarda nel DB per vedere come sono organizzati
     * 
     * $PunteggioP1S1   -> int
     * $PunteggioP1S2   -> int
     *      :esito della prima partita (G1 con G2)
     *
     * $PunteggioP2S1   -> int
     * $PunteggioP2S2   -> int
     *      :esito della seconda partita (G1 con G3)
     * 
     * $PunteggioP3S1   -> int
     * $PunteggioP3S2   -> int
     *      :esito della terza partita (G1 con G4)
     * 
     * $annuncio    -> int
     *      :l
     * 
     * @return
     * void
     * 
     * Registra l'esito di un trittico
     *      biliardino.registra_trittico
     * 
     * @throws
     *  RuntimeException
     */
    public function registerTripleMatch(/*int*/ $G1, /*int*/ $G2, /*int*/ $G3, /*int*/ $G4,
            /*int*/ $PunteggioP1S1, /*int*/ $PunteggioP1S2,
            /*int*/ $PunteggioP2S1, /*int*/ $PunteggioP2S2,
            /*int*/ $PunteggioP3S1, /*int*/ $PunteggioP3S2,
            /*int*/ $annuncio)
    {
        $query = "call biliardino.registra_trittico(?,?,?,?,?,?,?,?,?,?,?);";
        $stmt = $this->conn->prepare($query);
        
        if($stmt === NULL)
        {
            throw new RuntimeException("Cannot create prepare statement.");
        }
        
        if($stmt->bind_param("iiiiiiiiiii", $G1, $G2, $G3, $G4,
            $PunteggioP1S1, $PunteggioP1S2,
            $PunteggioP2S1, $PunteggioP2S2,
            $PunteggioP3S1, $PunteggioP3S2,
            $annuncio) === FALSE)
        {
            throw new RuntimeException("Cannot bind params to prepare statement.");
        }
        
        $stmt->execute();
        $stmt->store_result();
        
        if($stmt->errno != 0)
        {
            throw new RuntimeException("Error executing prepared statement." . "errno:" . $stmt->errno . ";error:" . $stmt->error);
        }
        
        $stmt->close();
    }
    
    /*
     * @params
     * 
     * $usr     -> int
     *      :codice dell'utente
     * 
     * @return
     * Array<Array di Partite>
     * 
     * Ottiene le tutte le partite giocate dal giocatore $usr
     * Utilizza la procedura
     *      biliardino.ottieni_partite_giocatore
     * 
     * @throws
     *  RuntimeException
     */
    public function getConfirmedMatches(/*int*/ $usr)
    {
        $query = "call biliardino.ottieni_partite_giocatore(?);";
        $stmt = $this->conn->prepare($query);
        
        if($stmt === NULL)
        {
            throw new RuntimeException("Cannot create prepare statement.");
        }
        
        if($stmt->bind_param("i", $usr) === FALSE)
        {
            throw new RuntimeException("Cannot bind params to prepare statement.");
        }
        
        $stmt->execute();
        $stmt->store_result();
        
        if($stmt->errno != 0)
        {
            throw new RuntimeException("Error executing prepared statement." . "errno:" . $stmt->errno . ";error:" . $stmt->error);
        }
        
        $played = [];
        for($i = 0, $n = $stmt->num_rows; $i!=$n; ++$i)
        {
        
            $stmt->bind_result(
                $Codice,
                $Gruppo,
                $TimeStamp,
                $PunteggioSquadra1,
                $PunteggioSquadra2,
                $S1G1,
                $S1G1name,
                $S1G2,
                $S1G2name,
                $S2G1,
                $S2G1name,
                $S2G2,
                $S2G2name);
            

            $stmt->fetch();

            $match = [
                "Codice"            => $Codice,
                "Gruppo"            => $Gruppo,
                "TimeStamp"         => $TimeStamp,
                "PunteggioSquadra1" => $PunteggioSquadra1,
                "PunteggioSquadra2" => $PunteggioSquadra2,
                "S1G1"              => $S1G1,
                "S1G1name"          => $S1G1name,
                "S1G2"              => $S1G2,
                "S1G2name"          => $S1G2name,
                "S2G1"              => $S2G1,
                "S2G1name"          => $S2G1name,
                "S2G2"              => $S2G2,
                "S2G2name"          => $S2G2name];
            
            $played[] = $match;
        }
        $stmt->close();

        return $played;
    }
    
    /*
     * @params
     * 
     * $announcement    -> int
     *      :codice dell'annuncio
     * 
     * @return
     * Array<Array di Partite>
     * 
     * Ottiene le tutte le partite legate all'annuncio $announcement, serve per
     *  la parte in cui bisogna confermare l'esito degli annunci
     * Utilizza la procedura
     *      biliardino.ottieni_partite_annuncio
     * 
     * @throws
     *  RuntimeException
     */
    public function announcementsMatches(/*int*/ $announcement)
    {
        $query = "call biliardino.ottieni_partite_annuncio(?);";
        $stmt = $this->conn->prepare($query);
        
        if($stmt === NULL)
        {
            throw new RuntimeException("Cannot create prepare statement.");
        }
        
        if($stmt->bind_param("i", $announcement) === FALSE)
        {
            throw new RuntimeException("Cannot bind params to prepare statement.");
        }
        
        $stmt->execute();
        $stmt->store_result();
        
        if($stmt->errno != 0)
        {
            throw new RuntimeException("Error executing prepared statement." . "errno:" . $stmt->errno . ";error:" . $stmt->error);
        }
        
        $played = [];
        for($i = 0, $n = $stmt->num_rows; $i!=$n; ++$i)
        {
        
            $stmt->bind_result(
                $Codice,
                $Gruppo,
                $TimeStamp,
                $PunteggioSquadra1,
                $PunteggioSquadra2,
                $S1G1,
                $S1G1name,
                $S1G2,
                $S1G2name,
                $S2G1,
                $S2G1name,
                $S2G2,
                $S2G2name);
            

            $stmt->fetch();

            $match = [
                "Codice"            => $Codice,
                "Gruppo"            => $Gruppo,
                "TimeStamp"         => $TimeStamp,
                "PunteggioSquadra1" => $PunteggioSquadra1,
                "PunteggioSquadra2" => $PunteggioSquadra2,
                "S1G1"              => $S1G1,
                "S1G1name"          => $S1G1name,
                "S1G2"              => $S1G2,
                "S1G2name"          => $S1G2name,
                "S2G1"              => $S2G1,
                "S2G1name"          => $S2G1name,
                "S2G2"              => $S2G2,
                "S2G2name"          => $S2G2name];
            
            $played[] = $match;
        }
        $stmt->close();

        return $played;
    }
            
    /*
     * @params
     * 
     * $usr     -> int
     *      :codice dell'utente
     * $group   -> int
     *      :codice del gruppo desiderato
     *
     * @return
     * void
     * 
     * Conferma l'esito delle partite del gruppo $group
     *  da parte del delll'utente $usr.
     * È idempotente
     * Utilizza la procedura
     *      biliardino.conferma_esito
     * 
     * @throws
     *  RuntimeException
     */
    public function confirmGroup(/*int*/ $usr, /*int*/ $group)
    {
        $query = "call biliardino.conferma_esito(?,?);";
        $stmt = $this->conn->prepare($query);
        
        if($stmt === NULL)
        {
            throw new RuntimeException("Cannot create prepare statement.");
        }
        
        if($stmt->bind_param("ii", $usr, $group) === FALSE)
        {
            throw new RuntimeException("Cannot bind params to prepare statement.");
        }
        
        $stmt->execute();
        $stmt->store_result();
        
        if($stmt->errno != 0)
        {
            throw new RuntimeException("Error executing prepared statement." . "errno:" . $stmt->errno . ";error:" . $stmt->error);
        }
        
        $stmt->close();
    }
    
    /*
     * @params
     * 
     * $usr     -> int
     *      :codice dell'utente
     * $group   -> int
     *      :codice del gruppo desiderato
     *
     * @return
     * bool
     * 
     * Permette di verificare se l'utente abbia o meno già confermato il 
     *  risultato segnato per l'incontro.
     * Utilizza la procedura
     *      biliardino.ha_confermato
     * 
     * @throws
     *  RuntimeException
     */
    public function hasConfirmedGroup(/*int*/ $usr, /*int*/ $group)
    {
        $query = "call biliardino.ha_confermato(?,?);";
        $stmt = $this->conn->prepare($query);
        
        if($stmt === NULL)
        {
            throw new RuntimeException("Cannot create prepare statement.");
        }
        
        if($stmt->bind_param("ii", $usr, $group) === FALSE)
        {
            throw new RuntimeException("Cannot bind params to prepare statement.");
        }
        
        $stmt->execute();
        $stmt->store_result();
        
        if($stmt->errno != 0)
        {
            throw new RuntimeException("Error executing prepared statement." . "errno:" . $stmt->errno . ";error:" . $stmt->error);
        }
        $ans = !boolval($stmt->num_rows);
        $stmt->close();
        return $ans;
    }
    
    
    /*
     * @params
     * 
     * $usr     -> int
     *      :codice dell'utente
     * $group   -> int
     *      :codice del gruppo desiderato
     *
     * @return
     * void
     * 
     * Complementare alla precedente.
     * È idempotente
     * Utilizza la procedura
     *      biliardino.conferma_esito
     * 
     * @throws
     *  RuntimeException
     */
    public function rejectGroup(/*int*/ $usr, /*int*/ $group)
    {
        $query = "call biliardino.sconfessa_esito(?,?);";
        $stmt = $this->conn->prepare($query);
        
        if($stmt === NULL)
        {
            throw new RuntimeException("Cannot create prepare statement.");
        }
        
        if($stmt->bind_param("ii", $usr, $group) === FALSE)
        {
            throw new RuntimeException("Cannot bind params to prepare statement.");
        }
        
        $stmt->execute();
        $stmt->store_result();
        
        if($stmt->errno != 0)
        {
            throw new RuntimeException("Error executing prepared statement." . "errno:" . $stmt->errno . ";error:" . $stmt->error);
        }
        
        $stmt->close();
    }
}
