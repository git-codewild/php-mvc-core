<?php

namespace codewild\phpmvc\table;

use codewild\phpmvc\Model;

class Table extends Model
{
    public static function begin(Model $model, ?array $attributes = null, ?string $classes = null){
        echo sprintf("
            <table class='table%s'>
                <thead>
                    <tr>",
            is_null($classes) ? "" : " $classes");
        $attributes = is_null($attributes) ? $model::attributes() : $attributes;
        foreach($attributes as $attr){
            echo sprintf("<th scope='col'>%s</th>", $model->getLabel($attr));
        }
        echo "</tr></thead>
        <tbody>";
        return new Table();
    }
    public function row(Model $model, ?array $attributes = null){
        return new BaseRow($model, $attributes);
    }
    public function formRow(Model $model, array $attributes){
        return new FormRow($model, $attributes);
    }
    public static function end(){
        echo "</tbody>
        </table>";
    }
}
