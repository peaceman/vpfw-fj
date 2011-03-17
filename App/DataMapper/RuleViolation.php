<?php
class App_DataMapper_RuleViolation extends Vpfw_DataMapper_Abstract {
    protected function fillDetailData() {
        $this->dataColumns = array(
            'Id' => 'i',
            'PictureId' => 'i',
            'SessionId' => 'i',
            'Time' => 'i',
            'Handled' => 'i',
            'Reason' => 's',
        );
        $this->tableName = 'ruleviolation';
        $this->sqlQueries['getById'] = 'SELECT
                                            a.Id,
                                            a.PictureId,
                                            a.SessionId,
                                            a.Time,
                                            a.Handled,
                                            a.Reason,
                                            b.Md5 AS PicMd5,
                                            b.Gender AS PicGender,
                                            b.SessionId AS PicSessionId,
                                            b.UploadTime AS PicUploadTime,
                                            b.SiteHits AS PicSiteHits,
                                            b.PositiveRating AS PicPositiveRating,
                                            b.NegativeRating AS PicNegativeRating,
                                            b.DeletionId AS PicDeletionId,
                                            c.UserId AS SesUserId,
                                            c.Ip AS SesIp,
                                            c.StartTime AS SesStartTime,
                                            c.LastRequest AS SesLastRequest,
                                            c.Hits AS SesHits,
                                            c.UserAgent AS SesUserAgent
                                        FROM
                                            {TableName} AS a
                                        INNER JOIN
                                            picture AS b ON
                                            a.PictureId = b.Id
                                        INNER JOIN
                                            session AS c ON
                                            a.SessionId = c.Id
                                        WHERE
                                            a.Id = ?';
        $this->sqlQueries['getByFv'] = 'SELECT
                                            a.Id,
                                            a.PictureId,
                                            a.SessionId,
                                            a.Time,
                                            a.Handled,
                                            a.Reason,
                                            b.Md5 AS PicMd5,
                                            b.Gender AS PicGender,
                                            b.SessionId AS PicSessionId,
                                            b.UploadTime AS PicUploadTime,
                                            b.SiteHits AS PicSiteHits,
                                            b.PositiveRating AS PicPositiveRating,
                                            b.NegativeRating AS PicNegativeRating,
                                            b.DeletionId AS PicDeletionId,
                                            c.UserId AS SesUserId,
                                            c.Ip AS SesIp,
                                            c.StartTime AS SesStartTime,
                                            c.LastRequest AS SesLastRequest,
                                            c.Hits AS SesHits,
                                            c.UserAgent AS SesUserAgent
                                        FROM
                                            {TableName} AS a
                                        INNER JOIN
                                            picture AS b ON
                                            a.PictureId = b.Id
                                        INNER JOIN
                                            session AS c ON
                                            a.SessionId = c.Id
                                        WHERE
                                            {WhereClause}';
        $this->sqlQueries['getByAll'] = 'SELECT
                                            a.Id,
                                            a.PictureId,
                                            a.SessionId,
                                            a.Time,
                                            a.Handled,
                                            a.Reason,
                                            b.Md5 AS PicMd5,
                                            b.Gender AS PicGender,
                                            b.SessionId AS PicSessionId,
                                            b.UploadTime AS PicUploadTime,
                                            b.SiteHits AS PicSiteHits,
                                            b.PositiveRating AS PicPositiveRating,
                                            b.NegativeRating AS PicNegativeRating,
                                            b.DeletionId AS PicDeletionId,
                                            c.UserId AS SesUserId,
                                            c.Ip AS SesIp,
                                            c.StartTime AS SesStartTime,
                                            c.LastRequest AS SesLastRequest,
                                            c.Hits AS SesHits,
                                            c.UserAgent AS SesUserAgent
                                        FROM
                                            {TableName} AS a
                                        INNER JOIN
                                            picture AS b ON
                                            a.PictureId = b.Id
                                        INNER JOIN
                                            session AS c ON
                                            a.SessionId = c.Id';
    }

    public function getUnhandledRuleViolations() {
        return $this->getEntriesByFieldValue(array('i|Handled|0'));
    }

    public function getHandledRuleViolations() {
        return $this->getEntriesByFieldValue(array('i|Handled|1'));
    }

    public function getRuleViolationsForPictureId($pictureId) {
        return $this->getEntriesByFieldValue(array('i|PictureId|' . $pictureId));
    }

    public function getRuleViolationsForSessionId($sessionId) {
        return $this->getEntriesByFieldValue(array('i|SessionId|' . $sessionId));
    }

    public function getRuleViolationsForUserId($userId) {
        $sql = 'SELECT
                    a.Id,
                    a.PictureId,
                    a.SessionId,
                    a.SessionId,
                    a.Time,
                    a.Handled,
                    a.Reason
                FROM
                    ruleviolation AS a
                INNER JOIN
                    session AS b ON
                    a.SessionId = b.Id
                INNER JOIN
                    user AS c ON
                    c.Id = b.UserId
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
        call_user_func(array($stmt, 'bind_result'), $params);

        $toReturn = array();
        while ($stmt->fetch()) {
            $toReturn = $this->createEntry($row);
        }
        $stmt->close();
        return $toReturn;
    }
}
 
