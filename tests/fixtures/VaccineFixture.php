<?php
namespace app\tests\fixtures;
use yii\test\ActiveFixture;


class VaccineFixture extends ActiveFixture
{
    public $modelClass = 'app\modules\v1\models\VaccineResource';
    public $depends = ['app\tests\fixtures\TmcTypeFixture', 'app\tests\fixtures\UserFixture'];


}