<?php

namespace app\modules\v1\controllers;

use app\modules\v1\models\CommentResource;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;

class CommentsController extends BaseController
{
    public $modelClass = 'app\modules\v1\models\CommentResource';
    public $excludedFields = ['id','author_id', 'comment_post_id', 'comment_user_id'];

    public function actionCreatecomment()
    {
        if($this->checkauthuser()){
            $request = \Yii::$app->getRequest();
            $data = $request->getBodyParams();
            $newComment = new CommentResource();


            $acceptableFields = ['comment_post_id', 'comment_content', 'author_id'];

            foreach ($data as $key=>$value) {
                if($newComment->hasProperty($key) && in_array($key, $acceptableFields)){
                    $newComment->$key = $value;
                }else{
                    throw new BadRequestHttpException("Requested Message Properties is not correct");
                }
            }

            $newComment->comment_user_id = $this->checkauthuser();
            $newComment->comment_created_date = date('Y-m-d H:i:s');
            $newComment->comment_updated_date = date('Y-m-d H:i:s');
            $newComment->author_id = $this->checkauthuser();
            $newComment->save();
            return $newComment;
        }       
        
    }



}
