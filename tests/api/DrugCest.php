<?php
require_once 'BaseCest.php';

class DrugCest extends BaseCest
{
    protected $base_url = 'drugs';
    protected $resource_data = ['data' => [
        'type' => 'drug',
        'attributes' => [
            'name' => 'Название препарата...',
            'id_tmc_type'=>1,
            'id_registered' => 1,
            'id_manufactured'=>1,
            'id_representation'=>1,
            'form'=>'Лекарственная',
            'form_description'=>'Описание лекарственной формы',
            'unit'=>'Unit 1',
            'id_measure'=>1,
            'id_active_substance'=>1,
            'active_substance_unit'=>'active_substance_unit 1',
            'id_active_substance_measure'=>1,
            'excipients'=>'Вспомогательные вещества.',
            'packaging'=>'Описание упаковки',
            'id_file_packaging_image'=>1,
            'basis'=>'Основание описания препарата.'
        ]
    ]];
    protected $resource_name = 'drug';
    protected $resource_required_fields = ['name'];
    protected $resource_all_fields = [
        'name','id_tmc_type','id_manufactured','id_representation', 'form','form_description',
        'unit', 'id_measure', 'id_active_substance','active_substance_unit','id_active_substance_measure',
        'excipients', 'packaging','id_file_packaging_image', 'basis'
    ];
    protected $filter_field = 'name';
    protected $sort_field = 'name';
    protected $filter_field_value = 'Название препарата';
    protected $sort_field_value = 'Название препарата 2';

    public function _fixtures()
    {
        return [
            'drugs' => [
                'class' => \app\tests\fixtures\DrugFixture::class,
            ]
        ];
    }



    public function tryExactlyFilter(ApiTester $i)
    {
        $this->authToken = $i->getAuthToken();
        $i->amBearerAuthenticated($this->authToken);
        $i->wantTo('Отфильтровать по полному совпадению id_manufactured');

        $i->sendGET($this->base_url . '?filter[id_manufactured]=1');
        $i->seeResponseCodeIs(200);
        $i->seeResponseContainsJson(['attributes' => ['id_manufactured' => 1]]);
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