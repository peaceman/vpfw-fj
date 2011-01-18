<?php
class App_DataMapper_Picture extends Vpfw_DataMapper_Abstract {
    protected function fillDetailData() {
        $this->dataColumns = array(
            'Id' => 'i',
            'Md5' => 'b',
            'Gender' => 'i',
            'SessionId' => 'i',
            'UploadTime' => 'i',
            'SiteHits' => 'i',
            'PositiveRating' => 'i',
            'NegativeRating' => 'i',
            'DeletionId' => 'i',
        );
        $this->tableName = 'picture';
        $this->sqlQueries['getById'] = 'SELECT
                                            a.Id,
                                            a.Md5,
                                            a.Gender,
                                            a.SessionId,
                                            a.UploadTime,
                                            a.SiteHits,
                                            a.PositiveRating,
                                            a.NegativeRating,
                                            a.DeletionId,
                                            b.UserId AS SesUserId,
                                            b.Ip AS SesIp,
                                            b.StartTime AS SesStartTime,
                                            b.LastRequest AS SesLastRequest,
                                            b.Hits AS SesHits,
                                            b.UserAgent AS SesUserAgent,
                                            b.DeletionId AS SesDeletionId
                                        FROM
                                            {TableName} AS a
                                        INNER JOIN
                                            session AS b ON
                                            a.SessionId = b.Id
                                        WHERE
                                            a.Id = ?';
        $this->sqlQueries['getByFV'] = 'SELECT
                                            a.Id,
                                            a.Md5,
                                            a.Gender,
                                            a.SessionId,
                                            a.UploadTime,
                                            a.SiteHits,
                                            a.PositiveRating,
                                            a.NegativeRating,
                                            a.DeletionId,
                                            b.UserId AS SesUserId,
                                            b.Ip AS SesIp,
                                            b.StartTime AS SesStartTime,
                                            b.LastRequest AS SesLastRequest,
                                            b.Hits AS SesHits,
                                            b.UserAgent AS SesUserAgent,
                                            b.DeletionId AS SesDeletionId
                                        FROM
                                            {TableName} AS a
                                        INNER JOIN
                                            session AS b ON
                                            a.SessionId = b.Id
                                        WHERE
                                            a.DeletionId IS NULL AND
                                            {WhereClause}';
        $this->sqlQueries['getAll'] = 'SELECT
                                            a.Id,
                                            a.Md5,
                                            a.Gender,
                                            a.SessionId,
                                            a.UploadTime,
                                            a.SiteHits,
                                            a.PositiveRating,
                                            a.NegativeRating,
                                            a.DeletionId,
                                            b.UserId AS SesUserId,
                                            b.Ip AS SesIp,
                                            b.StartTime AS SesStartTime,
                                            b.LastRequest AS SesLastRequest,
                                            b.Hits AS SesHits,
                                            b.UserAgent AS SesUserAgent,
                                            b.DeletionId AS SesDeletionId
                                        FROM
                                            {TableName} AS a
                                        INNER JOIN
                                            session AS b ON
                                            a.SessionId = b.Id
                                        WHERE
                                            a.DeletionId IS NULL';
        $this->sqlQueries['fvExists'] = 'SELECT
                                             Id
                                         FROM
                                             {TableName}
                                         WHERE
                                             DeletionId IS NULL AND
                                             {WhereClause}';
    }
}
 
