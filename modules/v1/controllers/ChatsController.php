<?php

namespace app\modules\v1\controllers;

use app\modules\v1\models\ChatResource;
use yii\web\ServerErrorHttpException;
use yii\web\BadRequestHttpException;

class ChatsController extends BaseController {

    public $modelClass = 'app\modules\v1\models\ChatResource'; 
    public $excludedFields = ['id','author_id'];
    
}

