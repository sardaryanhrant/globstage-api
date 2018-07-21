<?php
namespace app\common\components;

use Yii;
use yii\db\Query;
use yii\validators\ExistValidator;

class RelationExistValidator extends ExistValidator
{

    public function validateAttribute($model, $attribute)
    {
        $entityManager = Yii::$container->get('entityManager');
        $entityInstance = $entityManager->getEntity($this->targetRelation);

        $query = new Query();
        $query->from($entityInstance->getTableName())->where(['id' => $model->$attribute]);

        if (!$query->exists()) {
            $this->addError($model, $attribute, "Некорректный аттрибут {attribute} для связи {relation}", [
                'attribute' => $attribute, 'relation' => $this->targetRelation
            ]);
        }
    }
}