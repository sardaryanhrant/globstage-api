<?php
namespace app\tests\fixtures;
use yii\test\ActiveFixture;


class EquipmentFixture extends ActiveFixture
{
    public $tableName = 'equipments';
    public $depends = ['app\tests\fixtures\TmcTypeFixture', 'app\tests\fixtures\UserFixture'];


}