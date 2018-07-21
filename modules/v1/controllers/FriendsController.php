<?php

namespace app\modules\v1\controllers;

use app\modules\v1\models\FriendResource;

class FriendsController extends ApiController {

	public $excludedFields = ['id','author_id'];
}