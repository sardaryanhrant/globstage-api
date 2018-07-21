<?php

namespace app\common\components\entity;


use app\modules\v1\models\EntityResource;

class EntityResourceFactory
{

    /**
     * @param $name
     * @return EntityResource
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\di\NotInstantiableException
     */
    public static function getResource($name)
    {
        \Yii::$container->set('entityInstance', function($container) use ($name) {
            $manager = $container->get('entityManager');
            $entity = $manager->getEntity($name);
            $entity->getTableName();

            return $entity;
        });

        $entityInstance = \Yii::$container->get('entityInstance');
        if (!($entityInstance instanceof EntityInstance)) {
            return $entityInstance;
        }

        $resource = \Yii::$container->get('entityResource');

        return $resource;
    }
}