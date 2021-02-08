<?php

require_once 'ClassMySqlConnection.php';

/**
 * Questa classe statica non ha altro scopo che controllare gestire la 
 *  costruzione delle connessioni verso il DB del sito
 *
 * TUTTI i suoi metodi sono STATICI e restituiscono un'istanza di tipo
 *  InterfaceConnection
 * 
 * 
 * @author marco
 */
class ClassConnectionBuilder {
    
    /*
     * Classe statica, non voglio che sia instanziabile.
     */
    private function __construct() {}
    
    public static function buildDefaultConnection()
    {
        $obj = ClassConnectionBuilder::buildMySQLConnection();
        return $obj;
    }
    
    /*
     * ClassMySqlConnection
     */
    public static function buildMySQLConnection(
            /*string*/ $host = "localhost",
            /*string*/ $username = "root",
            /*string*/ $passwd = "",
            /*string*/ $dbname = "biliardino"
            )
    {
        $conn = new mysqli($host, $username, $passwd, $dbname);
        
        if($conn === FALSE)
        {
            throw new RuntimeException("Can't connect to db.");
        }
        else if ($conn->connect_errno != 0) {
            throw new RuntimeException('Connect Error (' 
                    . $mysqli->connect_errno . ') '
                    . $mysqli->connect_error);
        }
        
        // Spero che stavolta funzioni
        $conn->query("SET CHARACTER SET utf8");
        
        $obj = new ClassMySqlConnection($conn);
        
        return $obj;
    }
}
