<?php

namespace codewild\phpmvc;

use codewild\phpmvc\db\Database;
use codewild\csubmboer\models\ContactForm;

class Application {
    public static string $ROOT_DIR;

    public string $layout = 'main';
    public string $userClass;
    public Router $router;
    public Request $request;
    public Response $response;
    public Session $session;
    public Database $db;
    public ?UserModel $user;
    public View $view;
    public static Application $app;
    public ?Controller $controller = null;
    public function __construct($rootPath, array $config){

        self::$ROOT_DIR = $rootPath;
        self::$app = $this;
        $this->request = new Request();
        $this->response = new Response();
        $this->session = new Session();
        $this->router = new Router($this->request, $this->response);
        $this->view = new View();
        $this->db = new Database($config['db']);

        $this->userClass = $config['userClass'] ?? UserModel::class;
        $primaryValue = $this->session->get('user');
        if ($primaryValue) {
            $primaryKey = $this->userClass::primaryKey();
            $this->user = $this->userClass::findOne([$primaryKey => $primaryValue]);
        } else {
            $this->user = null;
        };        
    }

    public function run() {
        try { 
            echo $this->router->resolve();
        } catch(\Exception $e) {
            // if this is a HTTP error
            if (is_int($e->getCode())) {
                $this->response->setStatusCode($e->getCode());
            }
            echo $this->view->renderView('_error', [
                'model' => $e, 'contact' => new ContactForm()
            ]);
        }
    }

    public function getController(): Controller {
        return $this->controller;
    }
    public function setController(Controller $controller): void{
        $this->controller = $controller;
    }

    public function login(UserModel $user){
        $this->user = $user;
        $primaryKey = $user->primaryKey();
        $primaryValue = $user->{$primaryKey};
        $this->session->set('user', $primaryValue);
        return true;
    }

    public function logout(){
        $this->user = null;
        $this->session->remove('user');
    }

    public static function isGuest(){
        return !self::$app->user;
    }
}

?>
