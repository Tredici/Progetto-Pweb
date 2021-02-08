<?php

/* 
 * Questa classe serve a contenere tutte le informazioni per inviare file
 * tramite richieste http
 */

class ClassDataFile
{
    /*
     * string
     *  mime type del file
     */
    private $mime;
    public function getContentType() {
        return $this->mime;
    }
    
    /*
     * string
     *  contenuto del file
     */
    private $data;
    public function getContent() {
        return $this->data;
    }
    
    
    public function __construct(
            /*string*/ $_mimetype,
            /*string*/ $_data
            ) {
        $this->mime = $_mimetype;
        $this->data = $_data;
    }
    
}