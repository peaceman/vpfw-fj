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
        $this->sqlQueries['getByUserId'] = "SELECT
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
                                            INNER JOIN
                                                rbac_user2role AS c ON
                                                a.Id = c.RoleId
                                            WHERE
                                                c.UserId = ?
                                            GROUP BY
                                                a.Id";
    }

    public function getEntriesByUserId($userId) {
        $stmt = $this->db->prepare($this->sqlQueries['getByUserId']);
        $stmt->bind_param('i', $userId);
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
