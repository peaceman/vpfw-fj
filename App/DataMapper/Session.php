<?php
class App_DataMapper_Session extends Vpfw_DataMapper_Abstract {
    protected function fillDetailData() {
        $this->dataColumns = array(
            'Id' => 'i',
            'UserId' => 'i',
            'Ip' => 'i',
            'StartTime' => 'i',
            'LastRequest' => 'i',
            'Hits' => 'i',
            'UserAgent' => 's',
        );
        $this->tableName = 'session';
        $this->sqlQueries['getById'] = 'SELECT
                                            a.Id,
                                            a.UserId,
                                            a.Ip,
                                            a.StartTime,
                                            a.LastRequest,
                                            a.Hits,
                                            a.UserAgent,
                                            b.CreationTime,
                                            b.CreationIp,
                                            b.DeletionId,
                                            b.Username,
                                            b.Passhash,
                                            b.Email
                                        FROM
                                            session AS a
                                        LEFT JOIN
                                            user AS b ON
                                            a.UserId = b.Id
                                        WHERE
                                            a.Id = ?';
        $this->sqlQueries['getByFv'] = 'SELECT
                                            a.Id,
                                            a.UserId,
                                            a.Ip,
                                            a.StartTime,
                                            a.LastRequest,
                                            a.Hits,
                                            a.UserAgent,
                                            b.CreationTime,
                                            b.CreationIp,
                                            b.DeletionId,
                                            b.Username,
                                            b.Passhash,
                                            b.Email
                                        FROM
                                            session AS a
                                        LEFT JOIN
                                            user AS b ON
                                            a.UserId = b.Id
                                        WHERE
                                            {WhereClause}';
        $this->sqlQueries['getAll'] = 'SELECT
                                            a.Id,
                                            a.UserId,
                                            a.Ip,
                                            a.StartTime,
                                            a.LastRequest,
                                            a.Hits,
                                            a.UserAgent,
                                            b.CreationTime,
                                            b.CreationIp,
                                            b.DeletionId,
                                            b.Username,
                                            b.Passhash,
                                            b.Email
                                        FROM
                                            session AS a
                                        LEFT JOIN
                                            user AS b ON
                                            a.UserId = b.Id';
    }
}
 
