<?php

namespace codewild\phpmvc;

use codewild\phpmvc\db\DbModel;

abstract class UserModel extends DbModel {
    abstract public function getDisplayName(): string;

    abstract function isInRole($roleId): bool;
}
