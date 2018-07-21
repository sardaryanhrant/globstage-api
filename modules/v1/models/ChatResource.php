<?php

namespace app\modules\v1\models;

use yii\base\Arrayable;

/**
 * Class ChatResource
 * @package app\modules\v1\models
 */
class ChatResource extends BaseResource  
{
    protected $alias = 'chats';

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['from_id', 'for_id'], 'required', 'on' => ['insert', 'update']],
            [['from_id', 'for_id'], 'integer'],      
        ];
    }


    public function getType()
    {
        return 'chat';
    }


    public static function tableName()
    {
        return 'chats';
    }
}