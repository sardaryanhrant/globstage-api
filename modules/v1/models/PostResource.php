<?php

namespace app\modules\v1\models;

use app\modules\v1\models\FileResource;


use yii\base\Arrayable;

/**
 * Class PostResource
 * @package app\modules\v1\models
 *
 */
class PostResource extends BaseResource  
{
    protected $alias = 'posts';

    /**
     * @return array
     */
    public function rules()
    {

        /*
            TODO    
            Need to create foreign key with comments after comments migration
        */
        return [
            [['post_user_id', 'post_created_date', 'post_type', 'author_id'], 'required', 'on' => ['insert', 'update']],
            [['post_content', 'post_type', 'post_poll_title'], 'string'],
            [['post_user_id', 'post_like_count','post_comment_count','post_poll','post_community'], 'integer'],  
            // ['chat_id', 'exist', 'targetRelation' => 'chats'],       
        ];
    }


    public function getType()
    {
        return 'post';
    }


    public static function tableName()
    {
        return 'posts';
    }

    public function getAttachments()
    {
        return $this->hasOne(FileResource::className(), ['id' => 'post_attachments']);
    }

}