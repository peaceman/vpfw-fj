<?php
/**
 * Created by PhpStorm.
 * User: naegele.nico
 * Date: 07.01.2011
 * Time: 16:11:09
 */
class App_DataMapper_Deletion extends Vpfw_DataMapper_Abstract {
    protected function fillDetailData() {
        $this->dataColumns = array(
            'Id' => 'i',
            'SessionId' => 'i',
            'Time' => 'i',
            'Reason' => 's',
        );
        $this->tableName = 'deletion';
        $this->sqlQueries['getById'] = 'SELECT
                                            a.Id,
                                            a.SessionId,
                                            a.Time,
                                            a.Reason
                                            b.UserId,
                                            b.Ip,
                                            b.StartTime,
                                            b.LastRequest,
                                            b.Hits,
                                            b.UserAgent
                                        FROM
                                            deletion AS a
                                        INNER JOIN
                                            session AS b ON
                                            a.SessionId = b.Id
                                        WHERE
                                            a.Id = ?';
        $this->sqlQueries['getByFv'] = 'SELECT
                                            a.Id,
                                            a.SessionId,
                                            a.Time,
                                            a.Reason,
                                            b.UserId,
                                            b.Ip,
                                            b.StartTime,
                                            b.LastRequest,
                                            b.Hits,
                                            b.UserAgent
                                        FROM
                                            deletion AS a
                                        INNER JOIN
                                            session AS b ON
                                            a.SessionId = b.Id
                                        WHERE
                                            {WhereClause}';
        $this->sqlQueries['getAll'] = 'SELECT
                                           a.Id,
                                           a.SessionId,
                                           a.Time,
                                           a.Reason,
                                           b.UserId,
                                           b.Ip,
                                           b.StartTime,
                                           b.LastRequest,
                                           b.Hits,
                                           b.UserAgent
                                       FROM
                                           deletion AS a
                                       INNER JOIN
                                           session AS b ON
                                           a.SessionId = b.Id';
    }
}
 
