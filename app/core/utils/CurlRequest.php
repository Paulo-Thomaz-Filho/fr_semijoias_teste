<?php

namespace core\utils;

class CurlRequest {
    private $url;
    private $method;
    private $data;
    
    public function __construct($url, $method, $data) {
        $this->url      = $url;
        $this->method   = strtoupper($method);
        $this->data     = http_build_query($data);
    }
    
    public function execute() {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        switch ($this->method){
            case 'GET':
                
                if ( is_array($this->url )){
                    curl_setopt($ch, CURLOPT_URL, $this->url . '?' . http_build_query($this->data));
                    echo $this->url . '?' . http_build_query($this->data);
                } else{
                    curl_setopt($ch, CURLOPT_URL, $this->url );
                    echo $this->url . '?' . http_build_query($this->data);
                }
                
                
            break;
            case 'POST':
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($this->data));
                break;
            case 'PUT':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($this->data));
                break;
            case 'DELETE':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($this->data));
                break;
        }
        
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Erro no cURL: ' . curl_error($ch);
        }
        curl_close($ch);
        echo $response;
    }
}


?>
