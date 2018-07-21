<?php

namespace app\modules\v1\models;

use yii\base\Arrayable;


class FriendResource extends BaseResource 
{

    protected $alias = 'friends';
    protected $relationships = ['user' => 'user'];

    public function getType()
    {
        return 'friend';
    }

    public static function tableName()
    {
        return 'friends';
    }

    public function rules()
    {
        return [
            [['user_id','friend_id'], 'required', 'on' => 'insert'],
            [['subscription'], 'string'], 
            ['user_id', 'exist', 'targetRelation' => 'user'],             
            // ['tmc_class', 'in', 'range' => ['drug', 'equipment', 'vaccine']]
        ];
    }  


    public function extraFields()
    {
        return ['user'];
    }

        /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasMany( UserResource::class, ['id' => 'user_id']);
    }







}