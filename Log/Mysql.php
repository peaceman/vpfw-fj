<?php
class Vpfw_Log_Mysql extends Vpfw_Log_Abstract implements Vpfw_Interface_Cleaner {
    private $configArray = array(
        'AccessData' => 'localhost:3306;root;asdf;vpfw_db',
        'TableName' => 'vpfw_log',
        'Cached' => 'true',
    );
    private $toLog = array();
    private $mysqli;

    /**
     * Holt die benötigten Konfigurationsvariablen aus dem $configObject und
     * baut daraufhin eine MySQL-Verbindung auf. Wenn hier was schief geht wird
     * eine Vpfw_Exception_Critical Exception geworfen.
     * @param Vpfw_Config_Abstract $configObject
     */
    public function  __construct(Vpfw_Config_Abstract $configObject) {
        Vpfw_Cleaner::cleanMePls($this);
        parent::__construct($configObject);
        $startupErrors = array();
        try {
            $this->configArray = array_merge($this->configArray, $this->configObject->getValue('Log.MySQL'));
        } catch (Vpfw_Exception_InvalidArgument $e) {
            $startupErrors[] = $e->getMessage();
        }

        $accessData = explode(';', $this->configArray['AccessData']);
        if (4 != count($accessData)) {
            throw new Vpfw_Exception_Critical('Fehlerhafter Syntax der MySQL Zugriffsdaten');
        }

        $mysqlPort = 3306;
        // Trennen des Hostnamens vom Port
        if (false !== strpos($accessData[0], ':')) {
            list($accessData[0], $mysqlPort) = explode(':', $accessData[0]);
        }
        $this->mysqli = new mysqli($accessData[0], $accessData[1], $accessData[2], $accessData[3], $mysqlPort);
        if (false != mysqli_connect_error()) {
            throw new Vpfw_Exception_Critical('Konnte keine Verbindung mit der MySQL-Datenbank herstellen. (' . mysqli_connect_errno() . ') ' . mysqli_connect_error());
        }
        $this->checkTableSchema();
        foreach ($startupErrors as $msg) {
            $this->write('base', $msg);
        }
    }

    /**
     * Je nach Konfiguration direktes Schreiben in die Datenbank oder
     * zwischenspeichern in $this->toLog.
     * @param string $logGroup
     * @param string $msg
     */
    protected function _write($logGroup, $msg) {
        if ('true' == $this->configArray['Cached']) {
            $this->toLog[] = array(time(), $logGroup, $msg);
        } else {
            $stmt = $this->mysqli->prepare('INSERT INTO ' . $this->configArray['TableName'] . '(Time, LogGroup, Message) VALUES (?, ?, ?)');
            $stmt->bind_param('iss', time(), $logGroup, $msg);
            if (false == $stmt->execute()) {
                throw new Vpfw_Exception_Critical('Es ist ein Fehler beim Ausführen des SQL-Queries aufgetreten. (' . $stmt->errno . ') ' . $stmt->error);
            }
        }
    }

    /**
     * Falls nicht bereits geschehen wird in dieser Methoden die
     * MySQL-Verbindung geschlossen.
     */
    public function __destruct() {
        if (true == is_object($this->mysqli)) {
            $this->mysqli->close();
        }
    }

    /**
     * Wenn das Cachen der Lognachrichten eingeschalten ist, werden in dieser
     * Methode die Nachrichten in die MySQL-Datenbank geschrieben.
     */
    public function clean() {
        if ('true' == $this->configArray['Cached']) {
            $stmt = $this->mysqli->prepare('INSERT INTO ' . $this->configArray['TableName'] . ' (Time, LogGroup, Message) VALUES (?, ?, ?)');
            foreach ($this->toLog as $log) {
                $stmt->bind_param('iss', $log[0], $log[1], $log[2]);
                if (false == $stmt->execute()) {
                    throw new Vpfw_Exception_Critical('Es ist ein Fehler beim Ausführen des SQL-Queries aufgetreten. (' . $stmt->errno . ') ' . $stmt->error);
                }
            }
        }
    }

    /**
     * Prüft den Aufbau der Tabellen und wirft eine Vpfw_Exception_Critical
     * Exception wenn der Tabellenaufbau nicht dem erwarteten entspricht.
     */
    public function checkTableSchema() {
        // Prüfe ob die Tabelle für die Logeinträge existiert.
        $result = $this->mysqli->query('SHOW TABLES');
        if (false == $result) {
            throw new Vpfw_Exception_Critical('Es ist ein MySQL-Fehler beim Überprüfen des Tabellenschemas aufgetreten. (' . $this->mysqli->errno . ') ' . $this->mysqli->error);
        }
        $tableFound = false;
        while ($row = $result->fetch_row()) {
            if ($this->configArray['TableName'] == $row[0]) {
                $tableFound = true;
                break;
            }
        }
        $result->close();
        if (false == $tableFound) {
            throw new Vpfw_Exception_Critical('Die Tabelle ' . $this->configArray['TableName'] . ' konnte in der angegebenen Logdatenbank nicht gefunden werden.');
        }

        // Prüfe ob die Tabelle auch die benötigten Spalten besitzt
        $requiredColumns = array('Time' => false, 'LogGroup' => false, 'Message' => false);
        $result = $this->mysqli->query('DESCRIBE ' . $this->configArray['TableName']);
        while ($row = $result->fetch_assoc()) {
            $requiredColumns[$row['Field']] = true;
        }
        $errors = array();
        foreach($requiredColumns as $key => $value) {
            if (false == $value) {
                $errors[] = 'Die benötigte Tabellenspalte "' . $key . '" ist in der Logtabelle nicht vorhanden.';
            }
        }
        if (0 != count($errors)) {
            throw new Vpfw_Exception_Critical(implode(PHP_EOL, $errors));
        }
    }
}
