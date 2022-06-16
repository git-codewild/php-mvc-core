<?php

namespace codewild\csubmboer\core\components;

use codewild\csubmboer\core\Nav;

class ListGroup
{
    public function __construct(Nav $nav, ?string $classes = null)
    {
        echo sprintf("<div class='list-group%s'>", " $classes" ?? '');
        foreach ($nav->pages() as $key => $item){
            $route = $nav->routes()[$item];
            $title = $nav->titles()[$item];
            if ($key === $nav->active()) {
                echo "<a href='$route' class='list-group-item list-group-item-action active' aria-current='true'>$title</a>";
            } else {
                echo "<a href='$route' class='list-group-item list-group-item-action'>$title</a>";
            }
        }
        echo "</div>";
    }


}
