<?php
require_once 'BaseCest.php';

class BreedCest  extends BaseCest
{
    protected $base_url = 'breeds';
    protected $resource_data = ['data' => [
        'type' => 'breed',
        'attributes' => [
            'name' => 'Собакен',
            'description' => 'Лает и кусает'
        ]
    ]];
    protected $resource_name = 'порода';
    protected $resource_required_fields = ['name'];
    protected $resource_all_fields = ['name', 'description'];
    protected $filter_field = 'name';
    protected $sort_field = 'name';
    protected $filter_field_value = 'Мопс';
    protected $sort_field_value = 'Пудель';

    public function _fixtures()
    {
        return [
            'breeds' => \app\tests\fixtures\BreedFixture::class
        ];
    }
}