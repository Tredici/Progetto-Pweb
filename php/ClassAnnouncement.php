<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

//use Symfony\Component\Routing\Exception\InvalidParameterException;

//use function PHPSTORM_META\type;

/**
 * Description of ClassAnnouncement
 *
 * @author marco
 */
class ClassAnnouncement implements JsonSerializable {
    private $Codice;
    /*
     * int
     * Fornisce il codice dell'annuncio
     */
    public function getCode() {
        return $this->Codice;
    }
    
    private $Creatore;
    /*
     * int
     * Fornisce il codice del propretario dell'annuncio
     */
    public function getOwnerCode() {
        return $this->Creatore;
    }
    
    private $UsernameCreatore;
    /*
     * string
     * Fornisce il codice del propretario dell'annuncio
     */
    public function getOwnerName() {
        return $this->UsernameCreatore;
    }
    
    private $TipoPartita;
    /*
     * string
     * Fornisce il nome del tipo di partita
     */
    public function getMatchType() {
        return $this->TipoPartita;
    }
    
    private $Giorno;
    /*
     * string
     * Fornisce la data giorno in cui si vorrebbe giocare
     */
    public function getDay() {
        return $this->Giorno;
    }
    
    private $Inizio;
    /*
     * string
     * Fornisce l'orario in cui si vorrebbe iniziare a giocare
     */
    public function getStartTime() {
        return $this->Inizio;
    }
    
    private $Note;
    /*
     * string
     * Fornisce la data giorno in cui si vorrebbe giocare
     */
    public function getNotes() {
        return $this->Note;
    }
    
    private $G2;
    /*
     * int
     * Fornisce il codice del primo sottoscrittore
     */
    public function getG2Code() {
        return $this->G2;
    }
    
    private $UsernameG2;
    /*
     * string
     * Fornisce l'username del primo sottoscrittore
     */
    public function getG2Name() {
        return $this->UsernameG2;
    }
    
    private $G3;
    /*
     * int
     * Fornisce il codice del secondo sottoscrittore
     */
    public function getG3Code() {
        return $this->G3;
    }
    
    private $UsernameG3;
    /*
     * string
     * Fornisce l'username del secondo sottoscrittore
     */
    public function getG3Name() {
        return $this->UsernameG3;
    }
    
    private $G4;
    /*
     * int
     * Fornisce il codice del terzo sottoscrittore
     */
    public function getG4Code() {
        return $this->G4;
    }
    
    private $UsernameG4;
    /*
     * string
     * Fornisce l'username del terzo sottoscrittore
     */
    public function getG4Name() {
        return $this->UsernameG4;
    }
    
    // Non so se abbia senso
    private $GruppoPartite;
    /*
     * int
     * Fornisce il codice del gruppo di partite cui questa appartiene
     */
    public function getGroup() {
        return $this->GruppoPartite;
    }
    
    private $Stato; // ENUM('attivo', 'bloccato')
    /*
     * string
     * Fornisce lo stato della risposta
     */
    public function getState() {
        return $this->Stato;
    }
    
    /*
     * Trasforma in JSON
     */
    public function jsonSerialize() // from JsonSerializable
    {
        $ans = [
            "Codice"                => $this->Codice,
            "Creatore"              => $this->Creatore,
            "UsernameCreatore"      => $this->UsernameCreatore,
            "TipoPartita"           => $this->TipoPartita,
            "Giorno"                => $this->Giorno,
            "Inizio"                => $this->Inizio,
            "Note"                  => $this->Note,
            "G2"                    => $this->G2,
            "UsernameG2"            => $this->UsernameG2,
            "G3"                    => $this->G3,
            "UsernameG3"            => $this->UsernameG3,
            "G4"                    => $this->G4,
            "UsernameG4"            => $this->UsernameG4,
            "GruppoPartite"         => $this->GruppoPartite,
            "Stato"                 => $this->Stato
        ];
        
        global $conn;
        if(isset($_SESSION["user"]) && isset($conn) && $ans["Stato"] == "confermando") {
            $user = $_SESSION["user"];
            $group = $ans["GruppoPartite"];
            
            $data = "";
            
            if($conn->hasConfirmedGroup($user, $group))
                    $data = "confirmed";
            $ans["data"] = $data;
        }
        
        return $ans;
    }
    
    
    public function __construct(
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
            $Stato
            ) {
        $this->Codice = $Codice;
        $this->Creatore = $Creatore;
        $this->UsernameCreatore = $UsernameCreatore;
        $this->TipoPartita = $TipoPartita;
        $this->Giorno = $Giorno;
        $this->Inizio = $Inizio;
        $this->Note = $Note;
        $this->G2 = $G2;
        $this->UsernameG2 = $UsernameG2;
        $this->G3 = $G3;
        $this->UsernameG3 = $UsernameG3;
        $this->G4 = $G4;
        $this->UsernameG4 = $UsernameG4;
        $this->GruppoPartite = $GruppoPartite;
        $this->Stato = $Stato;
    }

    /*
     * @params
     * $usr -> int
     * 
     * @return
     * boolean
     * 
     * Verifica che l'utente il cui codice è fornito sia
     *  tra quelli che hanno sottoscritto l'annuncio
     * 
     * @throws
     *  InvalidParameterException - se il parametro non è numerico
     */
    public function isSubscriptor(/*int*/ $usr)
    {
        if(!is_numeric($usr))
            throw new InvalidParameterException('$usr must be numeric');
        # code...
        return $usr === $this->getOwnerCode()
            or $usr === $this->getG2Code()
            or $usr === $this->getG3Code()
            or $usr === $this->getG4Code();
    }
    
}
