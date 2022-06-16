<?php

namespace codewild\phpmvc\form;

use codewild\phpmvc\Model;

class InputGroup extends InputField {

    public ?string $id;

    public function __construct(Model $model, string $attribute, ?array $classes = null, string $id = null)
    {
        $this->id = $id;
        parent::__construct($model, $attribute, $classes);
    }

    public function __toString(){
        return sprintf("               
            <div class='input-group'>
                <label class='visually-hidden' for='$this->attribute'>$this->label</label>
                <span class='input-group-text'>$this->label</span>
                %s
                <input class='btn btn-outline-secondary' type='submit' role='submit' name='$this->id' value='%s' />
                <div class='invalid-feedback'>%s</div>
            </div>
        ", $this->renderInput(),
            'Submit',
            $this->model->getFirstError($this->attribute)
        );
    }

    public function renderInput(): string {
        $inputField = new InputField($this->model, $this->attribute);
        $inputField->setType($this->type);
        return $inputField->renderInput();
//
//
//        // TODO:: If type="number", the value will be rendered as a string, causing an error if the input it is submitted empty
//        return sprintf('<input type="text" name="%s" value="%s" class="form-control%s">',
//            $this->attribute,
//            $this->model->{$this->attribute},
//            $this->model->hasError($this->attribute) ? ' is-invalid' : '',
//        );
    }
}
