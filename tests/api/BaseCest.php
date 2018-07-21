<?php

abstract class BaseCest
{
    protected $authToken = null;

    protected $base_url = '';
    protected $resource_data = [];
    protected $resource_name = "";

    protected $resource_required_fields = [];
    protected $resource_all_fields = [];

    protected $filter_field;
    protected $sort_field;

    protected $overflowField;

    protected $sort_field_value;
    protected $filter_field_value;

    protected $error409response = '{
  "errors": [
    {   
      "code": "201",
      "source": { "pointer": "" },
      "title": "",
      "detail": ""
    }
  ]
}';

    protected $error404response = '{
  "errors": [
    {   
      "title": "",
      "detail": ""
    }
  ]
}';

    protected $error422response = '{
  "errors": [
    {   
      "code": "202",
      "title": "",
      "detail": ""
    }
  ]
}';

    public function _before(ApiTester $I)
    {
        $I->amBearerAuthenticated($this->authToken);
    }

    public function tryGetResource(ApiTester $i)
    {
        $this->authToken = $i->getAuthToken();
        $i->amBearerAuthenticated($this->authToken);

        $this->_trySingleResource(1, $i);
        $this->_tryLostResource($i);

        $this->_tryResourceList($i);
        $this->_tryFilter($i);
        $this->_trySort($i);
        $this->_tryPagination($i);
    }

    public function tryCreateResource(ApiTester $i)
    {
        $this->authToken = $i->getAuthToken();
        $i->amBearerAuthenticated($this->authToken);
        $i->wantTo('Создать новое ' . $this->resource_name);

        $i->sendPOST($this->base_url, $this->resource_data);
        $i->seeResponseCodeIs(201);
        $i->seeResponseJsonHasAttributes($this->resource_required_fields);
        $i->seeResponseJsonFitsScheme('jsonapi.json');

        $this->_tryCreateOverflow($i);
        $this->_tryCreateDuplicate($i);
        $this->_tryCreateMissedField($i);
    }

    public function tryUpdateResource(ApiTester $i)
    {
        $i->wantTo('Обновить существующий ' . $this->resource_name);

        $this->resource_data['data']['attributes'][$this->sort_field] = 'new data';
        $i->sendPUT($this->base_url . '/1', $this->resource_data);
        $i->seeResponseCodeIs(200);
        $i->seeResponseJsonHasAttributes($this->resource_required_fields);

        $value = $i->grabDataFromResponseByJsonPath('$.data.attributes.' . $this->sort_field);
        $i->assertEquals('new data', $value[0]);
        $i->seeResponseJsonFitsScheme('jsonapi.json');


        $this->_tryUpdateNotFound($i);
    }

    public function tryDeleteResource(ApiTester $i)
    {
        $i->wantTo('Удалить существующий ' . $this->resource_name);

        $i->sendDELETE($this->base_url . '/2');
        $i->seeResponseCodeIs(204);
        $i->sendGET($this->base_url . '/2');
        $i->seeResponseCodeIs(404);
    }

    public function _trySingleResource($id = 1, ApiTester $i)
    {
        $i->wantTo('Получить ' . $this->resource_name . ' по ID');

        $i->sendGET($this->base_url . '/' . $id);
        $i->seeResponseCodeIs(200);
        $i->seeResponseJsonHasAttributes($this->resource_all_fields);
        $i->seeResponseJsonFitsScheme('jsonapi.json');
    }

    public function _tryLostResource(ApiTester $i)
    {
        $i->wantTo('Получить несуществующий ' . $this->resource_name);

        $i->sendGET($this->base_url . '/1000000');
        $i->seeResponseCodeIs(404);
        $i->seeResponseJsonMatchesSchema($this->error404response);
        $i->seeResponseJsonFitsScheme('jsonapi.json');
    }

    public function _tryResourceList(ApiTester $i)
    {
        $i->wantTo('Получить список ' . $this->resource_name);

        $i->sendGET($this->base_url);
        $i->seeResponseCodeIs(200);
        $i->seeResponseJsonFitsScheme('jsonapi.json');
        $i->seeResponseJsonHasAttributes($this->resource_all_fields);

    }

    public function _tryFilter(ApiTester $i)
    {
        $i->wantTo('Отфильтровать по полю ' . $this->filter_field);

        $i->sendGET($this->base_url . '?filter[' . $this->filter_field . ']=' . $this->filter_field_value);
        $i->seeResponseCodeIs(200);
        $i->seeResponseContainsJson(['attributes' => [$this->filter_field => $this->filter_field_value]]);
        $i->seeResponseJsonHasAttributes($this->resource_all_fields);
    }

    public function _trySort(ApiTester $i)
    {
        $i->wantTo('Сортировка по ' . $this->sort_field);

        $i->sendGET($this->base_url . '?sort=-' . $this->sort_field);
        $i->seeResponseCodeIs(200);
        $name = $i->grabDataFromResponseByJsonPath('$.data[0].attributes.' . $this->sort_field);
        $i->assertEquals($this->sort_field_value, $name[0]);
        $i->seeResponseJsonFitsScheme('jsonapi.json');
        $i->seeResponseJsonHasAttributes($this->resource_all_fields);
    }

    public function _tryPagination(ApiTester $i)
    {
        $i->wantTo('Пагинация');

        $i->sendGET($this->base_url . '?sort=-' . $this->sort_field . '&page[offset]=1&page[limit]=1');
        $i->seeResponseCodeIs(200);
        $name = $i->grabDataFromResponseByJsonPath('$.data[0].attributes.' . $this->sort_field);
        $i->assertEquals($this->filter_field_value, $name[0]);
        $i->seeResponseJsonHasAttributes($this->resource_all_fields);
    }

    public function _tryCreateMissedField(ApiTester $i)
    {
        $i->wantTo('Создать новый ' . $this->resource_name . ' без обязательного поля');

        foreach ($this->resource_required_fields as $fieldname) {
            $post_data =$this->resource_data;
            unset($post_data['data']['attributes'][$fieldname]);
            $i->sendPOST($this->base_url, $post_data);
            $i->seeResponseCodeIs(422);
            $i->seeResponseJsonFitsScheme('jsonapi.json');
        }
    }

    public function _tryCreateDuplicate(ApiTester $i)
    {
        $i->wantTo('Создать ' . $this->resource_name . ' с существующим полем');
        $i->sendPOST($this->base_url, $this->resource_data);
        $i->seeResponseCodeIs(422);
        $i->seeResponseJsonFitsScheme('jsonapi.json');
//        $i->seeResponseJsonMatchesSchema($this->error409response);
    }

    public function _tryCreateOverflow(ApiTester $i)
    {
        if ($this->overflowField) {
            $i->wantTo('Создать ' . $this->resource_name . ' с превышением');
            $i->sendPOST($this->base_url, $this->overflowField);
            $i->seeResponseCodeIs(422);
            $i->seeResponseJsonFitsScheme('jsonapi.json');
        }
//        $i->seeResponseJsonMatchesSchema($this->error409response);
    }

    public function _tryUpdateNotFound(ApiTester $i)
    {
        $i->wantTo('Обновить несуществующий ' . $this->resource_name);

        $i->sendPUT($this->base_url . '/10000', $this->resource_data);
        $i->seeResponseCodeIs(404);
        $i->seeResponseJsonFitsScheme('jsonapi.json');
        $i->seeResponseJsonMatchesSchema($this->error404response);
    }

    public function _tryDeleteNotFound($fieldName, ApiTester $i)
    {

    }
}