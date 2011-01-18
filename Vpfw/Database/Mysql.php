<?php
class Vpfw_Database_Mysql extends Vpfw_Abstract_Loggable implements Vpfw_Interface_Cleaner {
    /**
     * MySQL-Verbindung
     * @var mysqli
     */
    private $mysqli;

    /**
     * In diesem Array werden die ausgeführten SQL-Queries zur Auswertung oder
     * ähnlichem gespeichert.
     * @var array
     */
    private $executedQueries;

    /**
     * Konfigurationsarray
     * @var array
     */
    private $config = array(
        'AccessData' => 'localhost:3306;root;asdf;magic_db',
        'LogQueries' => 'false',
    );


    /**
     * Öffnet die Datenbankverbindung und wirft Exceptions vom Typ
     * Vpfw_Exception_Critical wenn Fehler auftreten sollten.
     * @param Vpfw_Config_Abstract $configObject
     * @param Vpfw_Log_Abstract $logObject
     */
    public function __construct(Vpfw_Config_Abstract $configObject, Vpfw_Log_Abstract $logObject) {
        parent::__construct($logObject);
        Vpfw_Cleaner::cleanMePls($this);
        $this->logGroup = 'mysql';
        try {
            $this->config = array_merge($this->config, $configObject->getValue('MySQL'));
        } catch (Vpfw_Exception_InvalidArgument $e) {
            $this->log($e->getMessage());
        }
        $this->establishDatabaseConnection();
    }

    /**
     * Schließt die Datenbankverbindung, wenn dies nicht bereits geschehen ist.
     */
    public function __destruct() {
        if (true == is_object($this->mysqli)) {
            $this->mysqli->close();
        }
    }

    /**
     * Nur ein Wrapper für die prepare Methode der mysqli Klasse.
     * @param string $qry
     * @return Vpfw_Database_MysqlStmt
     */
    public function prepare($qry) {
        $stmt = $this->mysqli->prepare($qry);
        if (false == $stmt) {
            throw new Vpfw_Exception_Critical('MySQL-Error: (' . $this->mysqli->errno . ') ' . $this->mysqli->error);
        }
        return new Vpfw_Database_MysqlStmt($stmt, $qry, $this);
    }

    /**
     * Stellt die Datenbankverbindung her. Bei Fehlern wird die
     * Vpfw_Exception_Critical Exception geworfen.
     */
    private function establishDatabaseConnection() {
        $accessData = explode(';', $this->config['AccessData']);
        if (4 != count($accessData)) {
            throw new Vpfw_Exception_Critical('Fehlerhafter Syntax der MySQL Zugriffsdaten');
        }
        // Setzen des Standardwertes
        $mysqlPort = 3306;
        // Trennen des Hostnamens vom Port
        if (false !== strpos($accessData[0], ':')) {
            list($accessData[0], $mysqlPort) = explode(':', $accessData[0]);
        }
        $this->mysqli = new mysqli($accessData[0], $accessData[1], $accessData[2], $accessData[3], $mysqlPort);
        if (false != mysqli_connect_error()) {
            throw new Vpfw_Exception_Critical('Konnte keine Verbindung mit der MySQL-Datenbank herstellen. (' . mysqli_connect_errno() .') ' . mysqli_connect_error());
        }
    }
    
    /**
     *
     * @param string $qry 
     */
    public function addExecutedQuery($qry) {
        $this->executedQueries[] = $qry;
    }
    
    public function clean() {
        if (true == $this->config['LogQueries']) {
            foreach ($this->executedQueries as $qry) {
                $this->log($qry, 'mysqlqry');
            }
        }
    }
}
