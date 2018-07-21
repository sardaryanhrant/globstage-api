<?php
require_once 'BaseCest.php';

class MeasureCest extends BaseCest
{
    protected $base_url = 'measures';
    protected $resource_data = ['data' => [
        'type' => 'measure',
        'attributes' => [
            'name' => 'Единиц измерений какой то',
            'description' => 'Описание',
        ]
    ]];

    protected $resource_name = 'измерений';
    protected $resource_required_fields = ['name', 'description'];
    protected $resource_all_fields = ['name', 'description'];
    protected $filter_field = 'name';
    protected $sort_field = 'name';
    protected $filter_field_value = 'Единиц измерений 1';
    protected $sort_field_value = 'Единиц измерений 2';

    public function _fixtures()
    {
        return [
            'measures' => [
                'class' => \app\tests\fixtures\MeasureFixture::class,
            ]
        ];
    }

}
