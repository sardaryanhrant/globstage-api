<?php

namespace app\tests\fixtures;

use yii\test\ActiveFixture;

class UserFixture extends ActiveFixture
{

    public $modelClass = 'app\common\models\UserModel';

    protected function getData()
    {
        $hash =  \Yii::$app->getSecurity()->generatePasswordHash(USER_PASSWORD);

        return [
            'user1' => [
                'login' => USER_LOGIN, 'password' => $hash
            ]
        ];
    }


}