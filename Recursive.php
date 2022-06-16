<?php

namespace codewild\csubmboer\core;

abstract class Recursive extends db\DbModel
{
    public int $n = 0; // Index
    public ?string $parentId = null;
    public ?array $children = [];

    public static function attributes(): array
    {
        $array = parent::attributes();
        array_push($array, 'parentId', 'n');
        return $array;
    }

    public function labels(): array{
        return [
            'n' => 'Index',
            'parentId' => 'Parent'
        ];
    }

    public function rules(): array
    {
        return [
            'n' => [self::RULE_REQUIRED, [self::RULE_UNIQUE, 'condition' =>['parentId' => $this->parentId]]]
        ];
    }

    public function delete()
    {
        $children = self::findMany(['parentId' => $this->id]);
        $siblings = self::findMany(['parentId' => $this->parentId]);

        foreach ($children as $child){
            $child->parentId = $this->parentId;
            $child->n = ($this->n - 1) + $child->n;
            $child->update();
        }

        foreach($siblings as $sib){
            if ($sib->n > $this->n) {
                $sib->n -= 1 - (count($children) ?? 0);
                $sib->update();
            }
        }

        return parent::delete();
    }

    public function findChildren(){
        $this->children = static::class::findMany(['parentId' => $this->id], 'n');
    }

    public static function recursiveTree(array|object $array){
        $buffer = '<ul>';
        $newArray = array();

        $array = (array) $array;

        array_walk_recursive($array, function($item, $key) use (&$newArray) {
            if(is_object($item)) {
                $newArray[$key] = ['title' => $item->title];
                if (is_array($item->children)){
                    foreach($item->children as $child) {
                        $newArray[$key]['children'][] = ['title' => $child->title];
                    }
                }
            }
            return $newArray;
        });

        foreach ($newArray as $key => $item){
            $buffer.="<li>".$item['title']."</li><ul>";
            if (array_key_exists('children', $item)) {
                foreach ($item['children'] as $child) {
                    $buffer .= "<li>".$child['title']."</li>";
                }
            }
            $buffer.='</ul></li>';
        }
        $buffer.="</ul>";

        return $buffer;
    }

    public static function filter(array $input, array $conditions){
        $results = array();
        foreach($input as $k => $v){
            $conditional = 1;
            foreach ($conditions as $key => $value){
                if ($v->$key === $value){
                    $conditional *= 1;
                } else {
                    $conditional *= 0;
                }
            }
            if ($conditional === 1)
            {
                $results[] = current([$k => $v]);
            }
            if (!empty($v->children)){
                $results = array_merge($results, static::filter($v->children, $conditions));
            }
        };
        return $results;
    }
}
