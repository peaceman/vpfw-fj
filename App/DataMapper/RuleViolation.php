<?php
class App_DataMapper_RuleViolation extends Vpfw_DataMapper_Abstract {
    protected function fillDetailData() {
        $this->dataColumns = array(
            'Id' => 'i',
            'PictureId' => 'i',
            'SessionId' => 'i',
            'Handled' => 'i',
            'Reason' => 's',
        );
        $this->tableName = 'ruleviolation';
        $this->sqlQueries['getById'] = 'SELECT
                                            a.Id,
                                            a.PictureId,
                                            a.SessionId,
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
                                            c.Ip AS SesUserIp,
                                            c.StartTime AS SesStartTime,
                                            c.LastReqeust AS SesLastRequest,
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
                                            Id = ?';
        $this->sqlQueries['getByFv'] = 'SELECT
                                            a.Id,
                                            a.PictureId,
                                            a.SessionId,
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
                                            c.Ip AS SesUserIp,
                                            c.StartTime AS SesStartTime,
                                            c.LastReqeust AS SesLastRequest,
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
                                            c.Ip AS SesUserIp,
                                            c.StartTime AS SesStartTime,
                                            c.LastReqeust AS SesLastRequest,
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
}
 
