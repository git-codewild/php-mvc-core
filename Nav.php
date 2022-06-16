<?php

namespace codewild\csubmboer\core;

use codewild\csubmboer\core\components\ListGroup;

abstract class Nav
{
    abstract public function pages(): array;
    abstract public function routes(): array;
    abstract public function titles(): array;
    abstract public function needle(): string;

    public function __construct(?string $classes = null){
        new ListGroup($this, $classes);
    }

    public function active(){
        $pages = static::class::pages();
        $needle = static::class::needle();
        return array_search($needle, $pages);
    }
}
