<?php
require_once 'BaseCest.php';

class OrgTypeCest extends BaseCest
{
    protected $base_url = 'org-types';
    protected $resource_data = ['data' => [
        'type' => 'org_type',
        'attributes' => [
            'name' => 'ТТО',
            'description' => 'Тестовый тип организации'
        ]
    ]];


    protected $resource_name = 'тип организации';
    protected $resource_required_fields = ['name', 'description'];
    protected $resource_all_fields = ['name', 'description'];
    protected $filter_field = 'name';
    protected $sort_field = 'name';
    protected $filter_field_value = 'КВМ';
    protected $sort_field_value = 'ОЯЕ';

    public function _before(ApiTester $I)
    {
        parent::_before($I);

        $this->overflowField = [
            'data' => [
                'type' => 'orgType',
                'attributes' => ['name' => str_repeat('blabla', 1000)]
            ]
        ];
    }

    public function _fixtures()
    {
        return [
            'orgtypes' => \app\tests\fixtures\OrgTypeFixture::class
        ];
    }

}