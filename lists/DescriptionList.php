<?php

namespace codewild\csubmboer\core\lists;

use codewild\csubmboer\core\Model;

class DescriptionList extends Model
{
    public static function create(Model $model, ?array $keys = null): string{
        $properties = $model->labels();
        if (!is_null($keys)){
            $properties = array_intersect_key($properties, array_flip($keys));
        }
        $output = "<dl>";
        foreach ($properties as $key => $value) {
            $output .= sprintf("<dt>%s</dt><dd>%s</dd>",
                $value, $model->$key);
        }
        $output .= "</dl>";
        return $output;
    }
}
