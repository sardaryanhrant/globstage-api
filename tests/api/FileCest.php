<?php


class FileCest
{
    private $fileResponse = '{
  "data": {
    "type": "file",
    "id": 1,
    "attributes": {
        "path": "/upload/file/a7/c9/80/a7c980e1e3210106b64ae6a856572434bf364b11.jpg",
        "name": "file1.jpg",
        "created": 1529339371
    }
}}';

    protected $failUploadResponse = '{
  "errors": [
    {   
      "code": "0",
      "status": "400",
      "title": "Bad Request",
      "detail": "Неподдерживаемый тип файла"
    }
  ]
}';

    private $authToken = null;

    public function _before(ApiTester $I)
    {
        $this->authToken = $I->getAuthToken();
        $I->amBearerAuthenticated($this->authToken);
        $I->haveHttpHeader('Content-Type', 'multipart/form-data');
        Yii::setAlias('@webroot', '@app/web');
    }

    public function _after(ApiTester $I)
    {
    }

    public function _fixtures()
    {
        return [
            'files' => \app\tests\fixtures\FileFixture::class
        ];
    }

    public function tryFileUpload(ApiTester $I)
    {
        copy(codecept_data_dir('upload.jpg'), codecept_data_dir('file1.jpg'));
        $I->sendPOST('files', [], [
            'file' => codecept_data_dir('file1.jpg')
        ]);

        $I->seeResponseCodeIs(200);
        $I->seeResponseJsonMatchesSchema($this->fileResponse);
    }

    public function tryFileUploadFail(ApiTester $I)
    {
        $I->sendPOST('files', [], [
            'file' => codecept_data_dir('fail_upload.xml')
        ]);

        $I->seeResponseCodeIs(400);
        $I->seeResponseJsonFitsScheme('jsonapi.json');

        //$I->seeResponseJsonMatchesSchema($this->failUploadResponse);
    }
}
