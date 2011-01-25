<?php
class Vpfw_DataMapper_RbacRole extends Vpfw_DataMapper_Abstract {
    protected function fillDetailData() {
        $this->dataColumns = array(
            'Id' => 'i',
            'Name' => 's',
        );
        $this->tableName = 'rbac_role';
        $this->sqlQueries['getById'] = "SELECT
                                            a.Id,
                                            a.Name,
                                            GROUP_CONCAT(b.Id) AS PermIds,
                                            GROUP_CONCAT(b.ObjectId) AS PermObjectIds,
                                            GROUP_CONCAT(b.State) AS PermStates
                                        FROM
                                            {TableName} AS a
                                        LEFT JOIN
                                            rbac_permission AS b ON
                                            a.Id = b.RoleId
                                        WHERE
                                            a.Id = ?
                                        GROUP BY
                                            a.Id";
        $this->sqlQueries['getByFv'] = "SELECT
                                            a.Id,
                                            a.Name,
                                            GROUP_CONCAT(b.Id) AS PermIds,
                                            GROUP_CONCAT(b.ObjectId) AS PermObjectIds,
                                            GROUP_CONCAT(b.State) AS PermStates
                                        FROM
                                            {TableName} AS a
                                        LEFT JOIN
                                            rbac_permission AS b ON
                                            a.Id = b.RoleId
                                        WHERE
                                            {WhereClause}
                                        GROUP BY
                                            a.Id";
        $this->sqlQueries['getAll'] = "SELECT
                                            a.Id,
                                            a.Name,
                                            GROUP_CONCAT(b.Id) AS PermIds,
                                            GROUP_CONCAT(b.ObjectId) AS PermObjectIds,
                                            GROUP_CONCAT(b.State) AS PermStates
                                        FROM
                                            {TableName} AS a
                                        LEFT JOIN
                                            rbac_permission AS b ON
                                            a.Id = b.RoleId
                                        GROUP BY
                                            a.Id";
    }
}