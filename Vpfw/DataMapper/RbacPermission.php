<?php
class Vpfw_DataMapper_RbacPermission extends Vpfw_DataMapper_Abstract {
    public function fillDetailData() {
        $this->dataColumns = array(
            'Id' => 'i',
            'RoleId' => 'i',
            'ObjectId' => 'i',
            'State' => 'i',
        );
        $this->tableName = 'rbac_permission';
        $this->sqlQueries['getById'] = 'SELECT
                                            a.Id,
                                            a.RoleId,
                                            a.ObjectId,
                                            a.State,
                                            b.Name AS RoleName,
                                            c.Default AS ObjectDefault,
                                            c.Name AS ObjectName
                                        FROM
                                            {TableName} AS a
                                        INNER JOIN
                                            rbac_role AS b ON
                                            a.RoleId = b.Id
                                        INNER JOIN
                                            rbac_object AS c ON
                                            a.ObjectId = c.Id
                                        WHERE
                                            a.Id = ?';
        $this->sqlQueries['getByFv'] = 'SELECT
                                            a.Id,
                                            a.RoleId,
                                            a.ObjectId,
                                            a.State,
                                            b.Name AS RoleName,
                                            c.Default AS ObjectDefault,
                                            c.Name AS ObjectName
                                        FROM
                                            {TableName} AS a
                                        INNER JOIN
                                            rbac_role AS b ON
                                            a.RoleId = b.Id
                                        INNER JOIN
                                            rbac_object AS c ON
                                            a.ObjectId = c.Id
                                        WHERE
                                            {WhereClause}';
        $this->sqlQueries['getAll'] = 'SELECT
                                            a.Id,
                                            a.RoleId,
                                            a.ObjectId,
                                            a.State,
                                            b.Name AS RoleName,
                                            c.Default AS ObjectDefault,
                                            c.Name AS ObjectName
                                        FROM
                                            {TableName} AS a
                                        INNER JOIN
                                            rbac_role AS b ON
                                            a.RoleId = b.Id
                                        INNER JOIN
                                            rbac_object AS c ON
                                            a.ObjectId = c.Id';
    }
}