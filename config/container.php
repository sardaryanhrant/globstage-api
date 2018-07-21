<?php

use app\common\components\entity\EntityValidator;
use app\common\components\entity\EntityManager;

return [
    'definitions' => [
        'entityRules' => 'app\common\components\entity\EntityRules',
        'entityResource' => 'app\modules\v1\models\EntityResource',
        'entityNestedCollection' => 'app\common\components\entity\EntityNestedCollection',
        'entityMigration' => 'app\common\components\entity\EntityMigration',
        'app\common\components\media\MediaRepositoryInterface' => 'app\common\components\media\UploadFileRepository'
    ],
    'singletons' => [
        'entityManager' => function () {
            $schemePath = realpath(\Yii::getAlias('@app') . '/config/entitys_schema.json');
            $validator = new EntityValidator($schemePath);
            $nestedCollection = Yii::$container->get('entityNestedCollection');

            return new EntityManager($validator, \Yii::$app->params['entities_config'], $nestedCollection);
        },
    ]
];