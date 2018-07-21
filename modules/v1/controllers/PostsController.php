<?php

namespace app\modules\v1\controllers;

use app\modules\v1\models\PostResource;
use app\modules\v1\models\FileResource;
use app\modules\v1\models\UserResource;
use yii\web\ServerErrorHttpException;
use yii\web\BadRequestHttpException;
use Yii;


class PostsController extends BaseController {

    public $modelClass = 'app\modules\v1\models\PostResource';
    public $excludedFields = ['id','author_id','post_user_id'];

    public function actionCreatepost()
    {

        /*
            TODO
            Check user existence before set "post_wall_id"
        */

        $request = Yii::$app->getRequest();
        $data = $request->getBodyParams();       

        $acceptableFields = ['post_user_id','post_type', 'post_created_date','post_like_count','post_like_users', 'post_attachments','post_content',
                                'post_community', 'post_poll','post_poll_title','post_poll_all_voted', 'post_comment_count', 'post_updated_date',
                                'post_wall_id', 'post_group_id', 'author_id'
                            ];

        if($this->checkauthuser()){
            $newPost = new PostResource();
            $request = Yii::$app->getRequest();
            $data = $request->getBodyParams();

            if(empty($data['post_wall_id'])){
                $data['post_wall_id'] = $this->checkauthuser();
            }

            foreach ($data as $key=>$value) {

                if($newPost->hasProperty($key) && in_array($key, $acceptableFields)){                     
                    $newPost->$key = $value;
                }
            }

            $newPost->post_user_id          = $this->checkauthuser();
            $newPost->post_created_date     = date('Y-m-d H:i:s');
            $newPost->post_updated_date     = date('Y-m-d H:i:s');
            $newPost->post_like_count       = 0;
            $newPost->post_like_users       = array();
            $newPost->post_comment_count    = 0;

            $newPost->save();
            return $newPost;
        }
    }



    public function actionGetpostsbywallid($id)
    {
        /*
            TODO
            Create relation with files via post_attachments json   // Now worked via php queries
        */
        if($this->checkauthuser()){
            $posts = PostResource::find()
            ->where(['post_wall_id'=>$id])
            ->all();

            $files = array();
            $postsWithAttachmens = array();

            foreach ($posts as $key=>$value) {
                if($key = 'post_attachments'){
                     foreach ($value[$key] as $v) {$file = FileResource::findOne($v); $files[] = $file;}
                }
		
                $postsWithAttachmens[] = ['post'=>$value, 'files'=>$files];

                $files = array();
            }

            return $postsWithAttachmens;
        }else{
            throw new UnauthorizedHttpException();
        }
    }



    // public function actionDeletepost($id){

    //     if($this->checkauthuser()){
    //         $post = PostResource::find()->where(['id'=>$id, 'post_user_id'=>$this->checkauthuser()])->one();
    //         if(!empty($post)){
    //             $post->delete();
    //             return ['status'=>'OK', 'message'=>'Post with id='.$id.' successfully deleted'];
    //         }else{
    //             throw new BadRequestHttpException("Post with id=".$id. " does not exist");
    //         }
    //     }else{
    //         throw new UnauthorizedHttpException();
    //     }
    // }



    public function actionGetnews()
    {

        /*
            NOTE
                At this moment we are getting news from posts where user id is not equal auth id and post_user_id is in friend's list
            TODO
                Need to get group posts by type=group if user is subscribed to that group 
        */

      if($this->checkauthuser()){
        $friendList = UserResource::findOne($this->checkauthuser())->user_friends;
        $frendIds = array();

        foreach ($friendList as $key => $value) {$frendIds[] = $key;}

        $news = PostResource::find()
        ->where('post_user_id !='.$this->checkauthuser())
        ->andWhere(['in', 'post_user_id', $frendIds])
        ->all();

        $files = array();
        $newsWithAttachmens = array();
        foreach ($news as $key=>$value) {
            if($key = 'post_attachments'){
                 foreach ($value[$key] as $v) {$file = FileResource::findOne($v); $files[] = $file;}
            }
            $newsWithAttachmens[] = ['post'=>$value, 'files'=>$files];
            $files = array();
        }
        return $newsWithAttachmens;
      } 

    }

    
}

