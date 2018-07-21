<?php

namespace app\common\components;


use yii\di\Instance;
use yii\filters\auth\AuthMethod;

class JwtHttpBearerAuth extends AuthMethod
{
    /**
     * @var Jwt
     */
    public $jwt;

    /**
     * {@inheritdoc}
     * @throws \yii\base\InvalidConfigException
     */
    public function init()
    {
        parent::init();
        $this->jwt = Instance::ensure('jwt', Jwt::class);
    }


    /**
     * {@inheritdoc}
     * @throws \yii\base\InvalidConfigException
     */
    public function authenticate($user, $request, $response)
    {
        $authHeader = $request->getHeaders()->get('Authorization');
        if ($authHeader !== null && preg_match('/^Bearer\s+(.*?)$/', $authHeader, $matches)) {
            if (empty($matches[1])) {
                return null;
            }

            $token = $this->jwt->loadToken($matches[1]);
            if ($token === null) {
                return null;
            }

            return $user->loginByAccessToken($token, get_class($this));
        }

        return null;
    }

}