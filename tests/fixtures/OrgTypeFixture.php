<?php

namespace app\tests\fixtures;
use yii\test\ActiveFixture;


class OrgTypeFixture extends ActiveFixture
{

    public $tableName = 'org_types';
    public $depends = ['app\tests\fixtures\UserFixture'];

    protected function getData()
    {
        return [
            'org1' => [
                'name' => 'КВМ', 'description' => 'Комитет ветеринарии города Москвы'
            ],
            'org2' => [
                'name' => 'ОЯЕ', 'description' => 'ОЯЕБУ'
            ]
        ];
    }
}