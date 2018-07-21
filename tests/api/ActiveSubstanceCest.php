<?php
require_once 'BaseCest.php';


class ActiveSubstanceCest extends BaseCest
{

    protected $base_url = 'active-substances';

    protected $resource_data = [
      'data' => [
        'type' => 'active_substance',
        'attributes' => [
          'name' => 'Солод',
          'name_en' => 'Solod',
        ],
      ],
    ];

    protected $resource_name = 'Активное вещество';

    protected $resource_required_fields = ['name', 'name_en'];

    protected $resource_all_fields = ['name', 'name_en'];

    protected $filter_field = 'name';

    protected $sort_field = 'name';

    protected $filter_field_value = 'Пенталгин';

    protected $sort_field_value = 'Соли радия';

    public function _fixtures()
    {
        return [
          'active-substances' => [
            'class' => \app\tests\fixtures\ActiveSubstanceFixture::class,
          ],
        ];
    }
    
}