<?php

namespace app\modules\v1\controllers;

use app\modules\v1\models\MessageResource;
use app\modules\v1\models\UserResource;
use app\modules\v1\models\ChatResource;
use yii\web\ServerErrorHttpException;
use yii\web\BadRequestHttpException;
use Yii;


class MessagesController extends BaseController {

    public $modelClass = 'app\modules\v1\models\MessageResource';
    public $excludedFields = ['id','author_id','for_id','from_id'];

    public function actionCreatemessage(){

        // TODO check if both users exists in db

    	$request = Yii::$app->getRequest();
        $data = $request->getBodyParams();
        $newMessage = new MessageResource();
        $acceptableFields = ['from_id','for_id','content','attachment_id','attachment_src','chat_id','author_id'];
        

        if($this->checkauthuser()){   

        	$chat = ChatResource::find()
        	->orWhere([
        		'from_id'=>$this->checkauthuser(), 'for_id'=>$data['for_id']
        	])
        	->orWhere([
        		'from_id'=>$data['for_id'], 'for_id'=>$this->checkauthuser()
        	])->one();

        	$chat_id;

        	if(!empty($chat)){
        		$chat_id = $chat->id;
        	}else{
        		$newChat = new ChatResource();
        		$newChat->from_id = $this->checkauthuser();
        		$newChat->for_id  = $data['for_id'];
        		$newChat->save();
        		$chat_id = $newChat->id;
        	}
        	
        	$data['chat_id'] = $chat_id;
            $data['from_id'] = $this->checkauthuser();

            foreach ($data as $key=>$value) {
                if($newMessage->hasProperty($key) && in_array($key, $acceptableFields)){
                    $newMessage->$key = $value;
                }else{
                    throw new BadRequestHttpException("Requested Message Properties is not correct");
                }
            }
            $newMessage->author_id = $this->checkauthuser();
            $newMessage->save();

            // Adding User to Auth User Chat list
            $me = UserResource::findOne($this->checkauthuser());
            $chatList = $me->user_chat_list;

            /*
                TODO
                    Refactor migration => set user_chat_list default value {"chatList":[]} instead of []
            */

            if(!in_array($data['for_id'], $chatList['chatList']) && $this->checkauthuser() != $data['for_id']){
                $chatList['chatList'][] = $data['for_id'];
                $me->user_chat_list     = $chatList;
                $me->update();
            }
            
            return $newMessage;
        }else{
            throw new UnauthorizedHttpException();
        }
    }



    // public function actionDeletemessage(){
    //     if($this->checkauthuser()){   
    //        $request = Yii::$app->getRequest();
    //         $data = $request->getBodyParams(); 

    //         $message = MessageResource::find()->where(['id'=>$data['id']])->andWhere(['from_id'=>$this->checkauthuser()])->one();
    //         if(!empty($message)){
    //             $message->delete();
    //             return ['status'=>'OK', 'message'=>'Message succesfully deleted'];
    //         }else{
    //            throw new BadRequestHttpException("You have not access to delete others messages"); 
    //         }
    //     }
    // }



    public function actionGetmessagebyuserid($id){
        // TODO Set Limit messages to 100
         
        if($this->checkauthuser()){
            $chat = ChatResource::find()
            ->orWhere(['from_id'=>$id, 'for_id' => $this->checkauthuser()])
            ->orWhere(['for_id' =>$id, 'from_id' => $this->checkauthuser()])
            ->one();

            $chatId = $chat->id;
            $chatmessages = MessageResource::find()
                          ->where(['chat_id'=>$chatId])
                          ->all();

            return $chatmessages;
        }
    }


    
}

