<?php
require_once 'BaseCest.php';

class VaccineCest extends BaseCest
{
    protected $base_url = 'vaccines';
    protected $resource_data = ['data' => [
        'type' => 'vaccine',
        'attributes' => [
            'name' => 'Вакцина какая то',
            'form' => 'Описание',
            'id_tmc_type' => 1,
        ]
    ]];

    protected $resource_name = 'вакцина';
    protected $resource_required_fields = ['name', 'form'];
    protected $resource_all_fields = [
        'name', 'form', 'id_tmc_type', 'id_manufactured', 'id_representation', 'id_measure',
        'id_active_substance', 'id_active_substance_measure', 'id_file_packaging_image',
        'form_description', 'unit', 'active_substance_unit', 'excipients', 'packaging', 'basis'
    ];
    protected $filter_field = 'name';
    protected $sort_field = 'name';
    protected $filter_field_value = 'Вакцина 1';
    protected $sort_field_value = 'Вакцина 2';

    public function _fixtures()
    {
        return [
            'vaccines' => [
                'class' => \app\tests\fixtures\VaccineFixture::class,
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
