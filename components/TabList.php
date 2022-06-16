<?php

namespace codewild\phpmvc\components;

use codewild\phpmvc\Model;

class TabList
{
    public function begin(string $id = null, string $classes = null){
        return sprintf(
            '<ul %sclass="nav%s" role="tablist">',
            is_null($id) ? '' : "id='$id' ",
            is_null($classes) ? '' : ' '.$classes,
        );
    }

    public function navItem(Model $model, string $attribute, int $n) {
        $attr = $model->$attribute;
        return "<li class='nav-item' role='presentation'>
            <button class='btn nav-link' id='slide$n-tab' data-bs-toggle='pill' data-bs-target='#slide$n' type='button' role='tab' aria-controls='$attr'>
                $n: $attr
            </button>
        </li>";
    }

    public function contentStart(string $id, string $classes = null){
        return sprintf('</ul>
            <div %sclass="tab-content%s" style="min-height:500px;">',
            !is_null($id) ? "id='$id' " : '',
            !is_null($classes) ? " $classes" : ''
        );
    }

}
