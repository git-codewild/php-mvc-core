<?php

namespace codewild\csubmboer\core;

class Response {

    public function setStatusCode(int $code){
        http_response_code($code);
    }
    public function redirect(?string $url = null) {
        if (is_null($url)){
            $url = htmlspecialchars($_SERVER['REQUEST_URI']);
        }
        header("Location: $url");
    }
}

?>
