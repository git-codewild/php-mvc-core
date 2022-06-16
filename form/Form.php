<?php

namespace codewild\csubmboer\core\form;

use codewild\csubmboer\core\db\DbModel;
use codewild\csubmboer\core\Model;

class Form {
    public const TYPE_DEFAULT = 'default';
    public const TYPE_MULTIPART = 'multipart';

    public ?string $id;
    public string $type;

    public function __construct(?string $type = self::TYPE_DEFAULT, ?string $id = null){
        $this->type = $type;
        $this->id = $id;
    }

    public function begin(?string $classes = null, ?string $action = null){
        return sprintf("<form action='$action' method='POST' class='%s'%s%s>",
            $classes,
            ($this->type === self::TYPE_MULTIPART) ? " enctype='multipart/form-data'" : '',
            (!is_null($this->id)) ? " id='$this->id'" : ''
        );
    }

    public function end(?string $value = 'Submit', ?string $classes = 'btn-primary'){
        return "<input type='submit' class='btn $classes' name='$this->id' value='$value' />
            </form>";
    }
    public function field(Model $model, $attribute){
        return new InputField($model, $attribute);
    }
    public function inputGroup(Model $model, $attribute, array $classes = []){
        return new InputGroup($model, $attribute, $classes, $this->id);
    }
    public function radioInputGroup(Model $model, $attribute, array $classes = []){
        return new RadioInputGroup($model, $attribute, $classes, $this->id);
    }
    public function textarea(Model $model, $attribute, array $classes = []){
        return new TextareaField($model, $attribute, $classes);
    }
    public function selectField(Model|array $model, $attribute, array $classes = []){
        return new SelectField($model, $attribute, $classes);
    }
}


?>
