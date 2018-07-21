<?php

namespace app\modules\v1\models;
use app\common\components\entity\EntityInterface;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\web\BadRequestHttpException;
use yii\web\UploadedFile;

/**
 * Class FileResource
 * @package app\modules\v1\models
 *
 * @property string $hash
 * @property string $path
 * @@property string $name
 */
class CommentResource extends BaseResource
{
    protected $alias = 'comments';
    // public $excludedFields = ['id'];
    public $file;

    public function rules()
    {
        /*
            TODO 
                1. 'maxSize'=>1024 * 1024 * 2
                2. At this moment txt, doc, xls files not sending on globstage. If it need we can add extensions in validate rule
        */
        return [
            [['comment_user_id', 'comment_post_id', 'comment_content', 'comment_created_date', 'comment_updated_date'], 'required', 'on' => ['insert', 'update']],
            [['comment_content'], 'string', 'max' => 255],
            [['comment_user_id', 'comment_post_id'], 'integer'],
        ];


    }


    public static function tableName()
    {
        return 'comments';
    }


    public function getType()
    {
        return 'comment';
    }
}