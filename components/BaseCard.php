<?php

namespace codewild\phpmvc\components;

use codewild\phpmvc\Model;

abstract class BaseCard
{
    abstract public function renderHeader(): string;
    abstract public function renderBody(): string;
    abstract public function renderFooter(): string;

    public Model $model;

    public array $classes = [];

    public function __construct(Model $model, array $classes = [])
    {
        $this->model = $model;
        $this->classes = $classes;
    }

    public function __toString(){
        return sprintf("
        <div class='card%s'>
            <div class='card-header'>
                %s
            </div>
            <div class='card-body%s'>
                %s
            </div>
            <div class='card-footer'>
                %s
            </div>
        </div>",
        array_key_exists('card', $this->classes) ? ' '.$this->classes['card'] : '',
        $this->renderHeader(),
        array_key_exists('body', $this->classes) ? ' '.$this->classes['body'] : '',
        $this->renderBody(),
        $this->renderFooter(),
        );
    }

}
