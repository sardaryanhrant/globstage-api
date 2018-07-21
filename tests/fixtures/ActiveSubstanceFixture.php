<?php
namespace app\tests\fixtures;

use yii\test\ActiveFixture;

class ActiveSubstanceFixture extends ActiveFixture
{
    public $tableName = 'active_substances';
    public $depends = ['app\tests\fixtures\UserFixture'];
}