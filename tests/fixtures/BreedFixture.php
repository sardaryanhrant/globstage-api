<?php
namespace app\tests\fixtures;
use yii\test\ActiveFixture;


class BreedFixture extends ActiveFixture
{
    public $tableName = 'breeds';
    public $depends = ['app\tests\fixtures\UserFixture'];


}