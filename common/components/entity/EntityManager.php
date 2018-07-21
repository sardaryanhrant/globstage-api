<?php

namespace app\common\components\entity;


class EntityManager
{
    protected $validator;
    protected $config_path;
    protected $config_data = [];

    /**
     * @var EntityNestedCollection $entityNestedCollection
     */
    protected $entityNestedCollection;

    /**
     * @var EntityNestedCollection[] $nestedCollections
     */
    protected $nestedCollections;

    public function __construct(EntityValidator $validator, $config_path, EntityNestedCollection $entityNestedCollection)
    {
        $this->validator = $validator;
        $this->config_path = $config_path;
        $this->entityNestedCollection = $entityNestedCollection;

        $this->parseConfig();
    }

    public function validate()
    {
        return $this->validator->validate();
    }

    public function getValidator()
    {
        return $this->validator;
    }

    /**
     * @param string $name
     * @return null|EntityInstance
     * @throws EntityException
     */
    public function getEntity(string $name)
    {
        if (isset($this->config_data[$name])) {
            if (isset($this->config_data[$name]['meta']['class'])) {
                $class = $this->config_data[$name]['meta']['class'];
                return new $class;
            }
            $entity_data = $this->config_data[$name];

            if(isset($entity_data['meta']['active']) && $entity_data['meta']['active'] === false){
                return null;
            }

            if (isset($entity_data['meta']['virtual']) && $entity_data['meta']['virtual'] === true) {
                throw new EntityException("Сущность {$name} объявлена как виртуальная");
            }

            while (isset($entity_data['meta']['parent']) && $entity_data['meta']['parent']) {
                $base = $entity_data['meta']['parent'];
                $entity_data = array_merge_recursive($entity_data, $this->config_data[$base]);
                unset($entity_data['meta']['parent']);
            }

            $entityInstance = new EntityInstance($entity_data, $name);

            foreach ($this->findNestedCollections($name, $entityInstance) as $col) {
                $entityInstance->addNestedCollection($col);
            }

            return $entityInstance;

        } elseif ($this->validator->getErrors()) {
            throw new EntityException(print_r($this->validator->getErrors(), true));
        } else {
            throw new EntityException("Не найден конфиг для сущности {$name}");
        }
    }

    public function getAllEntitiesNames()
    {
        $names = array_slice(array_keys($this->config_data), 1);
        foreach ($names as $index => $name){
            if (isset($this->config_data[$name]['meta']['active']) && $this->config_data[$name]['meta']['active'] === false)
                unset($names[$index]);
        }
        return $names;
    }

    public function getRawConfig()
    {
        return $this->config_data;
    }

    protected function findNestedCollections($entityName, EntityInstance $entityInstance)
    {
        $cols = [];
        $names = $this->getAllEntitiesNames();

        foreach ($names as $entname) {
            $colname = $entityName . '-' . $entname;

            if (isset($this->config_data[$colname])) {
                $collectionData = $this->config_data[$colname];

                $colInstance = new $this->entityNestedCollection;
                $colInstance->init([$colname => $collectionData]);

                $cols[] = $colInstance;
            }
        }

        if ($pluralRelations = $entityInstance->getPluralRelations()) {
            foreach ($pluralRelations as $relation) {
                $colInstance = new $this->entityNestedCollection;
                $colInstance->init($relation, true);

                $cols[] = $colInstance;
            }
        }

        return $cols;
    }

    protected function parseConfig()
    {
        $data = require $this->config_path;

        if ($data) {
            $this->config_data = $data;

            $this->validator->setConfig($this->getRawConfig());
        }
    }
}