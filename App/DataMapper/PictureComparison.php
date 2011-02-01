<?php
class App_DataMapper_PictureComparison extends Vpfw_DataMapper_Abstract {
    public function fillDetailData() {
        $this->dataColums = array(
            'Id' => 'i',
            'PictureId1' => 'i',
            'PictureId2' => 'i',
            'Winner' => 'i',
        );
        $this->tableName = 'picture_comparison';
        $this->sqlQueries['getById'] = 'SELECT
                                            a.Id,
                                            a.PictureId1,
                                            a.PictureId2,
                                            a.Winner,
                                            b.Md5 AS Pic1Md5,
                                            b.Gender AS Pic1Gender,
                                            b.SessionId AS Pic1SessionId,
                                            b.UploadTime AS Pic1UploadTime,
                                            b.SiteHits AS Pic1SiteHits,
                                            b.PositiveRating AS Pic1PositiveRating,
                                            b.NegativeRating AS Pic1NegativeRating,
                                            b.DeletionId AS Pic1DeletionId,
                                            c.Md5 AS Pic2Md5,
                                            c.Gender AS Pic2Gender,
                                            c.SessionId AS Pic2SessionId,
                                            c.UploadTime AS Pic2UploadTime,
                                            c.SiteHits AS Pic2SiteHits,
                                            c.PositiveRating AS Pic2PositiveRating,
                                            c.NegativeRating AS Pic2NegativeRating,
                                            c.DeletionId AS Pic2DeletionId
                                        FROM
                                            {TableName} AS a
                                        INNER JOIN
                                            picture AS b ON
                                            b.Id = a.PictureId1
                                        INNER JOIN
                                            picture AS c ON
                                            c.Id = a.PictureId2
                                        WHERE
                                            Id = ?';
        $this->sqlQueries['getAll'] = 'SELECT
                                            a.Id,
                                            a.PictureId1,
                                            a.PictureId2,
                                            a.Winner,
                                            b.Md5 AS Pic1Md5,
                                            b.Gender AS Pic1Gender,
                                            b.SessionId AS Pic1SessionId,
                                            b.UploadTime AS Pic1UploadTime,
                                            b.SiteHits AS Pic1SiteHits,
                                            b.PositiveRating AS Pic1PositiveRating,
                                            b.NegativeRating AS Pic1NegativeRating,
                                            b.DeletionId AS Pic1DeletionId,
                                            c.Md5 AS Pic2Md5,
                                            c.Gender AS Pic2Gender,
                                            c.SessionId AS Pic2SessionId,
                                            c.UploadTime AS Pic2UploadTime,
                                            c.SiteHits AS Pic2SiteHits,
                                            c.PositiveRating AS Pic2PositiveRating,
                                            c.NegativeRating AS Pic2NegativeRating,
                                            c.DeletionId AS Pic2DeletionId
                                        FROM
                                            {TableName} AS a
                                        INNER JOIN
                                            picture AS b ON
                                            b.Id = a.PictureId1
                                        INNER JOIN
                                            picture AS c ON
                                            c.Id = a.PictureId2
                                        WHERE
                                            Id = ?';
    }
}
