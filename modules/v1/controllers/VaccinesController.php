<?php

namespace app\modules\v1\controllers;

use app\modules\v1\models\UserResource;

class VaccinesController extends BaseController {

    public $modelClass = 'app\modules\v1\models\VaccineResource';

    public function actionViewusers($id){

        $resource = UserResource::findOne($id);

        return $resource;
    }
}