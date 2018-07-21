<?php

namespace app\modules\v1\controllers;

use app\modules\v1\models\PostResource;
use app\modules\v1\models\FileResource;
use app\modules\v1\models\UserResource;
use app\modules\v1\models\GroupResource;
use yii\web\ServerErrorHttpException;
use yii\web\BadRequestHttpException;
use Yii;


class GroupsController extends BaseController {

    public $modelClass = 'app\modules\v1\models\GroupResource';
    public $excludedFields = ['id','author_id'];

    public function actionCreategroup()
    {

        $request = Yii::$app->getRequest();
        $data = $request->getBodyParams(); 

        $acceptableFields = [
        'group_name','group_author', 'group_coords','group_followers','group_created_date', 'group_updated_date','group_videos', 'author_id',
        'group_audios', 'group_description','group_address','group_website', 'group_privacy', 'group_background', 'post_wall_id', 'post_group_id'
        ];

        if($this->checkauthuser()){
            $group = new GroupResource();
            $request = Yii::$app->getRequest();
            $data = $request->getBodyParams();

            foreach ($data as $key=>$value) {
                if($group->hasProperty($key) && in_array($key, $acceptableFields)){                     
                    $group->$key = $value;
                }
            }

            $group->group_author       = $this->checkauthuser();
            $group->group_created_date = date('Y-m-d H:i:s');
            $group->group_updated_date = date('Y-m-d H:i:s');
            $group->author_id = $this->checkauthuser();

            $group->save();
            return $group;
        }else{
             throw new UnauthorizedHttpException();
        }
    }


    // public function actionUpdategroup()
    // {
    //     if($this->checkauthuser()){
    //         $request = Yii::$app->getRequest();
    //         $data = $request->getBodyParams();
    //         $group = GroupResource::find()->where(['id'=>$data['group_id'], 'group_author'=>$this->checkauthuser()])->one();

    //         if(!empty($group)){
    //              $acceptableFields = [
    //             'group_name','group_author', 'group_coords','group_followers','group_created_date', 'group_updated_date','group_videos',
    //             'group_audios', 'group_description','group_address','group_website', 'group_privacy', 'group_background', 'post_wall_id', 'post_group_id'
    //             ];

    //             foreach ($data as $key=>$value) {
    //                 if($group->hasProperty($key) && in_array($key, $acceptableFields)){                     
    //                     $group->$key = $value;
    //                 }
    //             }
    //             $group->update();
    //             return $group;
    //         }else{
    //             throw new BadRequestHttpException("You have not access to update this group !");
    //         }

    //     }
    // }



    public function actionGetowngroups()
    {
        if($this->checkauthuser()){
            $groups = GroupResource::find()
            ->where(['group_author'=>$this->checkauthuser()])
            ->all();

            return $groups;
        }
    }

    public function actionDeletegroup($id){
        if($this->checkauthuser()){
            $group = GroupResource::find()
            ->where(['id'=>$id])
            ->andWhere(['group_author'=>$this->checkauthuser()])
            ->one();
            if(!empty($group)){
                $group->delete();
                return ['status'=>'OK', 'message'=>'Group with id='.$id. ' successfully deleted'];    
            }else{
                throw new BadRequestHttpException("You have not access to delete this group !"); 
            }

        }else{
             throw new UnauthorizedHttpException();
        }

    }

    
}

