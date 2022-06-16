<?php

namespace codewild\csubmboer\core\middleware;

use codewild\csubmboer\authorization\AuthHandler;
use codewild\csubmboer\core\Application;
use codewild\csubmboer\core\exception\ForbiddenException;
use codewild\csubmboer\models\UserRole;

class AuthMiddleware extends BaseMiddleware {
    public array $actions = [];

    public function __construct(array $actions = []){
        $this->actions = $actions;
    }

    public function execute(){
        if (empty($this->actions)){
            if (Application::isGuest() || !Application::$app->user->isInRole(UserRole::ROLE_ADMIN)) {
                throw new ForbiddenException();
            }
        } else {
            foreach($this->actions as $key => $item){
                // $key = controller->action
                if (Application::$app->controller->action === $key) {

                    if (is_array($item)) {
                        // $item = [action => Model()]
                        foreach ($item as $k => $v) {
                            if (!AuthHandler::authorize($v, $k)) {
                                throw new ForbiddenException();
                            }
                        }
                    } else {
                        // $key = controller->action = AuthHandler::ACTION
                        // $item = Model()
                        if (!AuthHandler::authorize($item, $key)) {
                            throw new ForbiddenException();
                        }
                    }
                }
            }
        }
    }
}
