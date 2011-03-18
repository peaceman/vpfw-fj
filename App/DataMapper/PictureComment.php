<?php
class App_DataMapper_PictureComment extends Vpfw_DataMapper_Abstract {
    protected function fillDetailData() {
        $this->dataColumns = array(
            'Id' => 'i',
            'SessionId' => 'i',
            'PictureId' => 'i',
            'DeletionId' => 'i',
            'Time' => 'i',
            'Text' => 's',
        );
        $this->tableName = 'picture_comment';
        $this->sqlQueries['getById'] = 'SELECT
                                            a.Id,
                                            a.SessionId,
                                            a.PictureId,
                                            a.DeletionId,
                                            a.Time,
                                            a.Text,
                                            b.UserId AS SesUserId,
                                            b.Ip AS SesIp,
                                            b.StartTime AS SesStartTime,
                                            b.LastRequest AS SesLastRequest,
                                            b.Hits AS SesHits,
                                            b.UserAgent AS SesUserAgent,
                                            c.Md5 AS PicMd5,
                                            c.Gender AS PicGender,
                                            c.SessionId AS PicSessionId,
                                            c.UploadTime AS PicUploadTime,
                                            c.SiteHits AS PicSiteHits,
                                            c.PositiveRating AS PicPositiveRating,
                                            c.NegativeRating AS PicNegativeRating,
                                            c.DeletionId AS PicDeletionId
                                        FROM
                                            {TableName} AS a
                                        INNER JOIN
                                            session AS b ON
                                            a.SessionId = b.Id
                                        INNER JOIN
                                            picture AS c ON
                                            a.PictureId = c.Id
                                        WHERE
                                            a.Id = ?';
        $this->sqlQueries['getByFv'] = 'SELECT
                                            a.Id,
                                            a.SessionId,
                                            a.PictureId,
                                            a.DeletionId,
                                            a.Time,
                                            a.Text,
                                            b.UserId AS SesUserId,
                                            b.Ip AS SesIp,
                                            b.StartTime AS SesStartTime,
                                            b.LastRequest AS SesLastRequest,
                                            b.Hits AS SesHits,
                                            b.UserAgent AS SesUserAgent,
                                            c.Md5 AS PicMd5,
                                            c.Gender AS PicGender,
                                            c.SessionId AS PicSessionId,
                                            c.UploadTime AS PicUploadTime,
                                            c.SiteHits AS PicSiteHits,
                                            c.PositiveRating AS PicPositiveRating,
                                            c.NegativeRating AS PicNegativeRating,
                                            c.DeletionId AS PicDeletionId
                                        FROM
                                            {TableName} AS a
                                        INNER JOIN
                                            session AS b ON
                                            a.SessionId = b.Id
                                        INNER JOIN
                                            picture AS c ON
                                            a.PictureId = c.Id
                                        WHERE
                                            a.DeletionId IS NULL AND
                                            {WhereClause}';
        $this->sqlQueries['getAll'] = 'SELECT
                                            a.Id,
                                            a.SessionId,
                                            a.PictureId,
                                            a.DeletionId,
                                            a.Time,
                                            a.Text,
                                            b.UserId AS SesUserId,
                                            b.Ip AS SesIp,
                                            b.StartTime AS SesStartTime,
                                            b.LastRequest AS SesLastRequest,
                                            b.Hits AS SesHits,
                                            b.UserAgent AS SesUserAgent,
                                            c.Md5 AS PicMd5,
                                            c.Gender AS PicGender,
                                            c.SessionId AS PicSessionId,
                                            c.UploadTime AS PicUploadTime,
                                            c.SiteHits AS PicSiteHits,
                                            c.PositiveRating AS PicPositiveRating,
                                            c.NegativeRating AS PicNegativeRating,
                                            c.DeletionId AS PicDeletionId
                                        FROM
                                            {TableName} AS a
                                        INNER JOIN
                                            session AS b ON
                                            a.SessionId = b.Id
                                        INNER JOIN
                                            picture AS c ON
                                            a.PictureId = c.Id
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

    public function getCommentsFromUserId($userId) {
        $sql = 'SELECT
                    a.Id,
                    a.SessionId,
                    a.PictureId,
                    a.DeletionId,
                    a.Time,
                    a.Text
                FROM
                    picture_comment AS a
                INNER JOIN
                    session AS b ON
                    a.SessionId = b.Id
                INNER JOIN
                    user AS c ON
                    b.UserId = c.Id
                WHERE
                    c.Id = ?';

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i', $userId);
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
            $toReturn[] = $this->createEntry($row);
        }
        $stmt->close();
        return $toReturn;
    }
}