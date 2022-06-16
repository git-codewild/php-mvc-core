<?php

namespace codewild\csubmboer\core;

use codewild\csubmboer\core\db\DbModel;

abstract class UserModel extends DbModel {
    abstract public function getDisplayName(): string;

    abstract function isInRole($roleId): bool;
}
