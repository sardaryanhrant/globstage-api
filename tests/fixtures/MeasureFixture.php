<?php
namespace app\tests\fixtures;
use yii\test\ActiveFixture;


class MeasureFixture extends ActiveFixture
{
    public $tableName = 'measures';
    public $depends = ['app\tests\fixtures\UserFixture'];
}