<?php

namespace app\modules\v1\controllers;

use app\common\components\Jwt;
use app\common\controllers\ApiController;
use app\modules\v1\models\TokenResource;
use app\modules\v1\models\UserResource;
use app\modules\v1\models\FriendResource;
use Lcobucci\JWT\Token;
use Yii;
use yii\di\Instance;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\UnauthorizedHttpException;
use app\common\components\JwtHttpBearerAuth;
use Lcobucci\JWT\Parser;

class UserController extends ApiController
{
    public $excludedFields = ['id','author_id'];

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator']['except'][] = 'token';
        $behaviors['authenticator']['except'][] = 'create';

        return $behaviors;
    }



    public function checkauthuser()
    {

        $headers =  Yii::$app->request->headers;
        $token = explode(' ',$headers['authorization'])[1];

        $auth = (new Parser())->parse((string) $token);

        $id = Yii::$app->user->identity->id;
        $authID = $auth->getClaim('uid');

        if($authID == $id){
            return $authID;
        }else{
            return false;
        }
    }



    public function actionToken()
    {
        $request = Yii::$app->getRequest();
        $data = $request->getBodyParams();

        /**
         * Input data validation
         */
        if (empty($data)) {
            throw new BadRequestHttpException();
        }

        if (empty($data['user_name'])) {
           // throw new BadRequestHttpException();
        }

        if (empty($data['user_password'])) {
            throw new BadRequestHttpException();
        }

        $hash =  \Yii::$app->getSecurity()->generatePasswordHash($data['user_password']);

        $user = UserResource::findOne(['user_email' => $data['user_name'] ]);
        if (empty($user)) {
            throw new UnauthorizedHttpException();
        }

        if (!Yii::$app->getSecurity()->validatePassword($data['user_password'], $user->user_password)) {
            throw new UnauthorizedHttpException();
        }

        /**
         * @var Jwt $jwt
         */
        $jwt = Instance::ensure('jwt', Jwt::class);
        /**
         * @var Token $token
         */
        $token = $jwt->createToken($user);

        $resource = new TokenResource();
        $resource->token = (string)$token;
        $resource->expired = $token->getClaim('exp', 0);
        $resource->setResourceRelationship('user', $user);

        return ['user'=>$user, 'auth'=>$resource];
    }



    public function actionView($id)
    {
        if (!empty($id)) {
            $resource = UserResource::findOne($id);

            if (!empty($resource)) {
                return $resource;
            }
        }

        throw new NotFoundHttpException();
    }



    public function actionCreate()
    {

        $request = Yii::$app->getRequest();
        $data = $request->getBodyParams();
      
        if (empty($data['user_email'])) {
            throw new BadRequestHttpException();
        }
        $hash =  \Yii::$app->getSecurity()->generatePasswordHash($data['user_password']);        

        $user = new UserResource();
        $user->user_name = $data['user_first_name'];
        $user->user_email = $data['user_email'];
        $user->user_last_name = $data['user_last_name'];
        $user->user_password = $hash;
        $user->save();

        $newUser = UserResource::findOne($user->id);  


        if (!Yii::$app->getSecurity()->validatePassword($data['user_password'], $user->user_password)) {
            throw new UnauthorizedHttpException();
        }

        /**
         * @var Jwt $jwt
         */
        $jwt = Instance::ensure('jwt', Jwt::class);
        /**
         * @var Token $token
         */
        $token = $jwt->createToken($user);

        $resource = new TokenResource();
        $resource->token = (string)$token;
        $resource->expired = $token->getClaim('exp', 0);
        $resource->setResourceRelationship('user', $user);

        unset($newUser['user_password']);

        return ['user'=>$newUser, 'auth'=>$resource];
    }



    public function actionGetfriends($id)
    {
        $friends  = FriendResource::find()->where(['user_id'=>$id])->all();
        // print_r($friends);
        return $friends;
    }



    public function actionUpdateuser()
    {
        $acceptableFields = ['user_name','user_last_name', 'user_photo','user_first_name',
                            'user_location', 'user_date_of_birth','user_gender','author_id',
                            'user_marital_status', 'user_password','user_country','user_city', 'user_active', 'user_speed'
                            ];

        if($this->checkauthuser()){
            $authUser = UserResource::find()->where(['id'=> $this->checkauthuser()])->one();
            $request = Yii::$app->getRequest();
            $data = $request->getBodyParams();

            foreach ($data as $key=>$value) {

                if($authUser->hasProperty($key) && in_array($key, $acceptableFields)){ 
                    if($key == 'user_password'){
                        $authUser->user_password = \Yii::$app->getSecurity()->generatePasswordHash($value);
                    }else{
                        $authUser->$key = $value;
                    }
                }
            }
            $authUser->update();
            return $authUser;
        }else{
            throw new BadRequestHttpException("You hav not permission to change user account with id=".$id);
        }
    }



    public function actionAddfriend($id)
    {

        $newFriend = UserResource::findOne($id);
        if(!empty($newFriend) && $newFriend->id != $this->checkauthuser()){
            $me = UserResource::findOne($this->checkauthuser());
            $friendsList = $me->user_friends;
            if(!array_key_exists($id, $friendsList)){$friendsList[$newFriend->id][] = $newFriend->user_email;}            
            $me->user_friends = $friendsList;
            $me->update();
            return ['status'=>'OK', 'message'=>'User added to your friends list'];
        }elseif(empty($newFriend)){
            throw new BadRequestHttpException("User with id=".$id." does not exist in our DB");
        }else{
            throw new BadRequestHttpException("You can not add yourself to friends list :)");
        }
    }



    public function actionDeletefriend($id)
    {
        $me = UserResource::findOne($this->checkauthuser());
        $friendsList = $me->user_friends;
        if(array_key_exists($id, $friendsList)){
            unset($friendsList[$id]);
            $me->user_friends = $friendsList;
            $me->update();
            return  ['status'=>'OK', 'message'=>'User id='.$id.' deleted from your friends list'];
        }else{
            throw new BadRequestHttpException("Friend with id ".$id." does not exist in your friends list");
        }
            
    }



    public function actionAddchatlist($id)
    {
        $me = UserResource::findOne($this->checkauthuser());
        $user = UserResource::findOne($id);
        if($id != $this->checkauthuser() && !empty($user)){
            $chatList = $me->user_chat_list;

            if(!in_array($id, $chatList)){
                $chatList[] = $id;
                $me->user_chat_list = $chatList;
                $me->update();
                return ['status'=>'OK', 'message'=>'User added in your chat list'];
            }else{
                return ['message'=>'User is in your chatlist'];
            }
        }elseif(empty($user)){
            throw new BadRequestHttpException("User with id=".$id." does not exist in our DB");
        }else{
            throw new BadRequestHttpException("You cannot add yourself in your chatlist");
        }
    }



    public function actionGetchatlist()
    {
        if($this->checkauthuser()){
            $me = UserResource::findOne($this->checkauthuser());
            $chatlist = $me->user_chat_list;

            if(!empty($chatlist)){
                $userChatList = UserResource::find()
                ->where(['id'=>$chatlist['chatList']])
                ->select('id, user_name, user_last_name, user_photo')
                ->all();

                $userNewChatList = array();
                foreach ($userChatList as $value) {
                    $userNewChatList[] =['id' => $value->id, 'displayName' => $value->user_name . ' ' . $value->user_last_name, 'avatar' => $value->user_photo, 'status'=>0];
                }
                return $userNewChatList;
            }else{
                return [];
            }
        }else{
           throw new BadRequestHttpException("Permission defined to other chatlist"); 
        }
    }



    public function actionRemoveuserfromchatlist()
    {
        if($this->checkauthuser()){
            $request = Yii::$app->getRequest();
            $data = $request->getBodyParams();

            $me = UserResource::findOne($this->checkauthuser());

            $chatlist = $me->user_chat_list;

            if (($key = array_search($data['id'], $chatlist['chatList'])) !== false) {
                array_splice($chatlist['chatList'], $key, 1);
            }  

            $me->user_chat_list =  $chatlist; 
            $me->update();
            return $chatlist;
        }else{
           throw new BadRequestHttpException("Permission defined to other chatlist"); 
        }
    }


    
   public function actionAdduserblacklist()
   {
        $request = Yii::$app->getRequest();
        $data = $request->getBodyParams();

        $user_id = $data['user_id'];

        if($this->checkauthuser()){
            $me = UserResource::findOne($this->checkauthuser());

            $user = UserResource::findOne($user_id);
            $blacklist = $me->user_blacklist;
            if(!in_array($user_id, $blacklist) && !empty($user) && $user_id != $this->checkauthuser()){
                $blacklist[] = $user_id;
                $me->user_blacklist = $blacklist;
                $me->update();
                return ['status'=>'OK', 'message'=>'User added in you blacklist'];
            }elseif(empty($user)){
                throw new BadRequestHttpException("User with id=".$user_id." does not exist in our DB");
            }elseif($user_id == $this->checkauthuser()){
                return ['message'=>'You cannot add yourself to your blacklist :)'];                
            }elseif(in_array($user_id, $blacklist)){
                return ['message'=>'User is in your blacklist'];
            }
        }else{
           throw new BadRequestHttpException("Permission defined to other chatlist"); 
        }
    }



    public function actionUpdatecontact()
    {
        if($this->checkauthuser()){
            $request = Yii::$app->getRequest();
            $data = $request->getBodyParams();

            $acceptableFields = ['user_mobile', 'user_twitter', 'user_facebook', 'user_website', 'user_skype'];

            $me = UserResource::findOne($this->checkauthuser());

            $userContact = $me->user_contact;
            
            foreach ($data as $key => $value) {
                if(in_array($key, $acceptableFields)){
                    $userContact[$key] = $value;
                }
            }            
            $me->user_contact = $userContact;
            $me->update();
            return $me->user_contact;
        }
    } 



    public function actionUpdatepersonalinfo()
    {
        if($this->checkauthuser()){
            $request = Yii::$app->getRequest();
            $data = $request->getBodyParams();

            $acceptableFields = ['activities', 'interests', 'favorite_munshids', 'favorite_preachers', 'favorite_books', 'favorite_sports', 'favorite_quotes', 'about_me'];

            $me = UserResource::findOne($this->checkauthuser());
            $userInterests = $me->user_interests;            
            foreach ($data as $key => $value) {
                if(in_array($key, $acceptableFields)){
                    $userInterests[$key] = $value;
                }
            }            
            $me->user_interests = $userInterests;
            $me->update();
            return $userInterests;
        }
    }    
   

}