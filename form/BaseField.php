<?php

namespace codewild\phpmvc\form;

use codewild\phpmvc\Model;

abstract class BaseField {
    abstract public function renderInput(): string;

    public Model $model;
    public string $attribute;
    public string $label;
    public array $classes = [];

    public bool $isHidden = false;

    public function __construct(Model $model, string $attribute, ?array $classes = null){
        $this->model = $model;
        $this->attribute = $attribute;
        $this->label = $this->model->getLabel($this->attribute);
        $this->classes = $classes ?? [];
    }

    public function hiddenField(){
        $this->isHidden = true;
        return $this;
    }

    public function __toString(){
        return sprintf('
            <div class="form-group mb-2%s"%s>
                <label class="form-label">%s</label>
                %s
                <div class="invalid-feedback">%s</div>
            </div>
        ',  array_key_exists('div', $this->classes) ? ' '.$this->classes['div'] : '',
            $this->isHidden ? ' hidden' : '',
            $this->model->getLabel($this->attribute),
            $this->renderInput(),
            $this->model->getFirstError($this->attribute)
        );
    }
}
