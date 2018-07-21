<?php

namespace app\common\components\entity;

class EntityMigration
{
    /** @var EntityManager $entityManager */
    public $entityManager;

    /**
     * @param string $name
     * @return EntityInstance|null
     * @throws EntityException
     */
    protected function getEntity(string $name)
    {
        return $this->entityManager->getEntity($name);
    }

    /**
     * @param string $name
     * @throws EntityException
     */
    public function create(string $name)
    {
        $entity = $this->getEntity($name);

        $fields = $this->makeFieldsParam($entity->getAttributes());
        $relations = $this->makeRelationsParam($entity->getRelationships());

        if (!empty($fields)) {
            $this->createTableMigration($entity, $fields);
        }

        if (!empty($relations)) {
            $this->addRelationsMigration($entity, $relations);
        }
    }

    /**
     * @param string $name
     * @throws EntityException
     */
    public function diff(string $name)
    {
        $entity = $this->getEntity($name);

        $tableSchema = \Yii::$app->db->getTableSchema($entity->getTableName());
        print_r($tableSchema->getColumnNames());
        print_r($entity->getAttributes());
        die();
    }

    /**
     * @param array $attributes
     * @return array
     */
    protected function makeFieldsParam(array $attributes) : array
    {
        $fields = [];
        foreach ($attributes as $attribute) {
            $str = "{$attribute['name']}:{$attribute['type']}";

            if (isset($attribute['unique']) && $attribute['unique']) {
                $str .= ":unique";
            }

            if (isset($attribute['required']) && $attribute['required']) {
                $str .= ":notNull";
            }

            $fields[] = $str;
        }

        return $fields;
    }

    /**
     * @param array $relationships
     * @return array
     * @throws EntityException
     */
    protected function makeRelationsParam(array $relationships) : array
    {
        $relations = [];
        foreach ($relationships as $relation) {
            $foreignEntity = $this->getEntity($relation['link']);

            if ($foreignEntity) {
                $str = "{$relation['property']}:integer:foreignKey({$foreignEntity->getTableName()})";
                $relations[] = $str;
            }
        }

        return $relations;
    }

    protected function createTableMigration(EntityInstance $entity, array $fields) : void
    {
        $fields = implode(',', $fields);
        \Yii::$app->runAction('migrate/create', [
            "create_{$entity->getTableName()}_table",
            'fields' => $fields,
            'interactive' => 0
        ]);
    }

    protected function dropFieldsMigration(EntityInstance $entity, array $fields) : void
    {
        $fields = implode(',', $fields);
        \Yii::$app->runAction('migrate/create', [
            "from_columns_from_{$entity->getTableName()}_table",
            'fields' => $fields,
            'interactive' => 0
        ]);
    }

    protected function addFieldsMigration(EntityInstance $entity, array $fields) : void
    {
        $fields = implode(',', $fields);
        \Yii::$app->runAction('migrate/create', [
            "add_columns_to_{$entity->getTableName()}_table",
            'fields' => $fields,
            'interactive' => 0
        ]);
    }

    protected function changeFieldsMigration(EntityInstance $entity, array $fields) : void
    {
        //
    }

    protected function removeRelationsMigration(EntityInstance $entity, array $relations) : void
    {
        //
    }

    protected function addRelationsMigration(EntityInstance $entity, array $relations) : void
    {
        $fields = implode(',', $relations);
        \Yii::$app->runAction('migrate/create', [
            "create_{$entity->getTableName()}_table",
            'fields' => $fields,
            'interactive' => 0
        ]);
    }
}