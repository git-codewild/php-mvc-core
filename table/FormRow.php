<?php

namespace codewild\csubmboer\core\table;

class FormRow extends BaseRow
{
    public function renderInput($key, $value): string
    {
        $fieldStr = "
                <form class='%s' action='' method='post'>
                    <input type='text' name='id' value='%s' hidden>
                    %s
                    <input type='submit' name='%s' value='%s' class='btn%s' %s>
                    <div class='invalid-feedback'>%s</div>
                </form>";
        $buttonStr = "
                <form class='%s' action='' method='post'>
                    <input type='text' name='id' value='%s' hidden>
                    %s
                    <input type='submit' name='%s' value='%s' class='btn%s' %s>
                </form>";
        switch ($key){
            case 'move':
                $moveUp = sprintf($buttonStr,
                    'btn',
                    $this->model->id,
                    '',
                    'moveUp',
                    '&uparrow;',
                    ' btn-info',
                    $this->model->n === 1 ? ' disabled' : '',
                );
                $indent = sprintf($buttonStr,
                    'btn',
                    $this->model->id,
                    '',
                    'indent',
                    is_null($this->model->parentId) ? '&rarr;' : '&larr;',
                    ' btn-info',
                    $this->model->n === 1 ? ' disabled' : '',
                );
                return "<div class='button-group'>$indent$moveUp</div>";
                break;
            case 'rename':
                $isInvalid = $this->model->hasError('title') ? ' is-invalid' : '';

                return sprintf($fieldStr,
                    'form-group',
                    $this->model->id,
                    "<input type='text' name='title' value='".$value."' class='$isInvalid'>",
                    'rename',
                    'Save',
                    ' btn-primary',
                    '',
                    $this->model->getFirstError('title'));
                break;
            case 'delete':
                return sprintf($buttonStr,
                    '',
                    $this->model->id,
                    '',
                    'delete',
                    'Delete',
                    ' btn-danger',
                    '',
                );
                break;
            case 'link':
                $title = $value['title'];
                $href = $value['href'];
                return "<a href='$href' class='link'>$title</a>";
            default:
                return parent::renderInput($key, $value);
        }
    }
}
