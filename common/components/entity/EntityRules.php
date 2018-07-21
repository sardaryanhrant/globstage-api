<?php

namespace app\common\components\entity;


use app\common\components\RelationExistValidator;

class EntityRules
{
    protected $allowed_types = ['integer', 'date', 'string', 'double'];
    protected $allowed_restricts = ['required', 'unique'];

    /**
     * @var EntityInstance
     */
    protected $entityInstance;

    protected $entityAttrs = [];
    protected $entityRels  = [];

    protected $rules;

    /**
     * EntityRules constructor.
     * @param EntityInstance $entityInstance
     */
    public function init(EntityInstance $entityInstance)
    {
        $this->entityInstance = $entityInstance;
        $this->entityAttrs = $entityInstance->getAttributes();
        $this->entityRels = $entityInstance->getRelationships();
    }

    public function getRules()
    {
        $this->composeRules();

        return $this->rules;
    }

    protected function composeRules()
    {
        foreach ($this->allowed_restricts as $restrict) {
            if ($restrictFields = $this->getFieldsByRestriction($restrict)) {
                $this->rules[] = [$restrictFields, $restrict, 'on' => ['insert', 'update']];
            }
        }

        foreach ($this->allowed_types as $type) {
            if ($fields = $this->getFieldsByType($type)) {
                $this->rules[] = [$fields, $type, 'on' => ['insert', 'update']];
            }

            if ($fields = $this->getFieldsByType($type, true)) {
                foreach ($fields as $item) {
                    $this->rules[] = [key($item), $type, 'max' => current($item)];
                }
            }
        }

        foreach ($this->getRelationFields('link') as $field => $type) {
            $this->rules[] = [$field, RelationExistValidator::class, 'targetRelation' => $type];
        }
    }

    protected function getFieldsByRestriction($restriction)
    {
        $fields = [];

        array_walk($this->entityAttrs, function($value) use (&$fields, $restriction) {
            if (!empty($value[$restriction])) {
                $fields[] = $value['name'];
            }
        });

        return count($fields) ? $fields : null;
    }

    protected function getFieldsByType($type, $incl_max = false)
    {
        $fields = [];

        array_walk($this->entityAttrs, function($field) use (&$fields, $type, $incl_max) {
            if ($field['type'] == $type) {
                $max = $field['max'] ?? null;

                if ($incl_max) {
                    $max ? array_push($fields, [$field['name'] => $max]) : null;
                }
                else {
                    $fields[] = $field['name'];
                }
            }
        });

        if ($fields && $type == 'integer') {
            $fields = array_merge($fields, array_keys($this->getRelationFields()));
        }

        return count($fields) ? $fields : null;
    }

    protected function getRelationFields($key = 'propname')
    {
        $fields = [];

        array_walk($this->entityRels, function($relation) use (&$fields, $key) {
            $fields[$relation['property']] = $relation[$key];
        });

        return $fields;
    }
}