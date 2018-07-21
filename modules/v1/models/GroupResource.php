<?php

namespace app\modules\v1\models;


/**
 * Class GroupResource
 * @package app\modules\v1\models
 *
 */
class GroupResource extends BaseResource  
{
    protected $alias = 'groups';

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['group_name', 'group_author', 'group_created_date', 'group_updated_date'], 'required', 'on' => ['insert', 'update']],
            [['group_name', 'group_coords', 'group_description', 'group_address', 'group_website'], 'string'],
            [['group_author', 'group_background'], 'integer'],  
            // ['chat_id', 'exist', 'targetRelation' => 'chats'],       
        ];
    }


    public function getType()
    {
        return 'group';
    }


    public static function tableName()
    {
        return 'groups';
    }

}