<?php

namespace codewild\csubmboer\core\exception;

class DbException extends \Exception {
    protected $code = 400;
    protected $message = 'Database error';
}
