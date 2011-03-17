<?php
class App_Controller_Action_Navigation extends Vpfw_Controller_Action_Abstract {
    public function indexAction() {
        $linksArray = array(
            array(
                'url' => Vpfw_Router_Http::url('show'),
                'name' => 'Rate',
            ),
            array(
                'url' => Vpfw_Router_Http::url('show', 'top10', array('gender' => 'male')),
                'name' => 'Top 10 male',
            ),
            array(
                'url' => Vpfw_Router_Http::url('show', 'top10', array('gender' => 'female')),
                'name' => 'Top 10 female',
            ),
            array(
                'url' => Vpfw_Router_Http::url('picture', 'upload'),
                'name' => 'Upload Picture',
            ),
        );
        if (false == $this->session->isLoggedIn()) {
            $linksArray[] = array(
                'url' => Vpfw_Router_Http::url('user', 'register'),
                'name' => 'Register',
            );
            $linksArray[] = array(
                'url' => Vpfw_Router_Http::url('user', 'login'),
                'name' => 'Login',
            );
        } else {
            $linksArray[] = array(
                'url' => Vpfw_Router_Http::url('user', 'uploadedPictures'),
                'name' => 'Show Pictures',
            );
            $linksArray[] = array(
                'url' => Vpfw_Router_Http::url('user', 'logout'),
                'name' => 'Logout',
            );
            $linksArray[] = array(
                'url' => Vpfw_Router_Http::url('admin'),
                'name' => 'Administration',
            );
        }
        $this->view->links = $linksArray;
    }
}