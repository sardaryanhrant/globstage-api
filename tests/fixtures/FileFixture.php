<?php

namespace app\tests\fixtures;

use yii\test\ActiveFixture;

class FileFixture extends ActiveFixture
{
    public $depends = ['app\tests\fixtures\UserFixture'];

    public $tableName = 'files';
}