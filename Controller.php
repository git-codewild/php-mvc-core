<?php

namespace codewild\csubmboer\core;

use codewild\csubmboer\core\exception\NotFoundException;
use codewild\csubmboer\core\middleware\BaseMiddleware;

class Controller {
    public string $layout = 'main';
    public string $action = '';
    /**
     * @var BaseMiddleware[]
     */
    public array $middlewares = [];

    public function setLayout($layout) {
         $this->layout = $layout;
    }

    public function render($view, $params = []){
        if(array_key_exists('model', $params) && $params['model'] === false){
            throw new NotFoundException();
        }

        return Application::$app->view->renderView($view, $params);
    }

    protected function registerMiddleware(BaseMiddleware $middleware){
        $this->middlewares[] = $middleware;
    }
    public function getMiddlewares(): array {
        return $this->middlewares;
    }
}

?>
