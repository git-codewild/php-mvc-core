<?php

namespace codewild\phpmvc;

class View {
    public string $title = '';
    public array $scripts = [];

    public function renderView($view, $params = []){
        $viewContent = $this->renderOnlyView($view, $params);
        $layoutContent = $this->layoutContent();
        return str_replace('{{content}}', $viewContent, $layoutContent);
    }
    public function renderContent($viewContent){
        $layoutContent = $this->layoutContent();
        return str_replace('{{content}}', $viewContent, $layoutContent);
    }

    protected function layoutContent(){
        if (Application::$app->controller){
            $layout = Application::$app->controller->layout;
        } else {
            $layout = Application::$app->layout;
        }
        ob_start();
        include_once Application::$VIEWS_DIR."/_layouts/$layout.php";
        return ob_get_clean();
    }

    protected function renderOnlyView($view, $params){
        foreach($params as $key=>$value){
            $$key = $value;
        }
        ob_start();
        include_once Application::$VIEWS_DIR."/$view.php";
        return ob_get_clean();
    }

}
