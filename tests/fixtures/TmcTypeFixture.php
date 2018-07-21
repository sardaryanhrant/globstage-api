<?php
namespace app\tests\fixtures;

use yii\test\ActiveFixture;

class TmcTypeFixture extends ActiveFixture
{
    public $tableName = 'tmc_types';
    public $depends = ['app\tests\fixtures\UserFixture'];
}