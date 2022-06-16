<?php

namespace codewild\phpmvc;

abstract class Model {
    public const RULE_REQUIRED = 'required';
    public const RULE_EMAIL = 'email';
    public const RULE_MIN = 'min';
    public const RULE_MAX = 'max';
    public const RULE_MATCH = 'match';
    public const RULE_REGEX = 'regex';
    public const RULE_REGEX_UPPER = 'regex_upper';
    public const RULE_REGEX_LOWER = 'regex_lower';
    public const RULE_REGEX_NUMBER = 'regex_number';
    public const RULE_REGEX_SPECIAL = 'regex_special';
    public const RULE_CONTAINS = 'contains';

    public function get_class_name(){
        $str = static::class;
        if ($pos = strrpos($str, '\\')) return substr($str, $pos + 1);
        return $pos;
    }

    public function loadData($data) {
        foreach ($data as $key=>$value){
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
    }
    public static function attributes(): array{
        // Attributes to set in database
        return [];
    }
    public function labels(): array {
        return [];
    }
    public function getLabel($attribute){
        return $this->labels()[$attribute] ?? $attribute;
    }
    public function rules(): array{
        return [];
    }

    public array $errors = [];

    public function validate(?array $keys = null) {
        $rulesArray = $this->rules();
        if (!is_null($keys)) {
            $rulesArray = array_intersect_key($rulesArray, array_flip($keys));
        }

        foreach($rulesArray as $attribute => $rules) {
            $value = $this->{$attribute};
            foreach($rules as $rule) {


                $ruleName = $rule;
                if(!is_string($ruleName)){
                    $ruleName = $rule[0];
                }


                if ($ruleName === self::RULE_REQUIRED && !$value){
                    $this->addError($attribute, self::RULE_REQUIRED);
                }
                if ($ruleName === self::RULE_EMAIL && !filter_var($value, FILTER_VALIDATE_EMAIL)){
                    $this->addError($attribute, self::RULE_EMAIL);
                }
                if ($ruleName === self::RULE_MIN && strlen($value) < $rule['min']){
                    $this->addError($attribute, self::RULE_MIN, $rule);
                }
                if ($ruleName === self::RULE_MAX){
                    if ((is_string($value) && strlen($value) > $rule['max']) ||
                        (is_int($value) && $value > $rule['max'])){
                        $this->addError($attribute, self::RULE_MAX, $rule);
                    }
                }
                if ($ruleName === self::RULE_MATCH && $value !== $this->{$rule['match']}) {
                    $rule['match'] = $this->getLabel($rule['match']);
                    $this->addError($attribute, self::RULE_MATCH, $rule);
                }
                if ($ruleName === self::RULE_REGEX && !preg_match($rule['regex'], $value)) {
                    $rule['regex'] = $this->getLabel($rule['regex']);
                    $this->addError($attribute, self::RULE_REGEX, $rule);
                }
                if ($ruleName === self::RULE_REGEX_UPPER && !preg_match('/[A-Z]/', $value)) {
                    $this->addError($attribute, self::RULE_REGEX_UPPER);
                }
                if ($ruleName === self::RULE_REGEX_LOWER && !preg_match('/[a-z]/', $value)) {
                    $this->addError($attribute, self::RULE_REGEX_LOWER);
                }
                if ($ruleName === self::RULE_REGEX_NUMBER && !preg_match('/[0-9]/', $value)) {
                    $this->addError($attribute, self::RULE_REGEX_NUMBER);
                }
                if ($ruleName === self::RULE_REGEX_SPECIAL && !preg_match('/[!@#$%^&*()\-_+={}\[\]|:;<>,.]/', $value)) {
                    $this->addError($attribute, self::RULE_REGEX_SPECIAL);
                }
                if ($ruleName === self::RULE_CONTAINS && !in_array($value, $rule['contains'])){
                    $this->addError($attribute, self::RULE_CONTAINS, $rule['contains']);
                }
            }
        }
        return empty($this->errors);
    }

    public function addError(string $attribute, string $rule, $params = []){
        $message = $this->errorMessages()[$rule] ?? $rule;
        foreach($params as $key => $value){
            $message = str_replace("{{$key}}", $value, $message);
        }
        $this->errors[$attribute][] = $message;
    }

    public function errorMessages(): array{
        return [
            self::RULE_REQUIRED => 'The field is required',
            self::RULE_EMAIL => 'This field must be a valid email address',
            self::RULE_MIN => 'Must be at least {min} characters long',
            self::RULE_MAX => 'Can be no greater than {max} characters long',
            self::RULE_MATCH => 'Must be the same as {match}',
            self::RULE_REGEX => 'Must fit the pattern {regex}',
            self::RULE_CONTAINS => 'Must contain at least one of the following: {contains}',
            self::RULE_REGEX_UPPER => 'Must contain at least one uppercase letter',
            self::RULE_REGEX_LOWER => 'Must contain at least one lowercase letter',
            self::RULE_REGEX_NUMBER => 'Must contain at least one number',
            self::RULE_REGEX_SPECIAL => 'Must contain at least one special character',
        ];
    }

    public function hasError($attribute){
        return $this->errors[$attribute] ?? false;
    }
    public function getFirstError($attribute){
        return $this->errors[$attribute][0] ?? false;
    }
}

?>
