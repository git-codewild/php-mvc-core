<?php

namespace codewild\phpmvc\middleware;

abstract class BaseMiddleware {
    abstract public function execute();
}
