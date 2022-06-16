<?php

namespace codewild\csubmboer\core\form;

use codewild\csubmboer\core\db\DbModel;

class SelectField extends BaseField
{
    public ?string $id = null;
    public array $options;

    public function __construct(DbModel|array $model, string $attribute, ?array $classes = null)
    {
        if (is_array($model)){
            $this->options = $model;
            parent::__construct(current($model), $attribute, $classes);
        } else {
            $this->options = $model::findAll();
            parent::__construct($model, $attribute, $classes);
        }


    }

    public function renderInput(): string{
        return sprintf('<select%sname="%s" class="form-select%s%s" aria-label="">%s</select>',
            !is_null($this->id) ? " id='$this->id' " : ' ',
            $this->attribute,
            $this->model->hasError($this->attribute) ? ' is-invalid' : '',
            array_key_exists('textarea', $this->classes) ? ' '.$this->classes['textarea'] : '',
            $this->renderOptions()
        );
    }

    public function renderOptions(){
        $output = "<option selected>Select below...</option>";
        foreach ($this->options as $option){
            $output.= sprintf("<option value='%s'>%s</option>"
                ,$option->id, $option->title);
        }
        return $output;
    }

    public function id(string $id){
        $this->id = $id;
        return $this;
    }
}
