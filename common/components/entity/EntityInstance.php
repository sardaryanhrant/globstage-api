<?php

namespace app\common\components\entity;


class EntityInstance
{

    protected $alias_name;
    protected $table_name;
    protected $type_name;

    protected $attributes = [];
    protected $relationships = [];
    protected $pluralRelations = [];

    /**
     * @var EntityNestedCollection[]
     */
    protected $nestedCollections = [];

    public function __construct($entity_data, $name)
    {
        $this->alias_name = $name;
        $this->table_name = self::tableNameFromEntityName($name);
        $this->type_name = $this->alias_name;

        $this->attributes = $entity_data['attributes'];

        if (isset($entity_data['relations'])) {
            $this->relationships = $entity_data['relations'];
        }

        if (isset($entity_data['plural_relations'])) {
            $this->pluralRelations = $entity_data['plural_relations'];
        }

        $this->getTableName();
    }

    public static function tableNameFromEntityName(string $name)
    {
        return str_replace('-', '_', $name);
    }

    public static function inflectTypeName($type) : string
    {
        $stoplist = ['species'];

        if (!in_array($type, $stoplist)) {
            $type = substr($type, 0, -1);
            $type = str_replace('-', '_', $type);
        }

        return $type;
    }

    public function addNestedCollection(EntityNestedCollection $collection)
    {
        $this->nestedCollections[] = $collection;
    }

    public function getAllNestedCollections()
    {
        return $this->nestedCollections;
    }

    public function getNestedCollection($name)
    {
        $result = null;

        foreach ($this->nestedCollections as $col) {
            if ($col->getEntityCollectionName() == $name) {
                $result = $col;
                break;
            }
        }

        return $result;
    }

    /**
     * @return mixed
     */
    public function getAliasName()
    {
        return $this->alias_name;
    }

    /**
     * @return mixed
     */
    public function getTableName()
    {
        return $this->table_name;
    }

    /**
     * @return string
     */
    public function getTypeName() : string
    {
        return self::inflectTypeName($this->type_name);
    }

    /**
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function getRelationships()
    {
        return $this->relationships;
    }

    /**
     * @return array
     */
    public function getPluralRelations(): array
    {
        return $this->pluralRelations;
    }

    public function getRelationByField($relname, $field = 'link')
    {
        $relation = null;

        foreach ($this->relationships as $rel) {
            if ($rel[$field] == $relname) {
                $relation = $rel;
                break;
            }
        }

        return $relation;
    }
}