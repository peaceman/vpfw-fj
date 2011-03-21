<?php
interface Vpfw_Rbac_UserInterface {
    /**
     * @return Vpfw_DataObject_RbacRole[]
     */
    public function getRbacRoles();
}