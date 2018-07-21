<?php
require_once 'BaseCest.php';

class OrganizationCest extends BaseCest
{
    protected $base_url = 'organizations';
    protected $resource_data = ['data' => [
        'type' => 'organization',
        'attributes' => [
            'name' => 'Айболит',
            'short_name' => 'Клиника доктора айболита',
            'id_org_type' => 1,
            'id_address' => 1,
            'inn' => '4321656', 'kpp' => '979870977', 'ogrn' => '43423453423423'
        ]
    ]];
    protected $resource_name = 'организация';
    protected $resource_required_fields = ['name'];
    protected $resource_all_fields = ['name', 'short_name', 'inn', 'kpp', 'ogrn'];
    protected $filter_field = 'name';
    protected $sort_field = 'name';
    protected $filter_field_value = 'Тестовая организация';
    protected $sort_field_value = 'Яблоневый сад';

    public function _fixtures()
    {
        return [
            'orgs' => [
                'class' => \app\tests\fixtures\OrgFixture::class,
            ]
        ];
    }
}