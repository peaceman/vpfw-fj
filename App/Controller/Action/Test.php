<?php
class App_Controller_Action_Test extends Vpfw_Controller_Action_Abstract {
    protected function indexAction() {
        $language = new Vpfw_Language(new Vpfw_Language_Storage_Database(Vpfw_Factory::getDataMapper('Language')));
        $language->setLanguageToUse('deu');
        $language->set('testtext', 'hallo');
        $this->view->lang = $language;
    }
}
