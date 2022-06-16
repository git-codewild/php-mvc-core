<?php

namespace codewild\phpmvc\components;

class Modal
{
    public const TYPE_DD_ITEM = 'dd_item';
    public const TYPE_BUTTON = 'button';

    protected static $_instances = array();

    public string $type = self::TYPE_BUTTON;
    public string $id;
    public ?string $classes = null;
    public string $title;
    public string $labelName;
    public string $body;
    public string $footer;
    public string $onclick = '';


    public function __construct(string $id, string $title, string $body, string $footer)
    {
        $this->id = $id;
        $this->labelName = $this->id.'Label';
        $this->title = $title;
        $this->body = $body;
        $this->footer = $footer;

        self::$_instances[] = (object) $this;
    }

    public function __toString(): string
    {
        $id = $this->id;
        if ($this->type === self::TYPE_DD_ITEM) {
            $str = "<a class='dropdown-item' class='%s' data-bs-toggle='modal' data-bs-target='#$id' onclick=\"%s\">%s</a>";
        } else {
            $str = "<button type='button' class='btn%s' data-bs-toggle='modal' data-bs-target='#$id' onclick=\"%s\">%s</button>";
        }
        return sprintf($str,
            is_null($this->classes) ? ' btn-secondary' : ' '.$this->classes,
            $this->onclick,
            $this->title);
    }

    public function setClasses(string $classes){
        $this->classes = $classes;
        return $this;
    }

    public function setType(string $type){
        $this->type = $type;
        return $this;
    }

    public function setOnClick(string $onclick){
        $this->onclick = $onclick;
        return $this;
    }

    public function write(){
        $id = $this->id;
        $labelName = $this->labelName;
        $title = $this->title;
        return "
            <div class='modal fade' id='$id' aria-hidden='true' aria-labelledby='$labelName' tabindex='-1'>
                <div class='modal-dialog modal-dialog-centered'>
                    <div class='modal-content'>
                        <div class='modal-header'>
                            <h5 class='modal-title' id='$labelName'>$title</h5>
                            <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                        </div>
                        <div class='modal-body'>".
                            $this->body
                        ."</div>
                        <div class='modal-footer'>".
                            $this->footer
                        ."</div>
                    </div>
                </div>
            </div>";
    }

    public static function getInstances(){
        $return = array();
        foreach(self::$_instances as $instance) {
            $return[] = $instance;
        }
        return $return;
    }
}
