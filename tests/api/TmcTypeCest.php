<?php
require_once 'BaseCest.php';


class TmcTypeCest extends BaseCest
{
    protected $base_url = 'tmc-types';
    protected $resource_data = ['data' => [
        'type' => 'tmc_type',
        'attributes' => [
            'name' => 'Евагрин',
            'description' => 'Я просто так улыбаюсь',
            'tmc_class' => 'drug'
        ]
    ]];
    protected $resource_name = 'тип ТМЦ';
    protected $resource_required_fields = ['name', 'tmc_class'];
    protected $resource_all_fields = ['name', 'tmc_class', 'description'];
    protected $filter_field = 'name';
    protected $sort_field = 'name';
    protected $filter_field_value = 'Некий препарат';
    protected $sort_field_value = 'Пенталгин';

    public function _fixtures()
    {
        return [
            'orgs' => [
                'class' => \app\tests\fixtures\TmcTypeFixture::class,
            ]
        ];
    }
}