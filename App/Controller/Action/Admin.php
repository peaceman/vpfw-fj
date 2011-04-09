<?php
class App_Controller_Action_Admin extends Vpfw_Controller_Action_Abstract {
    public function __construct($environment = null) {
        parent::__construct($environment);
        $this->needDataMapper('RuleViolation');
        $this->needDataMapper('User');
        $this->needDataMapper('Deletion');
        $this->needDataMapper('Picture');
        $this->needDataMapper('PictureComment');
        $this->needDataMapper('RbacPermission');
        $this->needDataMapper('RbacObject');
        $this->needDataMapper('RbacRole');
        $this->registerForPreExecute(function($obj) {
            $hasAccess = $obj->getSession()->getRbacUser()->hasAccessTo('admin');
            if ($hasAccess == false) {
                $view = new Vpfw_View_Std('App/Html/NoAccess.html');
                $view->area = 'admin';
                $obj->setView($view);
                $obj->interruptExecution();
            }
        });
    }

    public function indexAction() {
        $linksArray = array(
            array(
                'url' => Vpfw_Router_Http::url('admin', 'ruleviolations'),
                'name' => 'Regelverstöße',
            ),
            array(
                'url' => Vpfw_Router_Http::url('admin', 'users'),
                'name' => 'Benutzer',
            ),
            array(
                'url' => Vpfw_Router_Http::url('admin', 'pictures'),
                'name' => 'Bilder',
            ),
            array(
                'url' => Vpfw_Router_Http::url('admin', 'rbacObjects'),
                'name' => 'RbacObjects',
            ),
            array(
                'url' => Vpfw_Router_Http::url('admin', 'rbacRoles'),
                'name' => 'RbacRoles',
            ),
        );
        $this->view->links = $linksArray;
    }

    public function pictureAction() {
        $picture = $this->getPictureFromRequestData();
        $this->view->picture = $picture;
        $this->view->user = $picture->getSession()->getUser();
    }

    private function getPictureFromRequestData() {
        $pictureId = (int)$this->request->getParameter('pictureId');
        $picture = null;
        try {
            $picture = $this->pictureMapper->getEntryById($pictureId);
        } catch (Vpfw_Exception_OutOfRange $e) {
            $this->setContent('Ein Bild mit der Id ' . HE($pictureId, false) . ' existiert nicht');
            $this->interruptExecution();
        }
        return $picture;
    }

    public function ruleviolationsAction() {
        $this->view->unhandledRuleViolations = $this->ruleviolationMapper->getUnhandledRuleViolations();
        $this->view->handledRuleViolations = $this->ruleviolationMapper->getHandledRuleViolations();
    }

    public function ruleViolationAction() {
        $ruleViolation = $this->getRuleViolationFromRequestData();
        $handledState = $this->getHandledStateFromRequestData();
        $ruleViolation->setHandled($handledState);
        if ($handledState == 0)
            $this->view->undoUrl = Vpfw_Router_Http::url('admin', 'ruleViolation', array('ruleViolationId' => $ruleViolation->getId(), 'handledState' => 1));
        else
            $this->view->undoUrl = Vpfw_Router_Http::url('admin', 'ruleViolation', array('ruleViolationId' => $ruleViolation->getId(), 'handledState' => 0));
        $this->view->handledState = $handledState;
    }

    private function getRuleViolationFromRequestData() {
        $ruleViolationId = (int)$this->request->getParameter('ruleViolationId');
        $ruleViolation = null;
        try {
            $ruleViolation = $this->ruleviolationMapper->getEntryById($ruleViolationId);
        } catch (Vpfw_Exception_OutOfRange $e) {
            $this->view->setContent('Ein Regelverstoß mit der Id ' . HE($ruleViolationId, false) . ' existiert nicht');
            $this->interruptExecution();
        }
        return $ruleViolation;
    }

    private function getHandledStateFromRequestData() {
        $handledState = $this->request->getParameter('handledState');
        if (is_null($handledState) || ($handledState != 0 && $handledState != 1)) {
        $this->response->addHeader('Location', Vpfw_Router_Http::url('admin', 'ruleviolations'));
            $this->interruptExecution();
        }
        return $handledState;
    }

    public function usersAction() {
        //TODO add paging
        $this->view->users = $this->userMapper->getAllEntries();
    }

    public function userAction() {
        $user = $this->getUserFromRequestData();
        $pictures = $user->getPictures();
        $comments = $user->getPictureComments();
        $this->view->user = $user;
        $this->view->pictures = $pictures;
        $this->view->comments = $comments;
    }

    public function userDelAction() {
        $user = $this->getUserFromRequestData();

        $reasonField = new Vpfw_Form_Field('reason', false);
        $whitespaceFilter = new Vpfw_Form_Filter_TrimSpaces();
        $notEmptyValidator = new Vpfw_Form_Validator_NotEmpty();
        $reasonField->addValidator($notEmptyValidator)
                ->addFilter($whitespaceFilter);

        $form = new Vpfw_Form($this->request, 'delUserProof', array($reasonField));
        $form->setAction(Vpfw_Router_Http::url('admin', 'userDel', array('userId' => $user->getId())))
                ->setMethod('post')
                ->handleRequest();

        $this->view->user = $user;
        $this->view->form = $form;

        if ($form->formWasSent() && $form->isAllValid()) {
            $validValues = $form->getValidValues();
            $parameters = array(
                'SessionId' => $this->session->getSession()->getId(),
                'Time' => time(),
            );
            if (array_key_exists('reason', $validValues)) {
                $parameters['reason'] = $validValues['reason'];
            }
            $deletion = $this->deletionMapper->createEntry($parameters, true);

            $user->setDeletion($deletion);
            $this->response->addHeader('Location', Vpfw_Router_Http::url('admin', 'users'));
        }
    }

    private function getUserFromRequestData() {
        $userId = (int)$this->request->getParameter('userId');
        $user = null;
        try {
            $user = $this->userMapper->getEntryById($userId);
        } catch (Vpfw_Exception_OutOfRange $e) {
            $this->view->setContent('Ein Benutzer mit der Id ' . HE($userId, false) . ' existiert nicht');
            $this->interruptExecution();
        }
        return $user;
    }

    private function getPictureCommentFromRequestData() {
        $pictureCommentId= (int)$this->request->getParameter('pictureCommentId');
        $pictureComment = null;
        try {
            $pictureComment = $this->pictureCommentMapper->getEntryById($pictureCommentId);
        } catch (Vpfw_Exception_OutOfRange $e) {
            $this->view->setContent('Ein Bildkommentar mit der Id ' . HE($pictureCommentId, false) . ' existiert nicht');
            $this->interruptExecution();
        }
        return $pictureComment;
    }

    public function picturesAction() {
        //TODO add paging
        $this->view->pictures = $this->pictureMapper->getAllEntries();
    }

    public function pictureDelAction() {
        $picture = $this->getPictureFromRequestData();

        $reasonField = new Vpfw_Form_Field('reason', false);
        $reasonField->addValidator(new Vpfw_Form_Validator_NotEmpty())
                ->addFilter(new Vpfw_Form_Filter_TrimSpaces());

        $form = new Vpfw_Form($this->request, 'deletePicture', array($reasonField));
        $form->setAction(Vpfw_Router_Http::url('admin', 'pictureDel', array('pictureId' => $picture->getId())))
                ->setMethod('post')
                ->handleRequest();

        $this->view->form = $form;
        $this->view->picture = $picture;

        if ($form->formWasSent() && $form->isAllValid()) {
            $validValues = $form->getValidValues();
            $parameters = array(
                'SessionId' => $this->session->getSession()->getId(),
                'Time' => time(),
            );
            if (array_key_exists('reason', $validValues)) {
                $parameters['reason'] = $validValues['reason'];
            }
            $deletion = $this->deletionMapper->createEntry($parameters, true);

            $picture->setDeletion($deletion);
            $this->response->addHeader('Location', Vpfw_Router_Http::url('admin', 'pictures'));
        }
    }

    public function pictureCommentDelAction() {
        $pictureComment = $this->getPictureCommentFromRequestData();

        $reasonField = new Vpfw_Form_Field('reason', false);
        $reasonField->addValidator(new Vpfw_Form_Validator_NotEmpty())
                ->addFilter(new Vpfw_Form_Filter_TrimSpaces());

        $form = new Vpfw_Form($this->request, 'deletePictureComment', array($reasonField));
        $form->setAction(Vpfw_Router_Http::url('admin', 'pictureCommentDel', array('pictureCommentId' => $pictureComment->getId())))
                ->setMethod('post')
                ->handleRequest();

        $this->view->form = $form;
        $this->view->pictureComment = $pictureComment;

        if ($form->formWasSent() && $form->isAllValid()) {
            $validValues = $form->getValidValues();
            $parameters = array(
                'SessionId' => $this->session->getSession()->getId(),
                'Time' => time(),
            );
            if (array_key_exists('reason', $validValues)) {
                $parameters['reason'] = $validValues['reason'];
            }
            $deletion = $this->deletionMapper->createEntry($parameters, true);

            $pictureComment->setDeletion($deletion);
            $this->response->addHeader('Location', Vpfw_Router_Http::url('admin', 'picture', array('pictureId' => $pictureComment->getPictureId())));
        }
    }

    public function rbacPermissionChangeRightAction() {
        $rbacPermission = $this->getRbacPermissionFromRequestData();
        $rbacPermission->setState(!$rbacPermission->getState());
    }

    public function rbacPermissionsAction() {
        //TODO add paging
        $rbacPermissions = $this->rbacpermissionMapper->getAllEntries();
        $this->view->rbacPermissions = $rbacPermissions;
    }

    public function rbacPermissionAction() {
        $rbacPermission = $this->getRbacPermissionFromRequestData();
        $this->view->rbacPermission = $rbacPermission;
        $this->view->rbacRole = $rbacPermission->getRole();
        $this->view->rbacObject = $rbacPermission->getObject();
    }

    public function rbacPermissionNewAction() {
        $rbacRole = $this->getRbacRoleFromRequestData(false);
        $rbacObject = $this->getRbacObjectFromRequestData(false);

        $rbacRoles = $this->rbacroleMapper->getAllEntries();
        $rbacObjects = $this->rbacobjectMapper->getAllEntries();

        $roleIds = $this->getIdsFromDataObjects($rbacRoles);
        $roleField = new Vpfw_Form_Field_MultipleChoice('roleId', $roleIds);
        $roleField->addValidator(new Vpfw_Form_Validator_InArray($roleIds));

        $objectIds = $this->getIdsFromDataObjects($rbacObjects);
        $objectField = new Vpfw_Form_Field_MultipleChoice('objectId', $objectIds);
        $objectField->addValidator(new Vpfw_Form_Validator_InArray($objectIds));

        $rightField = new Vpfw_Form_Field_MultipleChoice('right', array('permit', 'deny'));
        $rightField->addValidator(new Vpfw_Form_Validator_InArray(array('permit', 'deny')));

        $form = new Vpfw_Form($this->request, 'rbacPermissionNew', array($roleField, $objectField, $rightField));
        $form->setAction(Vpfw_Router_Http::url('admin', 'rbacPermissionNew'))
                ->setMethod('post')
                ->handleRequest();

        if (!is_null($rbacRole)) {
            $roleField->setValue($rbacRole->getId());
        }
        if (!is_null($rbacObject)) {
            $objectField->setValue($rbacObject->getId());
        }

        if ($form->formWasSent() && $form->isAllValid()) {
            $validValues = $form->getValidValues();
            $rbacPermission = $this->rbacpermissionMapper->createEntry();
            $validValues['state'] = $validValues['right'] == 'permit' ? true : false;
            unset($validValues['right']);
            $validationResult = $rbacPermission->publicate($validValues);
            if (true === $validationResult) {
                if (!is_null($rbacRole)) {
                    $nextLocation = Vpfw_Router_Http::url('admin', 'rbacRole', array('rbacRoleId' => $rbacRole->getId()));
                } elseif (!is_null($rbacObject)) {
                    $nextLocation = Vpfw_Router_Http::url('admin', 'rbacObject', array('rbacObjectId' => $rbacObject->getId()));
                } else {
                    $nextLocation = Vpfw_Router_Http::url('admin');
                }
                $this->response->addHeader('Location', $nextLocation);
            } else {
                foreach ($validationResult as $error) {
                    $form->addErrorForForm($error->getMessage());
                }
                $rbacPermission->notifyObserver();
            }
        }
        $this->view->form = $form;
        $this->view->rbacRoles = $rbacRoles;
        $this->view->rbacObjects = $rbacObjects;
    }

    private function getIdsFromDataObjects(array $dataObjects) {
        $ids = array();
        foreach ($dataObjects as $dataObject) {
            $ids[] = $dataObject->getId();
        }
        return $ids;
    }

    /**
     * @return Vpfw_DataObject_RbacPermission
     */
    private function getRbacPermissionFromRequestData() {
        $rbacPermissionId = (int)$this->request->getParameter('rbacPermissionId');
        $rbacPermission = null;
        try {
            $rbacPermission = $this->rbacpermissionMapper->getEntryById($rbacPermissionId);
        } catch (Vpfw_Exception_OutOfRange $e) {
            $this->view->setContent('Eine RbacPermission mit der Id ' . HE($rbacPermissionId, false) . ' existiert nicht');
            $this->interruptExecution();
        }
        return $rbacPermission;
    }

    public function rbacObjectsAction() {
        //TODO add paging
        $rbacObjects = $this->rbacobjectMapper->getAllEntries();
        $this->view->rbacObjects = $rbacObjects;
    }

    public function rbacObjectAction() {
        $rbacObject = $this->getRbacObjectFromRequestData();
        $this->view->rbacObject = $rbacObject;
        $this->view->rbacPermissions = $rbacObject->getPermissions();
    }

    public function rbacObjectNewAction() {
        $nameField = new Vpfw_Form_Field('name');
        $rightField = new Vpfw_Form_Field_MultipleChoice('default', array('permit', 'deny'));

        $nameField->addValidator(new Vpfw_Form_Validator_NotEmpty())
                ->addValidator(new Vpfw_Form_Validator_Length(2, 32))
                ->addFilter(new Vpfw_Form_Filter_TrimSpaces());

        $rightField->addValidator(new Vpfw_Form_Validator_InArray(array('permit', 'deny')));

        $form = new Vpfw_Form($this->request, 'rbacObjectNew', array($nameField, $rightField));
        $form->setAction(Vpfw_Router_Http::url('admin', 'rbacObjectNew'))
                ->setMethod('post')
                ->handleRequest();

        if ($form->formWasSent() && $form->isAllValid()) {
            $validValues = $form->getValidValues();
            $validValues['default'] = $validValues['default'] == 'permit' ? true : false;
            $rbacObject = $this->rbacobjectMapper->createEntry();
            $validationResult = $rbacObject->publicate($validValues);
            if (true === $validationResult) {
                $this->response->addHeader('Location', Vpfw_Router_Http::url('admin', 'rbacObjects'));
            } else {
                foreach ($validationResult as $error) {
                    $form->addErrorForForm($error->getMessage());
                }
                $rbacObject->notifyObserver();
            }
        }
        $this->view->form = $form;
    }

    /**
     * @param bool $interruptExecution
     * @return Vpfw_DataObject_RbacObject
     */
    private function getRbacObjectFromRequestData($interruptExecution = true) {
        $rbacObjectId = (int)$this->request->getParameter('rbacObjectId');
        $rbacObject = null;
        try {
            $rbacObject = $this->rbacobjectMapper->getEntryById($rbacObjectId);
        } catch (Vpfw_Exception_OutOfRange $e) {
            if ($interruptExecution) {
                $this->setContent('Ein RbacObject mit der Id ' . HE($rbacObjectId, false) . ' existiert nicht');
                $this->interruptExecution();
            }
        }
        return $rbacObject;
    }

    public function rbacRolesAction() {
        //TODO add paging
        $rbacRoles = $this->rbacroleMapper->getAllEntries();
        $this->view->rbacRoles = $rbacRoles;
    }

    public function rbacRoleAction() {
        $rbacRole = $this->getRbacRoleFromRequestData();
        $this->view->rbacRole = $rbacRole;
        $this->view->rbacPermissions = $rbacRole->getPermissions();
    }

    public function rbacRoleNewAction() {
        $nameField = new Vpfw_Form_Field('name');
        $nameField->addValidator(new Vpfw_Form_Validator_Length(2, 32))
                ->addValidator(new Vpfw_Form_Validator_NotEmpty())
                ->addFilter(new Vpfw_Form_Filter_TrimSpaces());

        $form = new Vpfw_Form($this->request, 'rbacRoleNew', array($nameField));
        $form->setAction(Vpfw_Router_Http::url('admin', 'rbacRoleNew'))
                ->setMethod('post')
                ->handleRequest();

        if ($form->formWasSent() && $form->isAllValid()) {
            $validValues = $form->getValidValues();
            $rbacRole = $this->rbacroleMapper->createEntry();
            $validationResult = $rbacRole->publicate($validValues);
            if (true === $validationResult) {
                $this->response->addHeader('Location', Vpfw_Router_Http::url('admin', 'rbacRoles'));
            } else {
                foreach ($validationResult as $error) {
                    $form->addErrorForForm($error->getMessage());
                }
                $rbacRole->notifyObserver();
            }
        }
        $this->view->form = $form;
    }

    /**
     * @param bool $interruptExecution
     * @return Vpfw_DataObject_RbacRole
     */
    private function getRbacRoleFromRequestData($interruptExecution = true) {
        $rbacRoleId = (int)$this->request->getParameter('rbacRoleId');
        $rbacRole = null;
        try {
            $rbacRole = $this->rbacroleMapper->getEntryById($rbacRoleId);
        } catch (Vpfw_Exception_OutOfRange $e) {
            if ($interruptExecution) {
                $this->view->setContent('Eine RbacRole mit der Id ' . HE($rbacRoleId, false) . ' existiert nicht');
                $this->interruptExecution();
            }
        }
        return $rbacRole;
    }
}
