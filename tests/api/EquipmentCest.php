<?php
require_once 'BaseCest.php';

class EquipmentCest extends BaseCest
{
    protected $base_url = 'equipments';
    protected $resource_data = ['data' => [
      'type' => 'equipment',
      'attributes' => [
        'name' => 'Зажим',
        'description' => 'Зажим для лап',
        'id_tmc_type' => 1,
      ]
    ]];
    protected $resource_name = 'оборудование';
    protected $resource_required_fields = ['name'];
    protected $resource_all_fields = ['name', 'description', 'id_tmc_type'];
    protected $filter_field = 'name';
    protected $sort_field = 'name';
    protected $filter_field_value = 'Тестовое оборудование';
    protected $sort_field_value = 'Шприц';

    public function _fixtures()
    {
        return [
          'equipments' => [
            'class' => \app\tests\fixtures\EquipmentFixture::class,
          ]
        ];
    }

    public function tryExactlyFilter(ApiTester $i)
    {
        $this->authToken = $i->getAuthToken();
        $i->amBearerAuthenticated($this->authToken);
        $i->wantTo('Отфильтровать по полному совпадению id_tmc_type');

        $i->sendGET($this->base_url . '?filter[id_tmc_type]=1');
        $i->seeResponseCodeIs(200);
        $i->seeResponseContainsJson(['attributes' => ['id_tmc_type' => 1]]);
        $i->seeResponseJsonHasAttributes($this->resource_all_fields);
    }

    public function _trySingleResource($id = 1, ApiTester $i)
    {
        $i->wantTo('Получить ' . $this->resource_name . ' по ID');

        $i->sendGET($this->base_url . '/' . $id);
        $i->seeResponseCodeIs(200);
        $i->seeResponseJsonHasAttributes($this->resource_all_fields);
        $i->seeResponseContainsJson(['relationships' => ['tmc_type' => ['data' => ['id' => 1]]]]);
        $i->seeResponseJsonFitsScheme('jsonapi.json');
    }
}