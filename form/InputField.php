<?php 

namespace codewild\phpmvc\form;

use codewild\phpmvc\Model;

class InputField extends BaseField {
    public const TYPE_TEXT = 'text';
    public const TYPE_PASSWORD = 'password';
    public const TYPE_NUMBER = 'number';
    public const TYPE_FILE = 'file';

    public string $type;
    public bool $isDisabled = false;
    public bool $isReadOnly = false;

    public function __construct(Model $model, string $attribute, ?array $classes = null){
        $this->type = self::TYPE_TEXT;
        parent::__construct($model, $attribute, $classes);
    }

    public function setType(string $type){
        $this->type = $type;
        return $this;
    }

    public function disabledField(){
        $this->isDisabled = true;
        return $this;
    }

    public function readonly(){
        $this->isReadOnly = true;
        return $this;
    }

    public function renderInput(): string{
        // REQUIRES ATTENTION
        // If type="number", the value will be rendered as a string, causing an error if the input it is submitted empty
        return sprintf('<input type="%s" name="%s" value="%s" class="form-control%s"%s%s>',
            $this->type,
            $this->attribute, 
            $this->type === self::TYPE_PASSWORD ? '' : $this->model->{$this->attribute},
            $this->model->hasError($this->attribute) ? ' is-invalid' : '',
            $this->isDisabled ? ' disabled' : '',
            $this->isReadOnly ? ' readonly' : '',
        );
    }
}

?>
