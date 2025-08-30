<?php

namespace core\utils;

class ControllerHandler {
    private $method = "";
    private $data   = array();
    
    public function __construct() {
        $this->method   = strtolower( $_SERVER['REQUEST_METHOD']);
        switch ( $this->method ) {
            case "get":
                $this->data = $_GET;
                $this->get();
                break;
            case "post":
                $this->data = $_POST;
                $this->post();
                break;
            case "put":
                parse_str(file_get_contents('php://input'), $this->data);
                $this->put();
                break;
            case "delete":
                parse_str(file_get_contents('php://input'), $this->data);
                $this->delete();
                break;
            case "file":
                $this->data = $_FILES;
                $this->file();
                break;
            default:
                $this->data = array();
                break;
        }
    }
    
    public function getMethod(){
        return $this->method;
    }
    
    public function setMethod($method){
        $this->method = $method;
    }
    
    public function getParameters(){
        unset( $this->data['uri']);
        return $this->data??array();
    }
    
    public function getParameter( $key ){
        return $this->data[$key]??"";
    }
    
    public function setParameter( $key, $value ){
        return $this->data[$key] = $value;
    }
    
    public function getData(){
        return $this->data;
    }
    
    public function setData($data){
        $this->data = $data;
    }
  
}

?>
