<?php

/**
 * Classe che contiene i dati di un generico utente registrato
 *
 * 
 * NOTA: al momento si usano String per date e TS, sarebbe il caso di trovare
 *  presto una valida alternativa
 * 
 * @author marco
 */
class ClassRegisteredUser {
    
    /**
     * @var date
     */
    //private $_birthday;
    //put your code here
    
    /*
     * int
     * Codice della pagina
     */
    private $codice;
    
    /*
     * String
     * Nome dell'utente
     */
    private $nome;
    
    /*
     * String
     * Cognome dell'utente
     */
    private $cognome;
    
    /*
     * String
     * Username dell'utente
     */
    private $username;
    
    /*
     * String
     * Email dell'utente
     */
    private $email;
    
    /*
     * date
     * Data di nasc
     */
    private $compleanno;
    
    /*
     * String - Char
     * Sesso dell'utente, ha senso solo se M o F
     */
    private $sex;
    
    /*
     * -
     * Timestamp di registrazione dell'utente
     */
    private $ts_registrazione;
    
    /*
     * String
     * Descrizione dell'utente
     */
    private $descrizione;
    
    /*
     * int
     * Livello di privilegio dell'utente
     */
    private $privilegio;
    
    
    public function __construct(
            /*int*/ $_codice, 
            /*string*/ $_name,
            /*string*/ $_surname,
            /*string*/ $_username,
            /*string*/ $_email,
            /*string*/ $_birthday,
            /*string*/ $_sex,
            /*string*/ $_ts_registration,
            $_description, // putroppo non possiamo usare nullable
            /*int*/ $_priviledge)
    {
        $this->codice = $_codice;
        $this->nome = $_name;
        $this->cognome = $_surname;
        $this->username = $_username;
        $this->email = $_email;
        $this->compleanno = $_birthday;
        $this->sesso = $_sex;
        $this->ts_registrazione = $_ts_registration;
        $this->descr = $_description;
        $this->privilegio = $_priviledge;
        
        if($_sex != 'M' && $_sex != 'F')
        {
            throw new InvalidArgumentException("Invalid sex.");
        }
    }
    
    /*
     * int
     * Ritorna il codice dell'utente
     */
    public function getCode()
    {
        return $this->codice;
    }
    
    /*
     * String
     * Ritorna il nome dell'utente
     */
    public function getName()
    {
        return $this->nome;
    }
    
    /*
     * String
     * Ritorna il cognome dell'utente
     */
    public function getSurname()
    {
        return $this->cognome;
    }
    
    /*
     * String
     * Ritorna il cognome dell'utente
     */
    public function getUsername()
    {
        return $this->username;
    }
    
    /*
     * String
     * Ritorna l'indirizzo email dell'utente
     */
    public function getEmail()
    {
        return $this->email;
    }
    
    /*
     * String
     * Ritorna il sesso dell'utente
     */
    public function getSex()
    {
        return $this->sex;
    }
    
    /*
     * String
     * Ritorna il timestamp di registrazione dell'utente
     */
    public function getRegistrationTS()
    {
        return $this->ts_registrazione;
    }
    
    /*
     * String
     * Ritorna il compleanno dell'utente
     */
    public function getBirthday()
    {
        return $this->compleanno;
    }
    
    /*
     * String
     * Ritorna una descrizione dell'utente
     */
    public function getDescription()
    {
        return $this->descrizione;
    }
    
    /*
     * int
     * Ritorna il livello di privilegio dell'utente
     */
    public function getPriviledge()
    {
        return $this->privilegio;
    }
    
}
