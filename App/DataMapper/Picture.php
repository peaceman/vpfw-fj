<?php
class App_DataMapper_Picture extends Vpfw_DataMapper_Abstract {
    protected function fillDetailData() {
        $this->dataColumns = array(
            'Id' => 'i',
            'Md5' => 's',
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
                                            b.UserAgent AS SesUserAgent
                                        FROM
                                            {TableName} AS a
                                        INNER JOIN
                                            session AS b ON
                                            a.SessionId = b.Id
                                        WHERE
                                            a.Id = ?';
        $this->sqlQueries['getByUserId'] = 'SELECT
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
                                                b.UserAgent AS SesUserAgent
                                            FROM
                                                {TableName} AS a
                                            INNER JOIN
                                                session AS b ON
                                                a.SessionId = b.Id
                                            WHERE
                                                a.DeletionId IS NULL AND
                                                b.UserId = ?';
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
                                            b.UserAgent AS SesUserAgent
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
                                            b.UserAgent AS SesUserAgent
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
        $this->sqlQueries['get2RndByGender'] = 'SELECT
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
                                                    b.UserAgent AS SesUserAgent
                                                FROM
                                                    {TableName} AS a
                                                INNER JOIN
                                                    session AS b ON
                                                    a.SessionId = b.Id
                                                WHERE
                                                    a.DeletionId IS NULL AND
                                                    a.Gender = ?
                                                ORDER BY
                                                    RAND()
                                                LIMIT 2';
        $this->sqlQueries['getTop10ByGender'] = 'SELECT
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
                                                    b.UserAgent AS SesUserAgent
                                                FROM
                                                    {TableName} AS a
                                                INNER JOIN
                                                    session AS b ON
                                                    a.SessionId = b.Id
                                                WHERE
                                                    a.DeletionId IS NULL AND
                                                    a.Gender = ?
                                                ORDER BY
                                                    a.PositiveRating - a.NegativeRating DESC
                                                LIMIT 10';
    }

    public function getTwoRandomPictures($gender) {
        if (is_string($gender)) {
            switch ($gender) {
                case 'male':
                    $gender = 0;
                    break;
                case 'female':
                    $gender = 1;
                    break;
            }
        }
        $stmt = $this->db->prepare($this->sqlQueries['get2RndByGender']);
        $stmt->bind_param('i', $gender);
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

    public function getTop10ByGender($gender) {
        $stmt = $this->db->prepare($this->sqlQueries['getTop10ByGender']);
        $stmt->bind_param('i', $gender);
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

    public function getEntriesByUserId($id) {
        $stmt = $this->db->prepare($this->sqlQueries['getByUserId']);
        $stmt->bind_param('i', $id);
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
}
 
