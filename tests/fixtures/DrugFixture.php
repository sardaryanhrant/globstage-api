<?php

namespace app\tests\fixtures;
use yii\test\ActiveFixture;


class DrugFixture extends ActiveFixture
{
    public $tableName = 'drugs';
    public $depends = ['app\tests\fixtures\UserFixture', 'app\tests\fixtures\TmcTypeFixture',
        'app\tests\fixtures\MeasureFixture', 'app\tests\fixtures\ActiveSubstanceFixture',
        'app\tests\fixtures\FileFixture'];

}