<?php

// Una pagina personale raccoglie i post degli utenti
require_once 'ClassRegisteredUser.php';
require_once 'ClassPersonalPost.php';
require_once 'InterfacePageBuilder.php';
/**
 * Description of ClassPersonalPage
 *
 *  Questa classe deve essere in grado di raccogliere tutte le informazioni
 *  presenti nella pagina personale di un utente e di usarle per costruire 
 *  effettivamente la pagina che sarà inviata al client
 * 
 * 
 * 
 * 
 * @author marco
 */
class ClassPersonalPageBuilder implements InterfacePageBuilder {
    
    /*
     * Array<ClassPersonalPost>
     * Elenco dei post nella pagina
     */
    private $posts;
    
    /*
     * int
     * Codice della pagina personale
     */
    private $codice;

    /*
     * int
     * Codice del proprietario della pagina
     * 
     * Ridondante, ma mantenuto per aumentare i controlli nel codice
     */
    private $codice_proprietario;
    
    /*
     * ClassRegisteredUser
     * Dati sull'utente proprietario della pagina
     */
    private $utente;
    public function getUserName() {
        return $this->utente->getUsername();
    }
    
    /*
     * Fornisce l'accesso diretto alla classe ClassRegisteredUser
     *  sottostante
     */
    public function getUser() {
        return $this->utente;
    }

    /*
     * RIMOSSO IN SEGUITO ALL'INTRODUZIONE DEI BLOB
     * 
     * String
     * URL dell'utente proprietario della pagina
     *
     *private $foto_profilo;
     */

    /*
     * String
     * Commento alla pagina dell'utente.
     */
    private $commento;
    
    public function __construct(
            /*int*/ $_codice,
            /*int*/ $_codice_proprietario,
            /*ClassRegisteredUser*/ $_utente, 
            ///*string*/ $_foto_profilo = NULL, Rimosso dopo i BLOB
            /*string*/ $_commento = NULL
            ) {
        
        $this->codice = $_codice;
        $this->codice_proprietario = $_codice_proprietario;
        $this->utente = $_utente;
        //$this->foto_profilo = $_foto_profilo;
        $this->commento = $_commento;
        $this->posts = array();
        
        if($this->utente === NULL)
        {
            throw new InvalidArgumentException('$_utente can\'t be NULL.');
        }
        else if($this->utente->getCode() !== $this->codice_proprietario)
        {
            throw new InvalidArgumentException('Wrong user associated with page.');
        }
    }
    
    public function __destruct() {}
    
    /*
     * void
     * Aggiunge un nuovo post a quelli associati alla pagina
     */
    public function addPost(ClassPersonalPost $_post)
    {
        if($_post === NULL)
        {
            throw new InvalidArgumentException("Can't add null post.");
        }
        $this->posts[] = $_post;
    }
    
    /*
     * void
     * Aggiunge una sequenza di post a quelli associati alla pagina
     */
    public function addPosts(array $_list)
    {
        $callback = function(/*int*/ $carry,
                        ClassPersonalPost $item)
                    {
                        return $carry + (int)($item instanceof ClassPersonalPost);
                    };
        if(count($_list) !== array_reduce($_list, $callback, 0))
        {
            throw new InvalidArgumentException("Array of ClassPersonalPost required.");
        }
        $this->posts = array_merge($this->posts, $_list);
    }

    /*
     * Array<ClassPersonalPost>
     * Ritorna tutti i post dell'utente
     */
    public function getPosts() {
        return $this->posts;
    }

    /*
     * void
     * Costruisce la pagina in questione
     */
    public function build() {
        //header( 'Content-type: text/html; charset=utf-8' );
        //header("Content-Type: text/html;charset=UTF-8");
        require_once '../Pagine/Materiale/PaginaPersonale.php';
    }

    
    /*
     * int
     * Ritorna il codice identificativo della pagina.
     */
    public function getCode() {
        return $this->codice;
    }
    
    /*
     * bool
     * Indica se l'utente possiede una foto profilo
     */
    public function hasPersonalPicture() {
        return $this->foto_profilo != "";
    }
    
    /*
     * String
     * Restituisce il percorso dalla cartella base del sito alla foto
     *  profilo dell'utente
     */
    public function getPersonalPhoto() {
        return $this->foto_profilo;
    }
    
    /*
     * int
     * Restituisce il codice identificativo dell'utente proprietario
     */
    public function getOwnwerCode() {
        return $this->codice_proprietario;
    }
    
}
