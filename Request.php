<?php

namespace codewild\csubmboer\core;

class Request {
    private array $routeParams = [];

    public function getMethod(){
        return strtolower($_SERVER['REQUEST_METHOD']);
    }

    public function getUrl(){
        $path = $_SERVER['REQUEST_URI'];
        $position = strpos($path, '?');
        if($position !== false){
            return substr($path, 0, $position);
        }
        return $path;
    }
    public function parseQuery(){
        if (array_key_exists('QUERY_STRING', $_SERVER)) {
            $path = $_SERVER['QUERY_STRING'];
            parse_str($path, $array);
            return $array;
        }
        return [];
    }

    public function isGet(): bool{
        return $this->getMethod() === 'get';
    }
    public function isPost(): bool{
        return $this->getMethod() === 'post';
    }

    public function getBody(){
        $body = [];
        if($this->getMethod() === 'get'){
            foreach($_GET as $key => $value){
                $body[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }
        if($this->getMethod() === 'post'){
            foreach($_POST as $key => $value){
                $body[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }

        return $body;
    }

    public function setRouteParams($params): Request
    {
        $this->routeParams = $params;
        return $this;
    }

    public function getRouteParams(): array {
        return $this->routeParams;
    }

    public static function createUrl(string $string, array $attributes){
        $string = preg_replace_callback('/{(\w*)}/', function($matches) use ($attributes) {
            if (array_key_exists($matches[1], $attributes)){
                return $attributes[$matches[1]];
            }
        }, $string);

        return $string;
    }




}
