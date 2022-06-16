<?php

namespace codewild\csubmboer\core\exception;

class NotFoundException extends \Exception {
    protected $code = 404;
    protected $message = 'Page not found.';
}
