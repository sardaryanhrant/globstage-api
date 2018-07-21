<?php


class UserCest
{
    private $tokenResponse = '{
  "data": {
    "type": "token",
    "id": null,
    "attributes": {
      "token": "eyJ0eXAiOiJKV1QiLCJ...hbGc",
      "expired": 1527051205
    },
    "relationships": {
       "user" : { "data": {"type": "user", "id": 15} }
    }
}}';

    private $userResponse = '{
  "data": {
    "type": "user",
    "id": 15,
    "attributes": {
      "login": "Mr. Smith"
    }
}}';

    private $badPasswordResponse = '
{
  "errors": [
    {   
      "code": "101",
      "source": { "pointer": "/data/attributes/login" },
      "title": "Неверное имя пользователя или пароль",
      "detail": ""
    }
  ]
}';

    private $authToken = null;

    public function _before(ApiTester $I)
    {
        $I->amBearerAuthenticated($this->authToken);
    }

    public function _after(ApiTester $I)
    {
    }

    public function _fixtures()
    {
        return [
            'users' => \app\tests\fixtures\UserFixture::class
        ];
    }

    // tests
    public function trySuccessAuth(ApiTester $i)
    {
        $i->wantTo('Аутентификация пользователя');
        $i->sendPOST('users/token', [
            'data' => [
                'type' => 'user',
                'attributes' => [
                    'login' => USER_LOGIN, 'password' => USER_PASSWORD
                ]
            ]
        ]);
        $i->seeResponseJsonMatchesSchema($this->tokenResponse);
        $i->seeResponseCodeIs(200);

        $resp = json_decode($i->grabResponse(), true);
        $this->authToken = $resp['data']['attributes']['token'];
    }

    public function tryFailsAuth(ApiTester $i, $scen)
    {
        $scen->skip('Документация врет');
        $i->wantTo('Неверный пароль');
        $i->sendPOST('users/token', [
            'data' => [
                'type' => 'user',
                'attributes' => [
                    'login' => USER_LOGIN, 'password' => 'badpassword'
                ]
            ]
        ]);
        $i->seeResponseJsonMatchesSchema($this->badPasswordResponse);
        $i->seeResponseCodeIs(409);
    }

    public function trySuccessUserGet(ApiTester $i)
    {
        $i->wantTo('Получить пользователя');
        $i->sendGET('users/1');
        $i->seeResponseJsonMatchesSchema($this->userResponse);
        $i->seeResponseCodeIs(200);
    }
}
