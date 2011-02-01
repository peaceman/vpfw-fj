<?php
class Vpfw_Database_MysqlStmt extends Vpfw_Abstract_Loggable {
    /**
     *
     * @var mysqli_stmt
     */
    private $stmt;
    
    /**
     *
     * @var string
     */
    private $qry;
    
    /**
     * 
     * @var Vpfw_Database_Mysql
     */
    private $mysql;
    
    /**
     * @param mysqli_stmt $stmt
     * @param string $qry 
     */
    public function __construct(mysqli_stmt $stmt, $qry, Vpfw_Database_Mysql $mysql) {
        $this->stmt = $stmt;
        $this->qry = $qry;
        $this->mysql = $mysql;
    }
    
    public function __call($name, $arguments) {
        return call_user_func_array(array($this->stmt, $name), $arguments);
    }
    
    /**
     * Ermöglicht das Loggen der Queries
     * 
     * @return bool
     */
    public function execute() {
        $state = $this->stmt->execute();
        $this->qry = implode(' ', preg_split('#[\s]+#', $this->qry));        
        $this->mysql->addExecutedQuery($this->qry);
        return $state;
    }
    
    /**
     * @param string $types
     * @return bool 
     */
    public function bind_param($types) {
        // Ersetze die Fragezeichen im SQL-Query durch ihre printf Formatkennzeichner
        $typesArr = str_split($types);
        foreach ($typesArr as &$type) {
            switch ($type) {
                case 'i':
                    $type = '%d';
                    break;
                case 's':
                case 'b':
                    $type = "'%s'";
                    break;
                default:
                    throw new Vpfw_Exception_Logical('Undefinierter Parametertyp ' . $type .' in ' . __METHOD__);        
            }
        }
        while (false !== $pos = strpos($this->qry, '?')) {
            $this->qry = substr_replace($this->qry, array_shift($typesArr), $pos, 1);
        }
        $args = func_get_args();
        array_shift($args);
        array_unshift($args, $this->qry);
        // Allmighty sprintf macht den Rest
        $this->qry = call_user_func_array('sprintf', $args);
        return call_user_func_array(array($this->stmt, 'bind_param'), func_get_args());
    }
    
    /**
     * 
     * @return string
     */
    public function getQry() {
        return $this->qry;
    }

    public function __get($name) {
        return $this->stmt->$name;
    }

    public function __set($name, $value) {
        $this->stmt->$name = $value;
    }
}