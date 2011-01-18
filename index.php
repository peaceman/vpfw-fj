<?php
require_once 'functions.php';
try {
    $stdCfg = new Vpfw_Config_Standard('std.cfg');
    Vpfw_Factory::setConfig($stdCfg);
    $stdCfg->setLogObject(Vpfw_Factory::getLog());
    
    $request = new Vpfw_Request_Http();
    $response = new Vpfw_Response_Http();
    $router = new Vpfw_Router_Standard();
    $fC = new App_Controller_Front_Explicit($router);
    $fC->handleRequest($request, $response);
} catch (Vpfw_Exception_Critical $e) {
    echo '<pre>';
    echo $e->getMessage();
    echo '</pre>';
} catch (Vpfw_Exception_Logical $e) {
    echo '<h3>Na, Code wieder nur hingebatscht und nicht den Koran befragt?!</h3>' . PHP_EOL;
    echo '<pre>';
    echo $e->getTraceAsString() . PHP_EOL;
    echo $e->getMessage() . PHP_EOL;
    echo $e->getFile() . ' @ ' . $e->getLine() . PHP_EOL;
    echo '<pre>';
} catch (Vpfw_Exception_Die $e) {
    
}

try {
    Vpfw_Cleaner::work();
} catch (Vpfw_Exception_Critical $e) {
    echo $e->getMessage();
}