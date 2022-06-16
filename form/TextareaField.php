<?php

namespace codewild\csubmboer\core\form;

use codewild\csubmboer\core\Model;

class TextareaField extends InputField {
    public ?string $id = null;

    public function renderInput(): string{
        return sprintf('<textarea%sname="%s" class="form-control%s%s"%s%s>%s</textarea>',
            !is_null($this->id) ? " id='$this->id' " : ' ',
            $this->attribute,
            $this->model->hasError($this->attribute) ? ' is-invalid' : '',
            array_key_exists('textarea', $this->classes) ? ' '.$this->classes['textarea'] : '',
            $this->isDisabled ? ' disabled' : '',
            $this->isReadOnly ? ' readonly' : '',
            $this->model->{$this->attribute}
        );
    }

    public function id(string $id){
        $this->id = $id;
        return $this;
    }
}
