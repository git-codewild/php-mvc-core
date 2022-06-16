<?php

namespace codewild\csubmboer\core\form;

class RadioInputGroup extends InputGroup
{
    public function __toString()
    {
        $str = "
            <label for='$this->attribute' class='form-label'>$this->label</label>
            <div class='input-group'>
                  <div class='input-group-text'>
                    <input class='form-check-input mt-0' type='radio' name='%s' value='$this->attribute' aria-label='Radio button for following text input'>
                  </div>
                  %s
                </div>";
        return sprintf($str,
            $this->id."Radio",
            $this->renderInput());
    }
}
