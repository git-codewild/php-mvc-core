<?php

namespace codewild\phpmvc;

use codewild\phpmvc\exception\NotFoundException;

class Router {
    public Request $request;
    public Response $response;
    protected array $routeMap = [];

    public function __construct(Request $request, Response $response){
        $this->request = $request;
        $this->response = $response;
    }
   
    public function get(string $url, $callback){
        $this->routeMap['get'][$url] = $callback;
    }
    public function post(string $url, $callback){
        $this->routeMap['post'][$url] = $callback;
    }

    public function getRouteMap($method): array {
        return $this->routeMap[$method] ?? [];
    }

    public function getCallback()
    {
        $method = $this->request->getMethod();
        $url = $this->request->getUrl();
        // Trim slashes
        $url = trim($url, '/');
        $routes = $this->routeMap[$method] ?? [];
        $routeParams = false;
        foreach ($routes as $route => $callback){
            $route = trim($route, '/');
            $routeNames = [];

            if (!$route){
                continue;
            }

            // Find all route names from route and save in $routeNames
            if(preg_match_all('/\{(\w+)(:[^}]+)?}/', $route, $matches)){
                $routeNames = $matches[1];
            }

            // Convert route name into regex pattern
            $routeRegex = "@^" .preg_replace_callback('/\{\w+(:([^}]+}?))?}/', fn($m) => isset($m[2]) ? "({$m[2]})" : '(\w+)', $route)."$@";

            //Test and match current route against $routeRegex

            if (preg_match_all($routeRegex, $url, $valueMatches)){

                $values = [];
                for ($i = 1; $i < count($valueMatches); $i++){
                    $values[] = $valueMatches[$i][0];
                };
                $routeParams = array_combine($routeNames, $values);
                $this->request->setRouteParams($routeParams);
                return $callback;
            }
        }
        return false;
    }

    public function resolve() {
        $url = $this->request->getUrl();
        $method = $this->request->getMethod();
        $callback = $this->routeMap[$method][$url] ?? false;

        if(!$callback){
            $callback = $this->getCallback();
            if ($callback === false) {
                throw new NotFoundException();
            }
        }
        if(is_string($callback)){
            return Application::$app->view->renderView($callback);
        }
        if(is_array($callback)){
            /** @var Controller $contr */
            $contr = new $callback[0]();
            Application::$app->controller = $contr;
            $contr->action = $callback[1];
            $callback[0] = $contr;
            foreach($contr->getMiddlewares() as $middleware){
                $middleware->execute();
            }
        }
        return call_user_func($callback, $this->request, $this->response);
    }


}

?>
