<?php

namespace codewild\csubmboer\core\table;

use codewild\csubmboer\core\Model;

class BaseRow
{
    public Model $model;
    public array $attributes;

    public string $lastColumn = '';

    public function __construct(Model $model, ?array $attributes = null){
        $this->model = $model;

        if (is_null($attributes)){
            $this->attributes = $model::attributes();
        } else {
            $this->attributes = $attributes;
        }
    }

    public function __toString(){
        $str = "<tr class='position-relative'>";
        foreach($this->attributes as $key => $value){
            $str.=sprintf("<td>%s</td>",
                    $this->renderInput($key, $value)
                );
        }
        return $str.$this->lastColumn."</tr>";
    }

    public function renderInput($key, string $value): string {
        if (!empty($value)){
            if(is_int($key)){
                return $this->model->$value ?? $value;
            } else {
                if ($value === $key) {
                    return $this->model->$key;
                } else {
                    return $value;
                }
            }
        }
        return $value;
    }

    public function lastColumn(string $type, string $value)
    {
        switch ($type){
            case 'link':
                $this->lastColumn  = "<td><a class='stretched-link' href='$value'></a></td>";
                break;
        }

        return $this;
    }
}
