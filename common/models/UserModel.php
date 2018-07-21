<?php

namespace app\common\models;


use Lcobucci\JWT\Token;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * @property string $id [integer]
 * @property string $login [varchar(255)]
 * @property string $password [varchar(255)]
 * @property void $authKey
 */
class UserModel extends ActiveRecord implements IdentityInterface
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'users';
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        if (!$token instanceof Token) {
            return null;
        }

        $uid = $token->getClaim('uid');
        if (empty($uid)) {
            return null;
        }

        return static::findOne($uid);
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
    }

}