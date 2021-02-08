<?php

/**
 * Description of ClassPersonalPost
 *  Questa classe dovrà servire a raccogliere tutte le informazioni sui post 
 *  degli utenti presenti nelle loro PAGINE PERSONALI.
 * 
 * Potrà essere usato anche per i MESSAGGI
 *  
 *  In futuro potrebbe essere unita alla (possibile) classe per gestire i 
 *  messaggi nelle chat ma al momento sarà sviluppata indipendentemente da 
 *  quello che potrebbe succedere in futuro.
 * 
 * 
 *  Un post possiede:
 *      Il codice dell'autore (della pagina cui appartiene in realtà)
 *      Un TimeStamp che indica quando è stato prodotto
 *      ... cose che mi verranno in mente
 * 
 *      Un titolo
 *      Un testo
 * 
 * 
 *  La funzione che accederà al DB per leggere i post sarà sviluppata 
 *  a parte
 *      
 *
 * @author marco
 */

/*
 * NOTA: in futuro avrebbe senso creare una classe base e saparare questa classe
 * da quella per i messaggi nelle chat
 * 
 * 
 * JsonSerializable:
 *  -fornisce il metodo jsonSerialize per semplificare la serializzazione
 *      degli oggetti usanfo json_encode
 * 
 */
class ClassPersonalPost implements JsonSerializable {
    
    /*
     * String
     * Titolo del post
     */
    private $titolo;
    
    /*
     * String
     * Contenuto del post
     */
    private $contenuto;
    
    /*
     * int
     * Codice dell'autore del post
     */
    private $autore;
    
    /*
     * int
     * Codice del post
     */
    private $codice;
    
    /*
     * int
     * Codice della pagina cui appartiene il post
     */
    private $pagina;
    
    /*
     * String
     * Codice del post
     */
    private $ts_creazione;
    
    /*
     * String
     * Codice della pagina cui appartiene il post
     */
    private $ts_ultima_modifica;
    
    /*
     * Trasforma in JSON
     */
    public function jsonSerialize() // from JsonSerializable
    {
        return [
            "codice"                => $this->getCode(),
            "pagina"                => $this->getPage(),
            "autore"                => $this->getAuthor(),
            "ts_creazione"          => $this->getTSCreation(),
            "ts_ultima_modifica"    => $this->getTSLastEdit(),
            "titolo"                => $this->getTitle(), 
            "contenuto"             => $this->getContent()
        ];
    }


    public function __construct(
            /*int*/ $_codice,
            /*int*/ $_pagina, 
            /*int*/ $_autore,
            /*string*/ $_ts_creazione,
            /*string*/ $_ts_ultima_modifica,
            /*string*/ $_titolo, 
            /*string*/ $_contenuto 
            ) {
        $this->codice = $_codice;
        $this->pagina = $_pagina;
        $this->autore = $_autore;
        $this->ts_creazione = $_ts_creazione;
        $this->ts_ultima_modifica = $_ts_ultima_modifica;
        $this->titolo = $_titolo;
        $this->contenuto = $_contenuto;
    }
    
    // Non serve far nulla
    public function __destruct() {}
    
    /*
     * String
     * Ritorna il nome dell'autore
     */
    public function getAuthor()
    {
        return $this->autore;
    }
    
    /*
     * String
     * Ritorna il titolo del post
     */
    public function getTitle()
    {
        return $this->titolo;
    }
    
    /*
     * String
     * Ritorna il contenuto del post
     */
    public function getContent()
    {
        return $this->contenuto;
    }
    
    /*
     * String
     * Ritorna il contenuto del post con i newline sostituiti da <br>
     */
    public function getEscapedContent()
    {
        //htmlspecialchars($this->contenuto)
        //echo(mb_detect_encoding($this->contenuto) . "<br>");
        //$s = htmlentities($this->contenuto, ENT_SUBSTITUTE | ENT_HTML5, "UTF-8");
        //$s = mb_encode_numericentity ($s, [0x0, 0xffff, 0, 0xffff], 'UTF-8');
        //echo(mb_detect_encoding($s) . "<br>");
        //$s = utf8_decode($s);
        //htmlentities ( string $string [, int $flags = ENT_COMPAT | ENT_HTML401 [, string $encoding = ini_get("default_charset") [, bool $double_encode = TRUE ]]] )
        //var_dump(mb_check_encoding ($s, "UTF-8"));
        //var_dump($s);
        //var_dump(get_html_translation_table ());
        $s = $this->contenuto;
        return $s;
    }
    
    /*
     * int
     * Ritorna il codice della pagina cui il messaggio appartiene
     */
    public function getPage()
    {
        return $this->pagina;
    }
    
    /*
     * int
     * Ritorna il codice del Post
     */
    public function getCode()
    {
        return $this->codice;
    }
    
    /*
     * String
     * Ritorna il TS in cui è stato creato il post
     */
    public function getTSCreation()
    {
        return $this->ts_creazione;
    }
    /*
     * String
     * Ritorna il TS in cui è stato modificato l'ultima volta il post
     */
    public function getTSLastEdit()
    {
        return $this->ts_ultima_modifica;
    }
    
}

?>