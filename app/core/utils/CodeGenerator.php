<?php
namespace app\core\utils;

class CodeGenerator {
    
    public function run($lenght) {
        
        $simbols = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789@$&_";
        $code = "";
        for ($i = 0; $i < $lenght; $i++) {
            $idx = rand(0, strlen($simbols)-1);
            $code .= $simbols[$idx];
        }
        return ($code);
    }
}



?>