<?php
namespace app\tests\fixtures;
use yii\test\ActiveFixture;


class OrgFixture extends ActiveFixture
{
    public $tableName = 'organizations';
    public $depends = ['app\tests\fixtures\OrgTypeFixture', 'app\tests\fixtures\UserFixture'];


}