<?php

namespace codewild\csubmboer\core\db;

use codewild\csubmboer\core\Application;
use codewild\csubmboer\core\exception\DbException;
use codewild\csubmboer\core\exception\NotFoundException;
use codewild\csubmboer\core\Model;

abstract class DbModel extends BaseDbModel {
    public const DEFAULT_UUID = '00000000-0000-00';

    public static function primaryKey(): string {
        return 'id';
    }

    public string $id = self::DEFAULT_UUID;

    public function shortId(): string{
        return substr($this->id, 0, 7);
    }

    public static function findByShortId(string $id)
    {
        $tableName = static::tableName();
        $sql = "SELECT * FROM $tableName WHERE LEFT (id, 7) = :id";
        $stmt = static::prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        return $stmt->fetchObject(static::class);
    }



}

?>
