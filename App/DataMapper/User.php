<?php
class App_DataMapper_User extends Vpfw_DataMapper_Abstract {
    protected function fillDetailData() {
        $this->dataColumns = array(
            'Id' => 'i',
            'CreationTime' => 'i',
            'CreationIp' => 'i',
            'DeletionId' => 'i',
            'Username' => 's',
            'Passhash' => 'b', //b für blob, weil passhash binärdaten enthält
            'Email' => 's',
        );
        $this->tableName = 'user';
        
        $this->sqlQueries['getByFv'] = 'SELECT
                                            {Columns}
                                        FROM
                                            {TableName}
                                        WHERE
                                            DeletionId IS NULL AND
                                            {WhereClause}';
        $this->sqlQueries['getAll'] = 'SELECT
                                           {Columns}
                                       FROM
                                           {TableName}
                                       WHERE
                                           DeletionId IS NULL';
        $this->sqlQueries['fvExists'] = 'SELECT
                                             Id
                                         FROM
                                             {TableName}
                                         WHERE
                                             DeletionId IS NULL AND
                                             {WhereClause}';
    }
}
