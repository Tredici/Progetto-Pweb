<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ClassSubscriptionRuqeuest
 *
 * @author marco
 */
class ClassSubscriptionReqeuest implements JsonSerializable {
    
    public function jsonSerialize() { // from JsonSerializable
        return [
            "code" => $this->getCode(),
            "name" => $this->getName(),
            "surname" => $this->getSurname(),
            "username" => $this->getUsername(),
            "name" => $this->getName(),
            "email" => $this->getEmail(),
            "birthday" => $this->getBithday(),
            "sex" => $this->getSex(),
            "reqTS" => $this->getRequestTS(),
            "notes" => $this->getNotes(),
            "state" => $this->getState()
        ];
    }
    
    /*
     * int
     * Codice della richiesta
     */
    private $codice;
    
    public function getCode() {
        return $this->codice;
    }
    // per il download
    public function setCode(/*int*/ $code) {
        $this->codice = $code;
    }
    
    /*
     * String
     * Nome dell'autore della richiesta
     */
    private $nome;
    
    public function getName()
    {
        return $this->nome;
    }

    /*
     * String
     * Cognome dell'autore della richiesta
     */
    private $cognome;
    
    public function getSurname()
    {
        return $this->cognome;
    }
    
    /*
     * String
     * nome utente proposto dall'autore dell'iscrizione
     */
    private $username;
    
    public function getUsername()
    {
        return $this->username;
    }
    
    /*
     * String
     * Indirizzo email dell'autore dell'iscrizione
     */
    private $email;
    
    public function getEmail() {
        return $this->email;
    }
    
    /*
     * String
     * Data di nascita dell'autore dell'iscrizione
     */
    private $bithday;
    
    public function getBithday() {
        return $this->bithday;
    }
    
    /*
     * String
     * Sesso dell'autore dell'iscrizione
     */
    private $sex;
    
    public function getSex() {
        return $this->sex;
    }
    
    /*
     * String
     * Sesso dell'autore dell'iscrizione
     */
    private $requestTS;
    
    public function getRequestTS() {
        return $this->requestTS;
    }
    public function setRequestTS(/*string*/ $ts) {
        $this->requestTS = $ts;
    }
    
    /*
     * String
     * Note aggiuntive associate alla richiesta di iscrizione.
     */
    private $notes;
    
    public function getNotes() {
        return $this->notes;
    }
    
    /*
     * String
     * Stato della richiesta di iscrizione.
     */
    private $stato;
    
    public function getState() {
        return $this->stato;
    }
    
    public function setState(/*string*/ $state) {
        $this->stato = $state;
    }
    
    /*
     * string
     * Password fornita dall'utente al momento dell'upload
     * di una nuova richiesta
     */
    private $password;
    public function getPassword()
    {
        return $this->password;
    }
    public function setPassword(/*string*/ $pwd)
    {
        $this->password = $pwd;
    }

        public function __construct(
            // non più parametro per via dell'upload
            ///*int*/ $_codice,  // default a 0 per le richieste da caricare
            /*string*/ $_nome,
            /*string*/ $_cognome,
            /*string*/ $_username,
            /*string*/ $_email,
            /*string*/ $_bithday,
            /*string*/ $_sex,
            ///*string*/ $_requestTS,
            /*string*/ $_notes = null
            // /*string*/ $_state // non necessario per l'upload
            )
    {
        $this->codice = -1;//$_codice;
        $this->password = "";
        
        $this->nome = $_nome;
        $this->cognome = $_cognome;
        $this->username = $_username;
        $this->email = $_email;
        $this->bithday = $_bithday;
        $this->sex = $_sex;
        //$this->requestTS = $_requestTS;
        $this->notes = $_notes;
        //$this->stato = $_state;
    }
    
}











