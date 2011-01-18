<?php
class Vpfw_Response_Http implements Vpfw_Response_Interface {
    private $status = '200 OK';
    private $headers = array();
    private $body = null;

    public function setStatus($status) {
        $this->status = $status;
    }

    public function addHeader($name, $value) {
        $this->headers[$name] = $value;
    }

    public function write($data) {
        $this->body .= $data;
    }

    public function getBody() {
        return $this->body;
    }

    public function setBody($body) {
        $this->body = $body;
    }

    public function flush() {
        header('HTTP/1.1 ' . $this->status);
        foreach($this->headers as $name => $value) {
            header($name . ': ' . $value);
        }
        echo $this->body;
        $this->headers = array();
        $this->data = null;
    }
}
