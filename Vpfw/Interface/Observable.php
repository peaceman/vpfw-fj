<?php
interface Vpfw_Interface_Observable {
    public function attachObserver(Vpfw_Interface_Observer $observer);
    public function detachObserver(Vpfw_Interface_Observer $observer);
    public function notifyObserver();
}