<?php

namespace codewild\phpmvc\db;

use codewild\phpmvc\Application;
use codewild\phpmvc\exception\DbException;
use codewild\phpmvc\Model;

abstract class BaseDbModel extends Model
{
    abstract static public function tableName(): string;
    abstract static public function primaryKey(): string|array;

    public const RULE_UNIQUE =  'unique';

    public function validate(?array $keys = null)
    {
        $rulesArray = $this->rules();
        if (!is_null($keys)) {
            $rulesArray = array_intersect_key($rulesArray, array_flip($keys));
        }
        foreach($rulesArray as $attribute => $rules) {
            $value = $this->{$attribute};
            foreach($rules as $rule) {
                $ruleName = $rule;
                if(!is_string($ruleName)){
                    $ruleName = $rule[0];
                }
                if ($ruleName === self::RULE_UNIQUE) {
                    $condition = $rule['condition'] ?? [];
                    // Find existing records with unique property value
                    $record = self::findMany(array_merge([$attribute => $value], $condition));
                    // Filter records with the same primary key
                    $pk = static::primaryKey();
                    $id = $this->$pk;
                    $filter = array_filter($record, function($e) use ($pk, $id) {
                        return ($e->$pk !== $id);
                    });
                    if(!empty($filter)){
                        $this->addError($attribute, self::RULE_UNIQUE, ['field' => $this->getLabel($attribute)]);
                    }
                }
            }
        }
        return empty($this->errors) && parent::validate($keys);
    }

    public function errorMessages(): array
    {
        $output = parent::errorMessages();
        $output[self::RULE_UNIQUE] = 'Record with this {field} already exists';
        return $output;
    }

    public function save(){
        $tableName = $this->tableName();
        $attributes = static::attributes();
        $params = array_map(fn($attr) => ":$attr", $attributes);
        $stmt = self::prepare("INSERT INTO $tableName 
            (".implode(',', $attributes).") 
            VALUES (".implode(',', $params).");"
        );

        foreach($attributes as $attribute){
            $stmt->bindValue(":$attribute", $this->{$attribute});
        }

        return $stmt->execute();
    }

    public function update(?array $keys = null){
        $tableName = $this->tableName();
        $pk = static::primaryKey();
        $attributes = is_null($keys) ? static::attributes() : array_intersect(static::attributes(), $keys);
        $params = array_map(fn($attr) => "$attr = :$attr", $attributes);
        $sql = implode(', ', $params);
        if (is_array($pk)){
            $keys = array_map(fn($k) => "$k = :$k", $pk);
            $keys = implode(' AND ', $keys);
            $stmt = self::prepare("UPDATE $tableName SET $sql WHERE $keys");
            foreach($pk as $k){
                $stmt->bindValue(":$k", $this->{$k});
            }
        } else {
            $stmt = self::prepare("UPDATE $tableName SET $sql WHERE $pk = :$pk");
            $stmt->bindValue(":$pk", $this->{$pk});
        }
        foreach ($attributes as $attribute) {
            $stmt->bindValue(":$attribute", $this->{$attribute});
        }
        return $stmt->execute();
    }

    public function delete(){
        $tableName = $this->tableName();
        $pk = static::primaryKey();
        if (is_array($pk)){
            $sql = array_map(fn($k) => "$k = :$k", $pk);
            $sql = implode(' AND ', $sql);
            $stmt = self::prepare("DELETE FROM $tableName WHERE $sql");
            foreach($pk as $k){
                $stmt->bindValue(":$k", $this->{$k});
            }
        } else {
            $stmt = self::prepare("DELETE FROM $tableName WHERE $pk = :$pk");
            $stmt->bindValue(":$pk", $this->{$pk});
        }
        return $stmt->execute();
    }

    public static function findOne($where){
        $attributes = static::attributes();
        if (is_array(static::primaryKey())){
            $attributes = array_merge(static::primaryKey(), $attributes);
        } else {
            array_unshift($attributes, static::primaryKey());
        }
        $attr = implode(', ', $attributes);
        
        $stmt = static::createStmt($where, null, $attr);
        if($stmt->rowCount() !== 1){
            return false;
        } else {
            return $stmt->fetchObject(static::class);
        }
    }

    public static function findMany($where, ?string $orderBy = null){
        $stmt = static::createStmt($where, $orderBy);
        return $stmt->fetchAll(\PDO::FETCH_CLASS, static::class);
    }

    public static function findAll(){
        $tableName = static::tableName();
        $stmt = self::prepare("SELECT * from $tableName");
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_CLASS, static::class);
    }

    public static function prepare(string $sql){
        return Application::$app->db->pdo->prepare($sql);
    }

    public static function lastInsertId(){
        $stmt = Application::$app->db->pdo->query("SELECT @last_insert_id");
        return $stmt->fetchColumn();
    }

    private static function createStmt($where, ?string $orderBy = null, ?string $attributes = null): \PDOStatement{
        $tableName = static::tableName();
        // Make a copy of $where[] to store values
        $values = $where;
        // Alter where in order to select NULL values
        array_walk($where, function (&$item, $key)
        {
            if ($item) {
                $item = "$key = :$key";
            } else {
                $item = "$key IS NULL";
            }
        });
        // Create and prepare statement
        $sql = implode(" AND ", $where);
        $sql = $sql.=is_null($orderBy)?"":" ORDER BY $orderBy";
        $sql = sprintf("SELECT %s FROM $tableName WHERE $sql",
            is_null($attributes) ? '*' : $attributes);
        $stmt = self::prepare("$sql");

        // Pass $values[] back into statement and bind to item if not null
        foreach ($values as $key => $value){
            if($value) {
                $stmt->bindValue(":$key", $value);
            }
        }
        $stmt->execute();
        return $stmt;
    }
}
