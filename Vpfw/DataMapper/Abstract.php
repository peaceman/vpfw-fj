<?php
abstract class Vpfw_DataMapper_Abstract implements Vpfw_DataMapper_Interface, Vpfw_Interface_Cleaner {
    /**
     * Verwalteter Datenspeicher für DataObjects
     * @var Vpfw_Database_Mysql
     */
    protected $db;

    /**
     * Verwalter Datenspeicher für DataObjects
     * @var Vpfw_ObserverArray
     */
    protected $cache;

    /**
     *
     * @var Vpfw_ObserverArray
     */
    protected $toInsert;

    /**
     * Beinhaltet den Tabellennamen
     *
     * Wird von der Kindklasse gesetzt
     * 
     * @var string
     */
    protected $tableName;
    
    /**
     * Beinhaltet die Datenspalten der aktuell repräsentierten Tabelle in der
     * Reihenfolge wie sie auch in den SQL-Queries anzutreffen ist.
     *
     * Die Arrayschlüssel beinhalten die Spaltennamen und die Arraywerte die
     * jeweiligen Datentypen.
     * 
     * @var array
     */
    protected $dataColumns;

    /**
     * Die Platzhalter Columns, TableName und Values werden von der spezifischen
     * DataMapper Klassen im Konstruktor ersetzt.
     * @var array
     */
    protected $sqlQueries = array(
        'getById' => 'SELECT
                          {Columns}
                      FROM
                          {TableName}
                      WHERE
                          Id = ?',
        'getByFv' => 'SELECT
                          {Columns}
                      FROM
                          {TableName}
                      WHERE
                          {WhereClause}',
        'getAll' => 'SELECT
                         {Columns}
                     FROM
                         {TableName}',
        'delById' => 'DELETE FROM {TableName} WHERE Id = ?',
        'delByFv' => 'DELETE FROM {TableName} WHERE {WhereClause}',
        'delAll' => 'DELETE FROM {TableName}',
        'fvExists' => 'SELECT
                           Id
                       FROM
                           {TableName}
                       WHERE
                           {WhereClause}',
        'insert' => 'INSERT INTO
                         {TableName}
                         ({Columns})
                     VALUES
                         ({Values})',
        'update' => 'UPDATE
                         {TableName}
                     SET
                         {Updates}
                     WHERE
                         Id = ?'
    );

    /**
     * Konstruktor
     *
     * @param Vpfw_Database_Mysql $db
     */
    public function __construct(Vpfw_Database_Mysql $db) {
        $this->db = $db;
        $this->cache = new Vpfw_ObserverArray();
        $this->toInsert = new Vpfw_ObserverArray();
        $this->fillDetailData();
        $this->fixSqlQueries();
        Vpfw_Cleaner::cleanMePls($this);
    }

    /**
     * Befüllt die Objekteigenschaften dataColumns und tableName, desweiteren
     * können hier auch die SQL-Queries ersetzt werden, wenn die Standardqueries
     * nicht ausreichen sollten.
     *
     * @return void
     */
    abstract protected function fillDetailData();
    
    /**
     * Ersetzt die Standardplatzhalter Columns, TableName und Values durch
     * ihre jeweiligen Werte. Sollten die Standard SQL-Queries nicht
     * ausreichend sein und es werden weitere Platzhalter benötigt müssen diese
     * in der jeweiligen Kindklasse durch ihren Inhalt ersetzt werden. Dabei
     * spielt es keine Rolle ob diese Elternmethode am Schluss der Kindmethode
     * oder am Anfang ausgeführt wird.
     *
     * @return void
     */
    protected function fixSqlQueries() {
        $columns = '';
        $columnsWithoutId = '';
        $values = '';
        $valuesForInsert = '';
        $countDataColumns = count($this->dataColumns);
        $i = 0;
        $j = 0;
        $k = 0;
        foreach ($this->dataColumns as $key => $value) {
            $columns .= '`' . $key . '`';
            if ($i != $countDataColumns - 1) {
                $columns .= ',' . PHP_EOL;
            }
            $i++;

            if ('Id' != $key) {
                $values .= '?';
                if ($j != $countDataColumns - 2) {
                    $values .= ',' . PHP_EOL;
                }
                $j++;
                $columnsWithoutId .= '`' . $key . '`';
                if ($k != $countDataColumns - 2) {
                    $columnsWithoutId .= ',' . PHP_EOL;
                }
                $k++;
            }
        }

        foreach ($this->sqlQueries as $key => &$value) {
            if ('insert' == $key) {
                $this->sqlQueries['filledInsert'] = str_replace('{Columns}', $columnsWithoutId, $value);
                $this->sqlQueries['filledInsert'] = str_replace('{Values}', $values, $this->sqlQueries['filledInsert']);
            } else {
                $value = str_replace('{Columns}', $columns, $value);
                $value = str_replace('{Values}', $values, $value);
            }
            $value = str_replace('{TableName}', $this->tableName, $value);            
        }
    }

    /**
     * Diese Methode kapselt das Erzeugen von DataObjects wobei sie hierbei auch
     * nur die Vpfw_Factory mit dem erzeugen beauftragt. Der Sinn dieser Methode
     * liegt jedoch darin, dass die erzeugten DataObjects gleich in den
     * verwalteten Arrays des DataMappers referenziert werden und somit nicht
     * verloren gehen können.
     *
     * @param array $parameters Optionale Parameter, die der Methode publicate des erzeugten DataObjects übergeben werden
     * @param bool $forceInsert Steht dieser Wert auf true ist der Parameter $parameters nichtmehr optional und das erzeugte DataObject wird direkt in die Datenbank eingetragen
     * @return Vpfw_DataObject_Interface
     */
    public function createEntry($parameters = null, $forceInsert = false) {
        $doName = explode('_', get_called_class());
        $doName = $doName[2];
        if (true == $forceInsert) {
            if (true == is_null($parameters)) {
                throw new Vpfw_Exception_Logical('Wenn das DataObject direkt in die Datenbank eingetragen werden soll, sollte es auch Daten beinhalten');
            } else {
                $dataObject = Vpfw_Factory::getDataObject($doName);
                $publicateResult = $dataObject->publicate($parameters);
                if (true !== $publicateResult) {
                    throw new Vpfw_Exception_Validation('Die Validierung der Daten ist fehlgeschlagen', $publicateResult);
                }

                $this->insert($dataObject);
                $this->cache[$dataObject->getId()] = $dataObject;
            }
        } else {
            if (isset($this->cache[$parameters['Id']])) {
                $dataObject = $this->cache[$parameters['Id']];
            } else {
                $dataObject = Vpfw_Factory::getDataObject($doName, $parameters);
                if (true == is_null($parameters)) {
                    $this->toInsert[] = $dataObject;
                } else {
                    $this->cache[$dataObject->getId()] = $dataObject;
                }
            }
        }
        return $dataObject;
    }

    /**
     * Schreibt das übergebene DataObject in die Datenbank.
     *
     * Die spezifische Kindklasse kümmert sich um die Typsicherheit.
     *
     * @throws Vpfw_Exception_Logical
     * @throws Vpfw_Exception_Critical
     * @param Vpfw_DataObject_Interface $dataObject
     * @return void
     */
    protected function insert(Vpfw_DataObject_Interface $dataObject) {
        $data = $dataObject->exportData(Vpfw_DataObject_Interface::WITHOUT_ID);
        // -1 wegen der id
        if (count($data) < $dataObject->getCountOfRequiredColumns() - 1) {
            throw new Vpfw_Exception_Logical('Es wurden nicht alle Eigenschaften des DataObjects gefüllt');
        }

        $dataTypes = '';
        $values = array();
        if (count($data) == count($this->dataColumns) - 1) {
            $stmt = $this->db->prepare($this->sqlQueries['filledInsert']);
            foreach ($this->dataColumns as $colName => $dataType) {
                if ($colName != 'Id') {
                    $dataTypes .= $dataType;
                    if (true == array_key_exists($colName, $data)) {
                        $values[] = $data[$colName];
                    } else {
                        $values[] = null;
                    }
                }
            }
        } else {
            // Da nicht alle Eigenschaften des DataObjects gefüllt wurden, müssen wir zuerst einen passenden Query erzeugen
            $columnNames = '';
            $columnValues = '';
            $handledColumnCounter = 0;
            $countData = count($data);
            foreach ($data as $columnName => $columnValue) {
                if (false == array_key_exists($columnName, $this->dataColumns)) {
                    throw new Vpfw_Exception_Logical('Die exportData Methode liefert Spaltennamen die nicht bekannt sind');
                }
                $columnNames .= '`' . $columnName . '`';
                $columnValues .= '?';
                if ($handledColumnCounter != $countData - 1) {
                    $columnNames .= ',' . PHP_EOL;
                    $columnValues .= ',' . PHP_EOL;
                }
                $values[] = $columnValue;
                $dataTypes .= $this->dataColumns[$columnName];
                $handledColumnCounter++;
            }
            $stmt = $this->db->prepare(str_replace('{Values}', $columnValues, str_replace('{Columns}', $columnNames, $this->sqlQueries['insert'])));
        }


        array_unshift($values, $dataTypes);
        call_user_func_array(array($stmt, 'bind_param'), $values);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->errno) {
            throw new Vpfw_Exception_Critical('MySQL-Error: ' . $stmt->errno . ' (' . $stmt->error . ')');
        }
        if (true == is_null($stmt->insert_id)) {
            throw new Vpfw_Exception_Critical('Nach einem Insert in die Datenbank konnte die Insert-Id nicht ermittelt werden');
        }
        $dataObject->setId($stmt->insert_id, false);
        $dataObject->setSomethingChanged(false);
    }

    /**
     * Schreibt die Änderungen die an dem übergebenen DataObject vollzogen
     * wurden zurück in die Datenbank
     *
     * @throws Vpfw_Exception_Logical
     * @throws Vpfw_Exception_Critical
     * @param Vpfw_DataObject_Interface $dataObject
     * @return void
     */
    protected function update(Vpfw_DataObject_Interface $dataObject) {
        $data = $dataObject->exportData(Vpfw_DataObject_Interface::CHANGED);
        if (0 == count($data)) {
            throw new Vpfw_Exception_Logical('Wenn es keine veränderten Eigenschaften in diesem DataObject gibt, was treibt es dann hier in der update Methode?');
        }

        $dataTypes = '';
        $updateStmt = '';
        $values = array();
        $i = 0;
        $countData = count($data);
        foreach ($data as $colName => $value) {
            if (false == array_key_exists($colName, $this->dataColumns)) {
                throw new Vpfw_Exception_Logical('Die exportData Methode liefert Datenspalten welche nicht bekannt sind');
            }
            $dataTypes .= $this->dataColumns[$colName];
            $values[] = $value;
            $updateStmt .= $colName . ' = ?';
            if ($i != $countData - 1) {
                $updateStmt .= ',' . PHP_EOL;
            }
            $i++;
        }
        $dataTypes .= 'i';
        $values[] = $dataObject->getId();

        $stmt = $this->db->prepare(str_replace('{Updates}', $updateStmt, $this->sqlQueries['update']));
        array_unshift($values, $dataTypes);
        call_user_func_array(array($stmt, 'bind_param'), $values);
        $stmt->execute();
        if (0 == $stmt->affected_rows) {
            throw new Vpfw_Exception_Critical('Nach einem Update in der Datenbank liegt die Anzahl der veränderten Zeilen bei 0');
        }
        $dataObject->setSomethingChanged(false);
    }

    /**
     * Prüft zuerst den lokalen Cache auf das vorhandensein eines DataObjects
     * mit der übergebenen Id, wenn keines gefunden werden konnte und der
     * Parameter $autoLoad auf true steht wird die Datenbank nach einem
     * DataObject mit dieser Id durchsucht, sollte in beiden Fällen nichts
     * gefunden worden sein wird eine Exception vom Typ Vpfw_Exception_OutOfRange
     * geworfen.
     *
     * @throws Vpfw_Exception_OutOfRange
     * @param int $id Die zu suchende Id
     * @param bool $autoLoad Schalter ob in der Datenbank nach einem passenden Objekt gesucht werden soll
     * @return Vpfw_DataObject_Interface
     */
    public function getEntryById($id, $autoLoad = true) {
        if (true == array_key_exists($id, $this->cache)) {
            return $this->cache[$id];
        }
        if (false == $autoLoad) {
            throw new Vpfw_Exception_OutOfRange('In der Modelklasse ' . get_called_class() . ' ist kein DataObject mit der Id ' . $id . ' gecached');
        }
        $stmt = $this->db->prepare($this->sqlQueries['getById']);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->store_result();
        if (1 != $stmt->num_rows) {
            throw new Vpfw_Exception_OutOfRange('Konnte kein Element mit der Id ' . $id . ' in der Modelklasse ' . get_called_class() . ' finden.');
        }
        $resultArray = array();
        $metaData = $stmt->result_metadata();
        $params = array();
        while ($field = $metaData->fetch_field()) {
            $params[] = &$resultArray[$field->name];
        }
        call_user_func_array(array($stmt, 'bind_result'), $params);
        $stmt->fetch();
        $stmt->close();
        return $this->createEntry($resultArray);
    }

    /**
     * Liest alle Einträge aus der Datenbank und erstellt dann die DataObjects
     * aus den gesammelten Daten. Das Rückgabearray enthält nicht die Objekte
     * aus $this->toInsert
     *
     * @return array Array aus Vpfw_DataObject_Interface
     */
    public function getAllEntries() {
        $stmt = $this->db->prepare($this->sqlQueries['getAll']);
        $stmt->execute();
        $stmt->store_result();
        $metaData = $stmt->result_metadata();
        $params = array();
        $row = array();
        while ($field = $metaData->fetch_field()) {
            $params[] = &$row[$field->name];
        }
        call_user_func_array(array($stmt, 'bind_result'), $params);

        $toReturn = array();
        while ($stmt->fetch()) {
            if (false == isset($this->cache[$row['Id']])) {
                $toReturn[] = $this->createEntry($row);
            } else {
                $toReturn[] = $this->cache[$row['Id']];
            }
        }
        $stmt->close();
        return $toReturn;
    }

    /**
     * Löscht ein DataObject aus dem lokalen Cache und aus der Datenbank,
     * wenn kein passendes DataObject gefunden werden konnte und der Schalter
     * $strict auf true steht wird eine Exception vom Typ
     * Vpfw_Exception_OutOfRange geworfen.
     *
     * @throws Vpfw_Exception_OutOfRange
     * @param int $id
     * @param bool $strict
     * @return void
     */
    public function deleteEntryById($id, $strict = false) {
        if (true == isset($this->cache[$id])) {
            $this->cache[$id]->notifyObserver();
        }

        $stmt = $this->db->prepare($this->sqlQueries['delById']);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        if (0 == $stmt->affected_rows) {
            throw new Vpfw_Exception_OutOfRange('Konnte kein Element mit der Id ' . $id . ' in der Modelklasse ' . get_called_class() . ' finden');
        }
    }

    /**
     * Löscht alle DataObjects aus dem lokalen Cache, $this->toInsert und
     * aus der Datenbank.
     *
     * @return void
     */
    public function deleteAllEntries() {
        foreach ($this->cache as $dataObject) {
            $dataObject->notifyObserver();
        }
        foreach ($this->toInsert as $dataObject) {
            $dataObject->notifyObserver();
        }
        $stmt = $this->db->prepare($this->sqlQueries['delById']);
        $stmt->execute();
    }

    /**
     * Sucht in der Datenbank nach Übereinstimmungen mit den übergebenen
     * Anforderungen und gibt diese dann in einem Array zurück auch wenn nur
     * eine Übereinstimmung gefunden wurde,
     *
     * Sucht nur in der Datenbank, nicht im lokalen Cache oder in $this->toInsert
     *
     * @param array $fieldValues
     * @return array Array aus Vpfw_DataObject_Interface
     */
    public function getEntriesByFieldValue(array $fieldValues) {
        // Aufbauen von $whereClause, $paramTypes und $values
        list($paramTypes, $whereClause, $values) = self::parseFieldValues($fieldValues);

        $stmt = $this->db->prepare(str_replace('{WhereClause}', $whereClause, $this->sqlQueries['getByFv']));
        array_unshift($values, $paramTypes);
        call_user_func_array(array($stmt, 'bind_param'), $values);
        $stmt->execute();
        $stmt->store_result();

        $params = array();
        $row = array();
        $metaData = $stmt->result_metadata();
        while ($field = $metaData->fetch_field()) {
            $params[] = &$row[$field->name];
        }
        call_user_func_array(array($stmt, 'bind_result'), $params);

        $toReturn = array();
        while ($stmt->fetch()) {
            if (false == isset($this->cache[$row['Id']])) {
                $toReturn[] = $this->createEntry($row);
            } else {
                $toReturn[] = $this->cache[$row['Id']];
            }
        }
        $stmt->close();
        return $toReturn;
    }

    /**
     * Prüft ob ein DataObject das mit den übergebenen Anforderungen
     * übereinstimmt in der Datenbank existiert.
     *
     * Prüft nicht den lokalen Cache oder $this->toInsert
     * 
     * @param array $fieldValues
     * @return bool
     */
    public function entryWithFieldValuesExists(array $fieldValues) {
        list($paramTypes, $whereClause, $values) = self::parseFieldValues($fieldValues);
        $stmt = $this->db->prepare(str_replace('{WhereClause}', $whereClause, $this->sqlQueries['fvExists']));
        array_unshift($values, $paramTypes);
        call_user_func_array(array($stmt, 'bind_param'), $values);
        $stmt->execute();
        $stmt->store_result(); // Muss ausgeführt werden, da die Eigenschaft num_rows sonst immer null bleibt.
        if (0 != $stmt->num_rows) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Löscht DataObjects aus der Datenbank, wenn diese mit den übergebenen
     * Anforderungen übereinstimmen.
     *
     * Löscht nicht aus dem lokalen Cache oder aus $this->toInsert
     *
     * @throws Vpfw_Exception_OutOfRange
     * @param array $fieldValues
     * @param bool $strict Sollte die Anzahl der gelöschten DataObjects nicht mit der Anzahl der Anforderungen übereinstimmen und diesr Schalter ist auf true gestellt wird eine Exception vom Typ Vpfw_Exception_OutOfRange geworfen.
     * @return void
     */
    public function deleteEntriesByFieldValue(array $fieldValues, $strict = false) {
        list($paramTypes, $whereClause, $values) = self::parseFieldValues($fieldValues);
        $stmt = $this->db->prepare(str_replace('{WhereClause}', $whereClause, $this->sqlQueries['delByFv']));
        array_unshift($values, $paramTypes);
        call_user_func_array(array($stmt, 'bind_param'), $values);
        $stmt->execute();
        if (true == $strict) {
            if (count($fieldValues) != $stmt->affected_rows) {
                throw new Vpfw_Exception_OutOfRange('Es konnten nicht alle Elemente aus der Datenbank gelöscht werden');
            }
        }
    }

    /**
     * Wenn es sich bei $fieldValues um ein eindimensionales Array handelt,
     * werden die Arrayelemente mit einem OR verknüpft. Sollte es sich jedoch um
     * ein zweidimensionales Array handeln, werden die Elemente in der ersten
     * Dimension mit einem OR und die Elemente in der zweiten Dimension mit
     * einem AND verknüpft.
     *
     * Beispiel:
     * array(
     *     'i|Id|4',
     *     'i|Id|5',
     * );
     * Resultiert in:
     * WHERE
     *     Id = 4 OR
     *     Id = 5
     *
     * array(
     *     array(
     *         's|Name|aloha',
     *         'i|Iq|50',
     *     ),
     *     array(
     *         's|Name|blubber',
     *         'i|Iq|60',
     *     ),
     * );
     * Resultiert in:
     * WHERE
     *     (Name = aloha AND Iq = 50) OR
     *     (Name = blubber AND Iq = 60)
     * @param array $fieldValues
     * @return array Beinhaltet die Parametertypen und die WhereClause als String und die Values als array
     */
    protected static function parseFieldValues(array $fieldValues) {
        $paramTypes = '';
        $whereClause = '';
        $values = array();
        $countValues = count($fieldValues);
        foreach ($fieldValues as $key => $value) {
            if (true == is_array($value)) {
                $whereClause .= '(';
                $countSubValues = count($value);
                foreach ($value as $subKey => $subValue) {
                    list($dataType, $fieldName, $fieldValue) = explode('|', $subValue, 3);
                    $paramTypes .= $dataType;
                    $values[] = $fieldValue;
                    $whereClause .= $fieldName . ' = ?';
                    if ($countSubValues - 1 != $subKey) {
                        $whereClause .= ' AND ';
                    }
                }
                $whereClause .= ')';
            } else {
                list($dataType, $fieldName, $fieldValue) = explode('|', $value, 3);
                $paramTypes .= $dataType;
                $values[] = $fieldValue;
                $whereClause .= $fieldName . ' = ?';
            }
            if ($countValues - 1 != $key) {
                $whereClause .= ' OR' . PHP_EOL;
            }
        }
        return array($paramTypes, $whereClause, $values);
    }

    /**
     * Methode die vom Vpfw_Cleaner ausgeführt wird und
     * neue bzw. veränderte DataObjects in die Datenbank
     * zurückschreibt.
     *
     * @return void
     */
    public function clean() {
        foreach ($this->toInsert as $dataObject) {
            $this->insert($dataObject);
        }

        foreach ($this->cache as $dataObject) {
            if (true == $dataObject->isSomethingChanged()) {
                $this->update($dataObject);
            }
        }
    }
}