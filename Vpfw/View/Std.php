<?php
class Vpfw_View_Std implements Vpfw_View_Interface {
    private $vars = array();
    private $template;
    public $errors;

    public function __construct($pathToTemplate) {
        $this->template = $pathToTemplate;
    }

    public function setVar($name, $value) {
        $this->vars[$name] = $value;
    }

    public function render() {
        ob_start();
        include $this->template;
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }

    public function __call($name, $arguments) {
        if (true == isset($this->vars[$name])) {
            echo $this->vars[$name];
        } else {
            echo '<!-- requested content ' . $name . ' not found -->';
        }
    }

    public function __get($name) {
        if (true == isset($this->vars[$name])) {
            return $this->vars[$name];
        }
    }

    public function __set($name, $value) {
        $this->setVar($name, $value);
    }


}
