<?php
namespace app\modules\v1\controllers;

use app\common\components\JwtHttpBearerAuth;
use app\common\components\LegacyApiQuery;
use app\modules\v1\models\EntityResource;
use yii\base\InvalidConfigException;
use yii\data\ActiveDataProvider;
use yii\filters\Cors;
use yii\rest\IndexAction;
use yii\web\Response;
use yii\rest\ActiveController;
use Lcobucci\JWT\Parser;
use yii\web\BadRequestHttpException;


class BaseController extends ActiveController
{

    protected function verbs()
    {
        return [
            'index'  => ['GET', 'HEAD'],
            'view'   => ['GET', 'HEAD'],
            'create' => ['POST'],
            'update' => ['PUT', 'PATCH'],
            'delete' => ['DELETE'],
        ];
    }


    public function checkauthuser()
    {

        $headers =  \Yii::$app->request->headers;
        $token = explode(' ',$headers['authorization'])[1];

        $auth = (new Parser())->parse((string) $token);

        $authID = $auth->getClaim('uid');

        if($authID){
            return $authID;
        }else{
            return false;
        }
    }
    

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['update']);

        $actions['index'] = [
            'class' => 'yii\rest\IndexAction',
            'modelClass' => $this->modelClass,
//            'dataFilter' => [
//                'class' => 'yii\data\ActiveDataFilter',
//                'searchModel' => $this->modelClass
//            ],
            'prepareDataProvider' => function(IndexAction $action, $filter) {
                return $this->prepareDataProvider($action, null);
            }
        ];

        return $actions;
    }

    public function actionUpdate($id)
    {
        $request = \Yii::$app->getRequest();
        $data = $request->getBodyParams();
        $resource = $this->modelClass::findOne($id);
        if(!empty($resource)){
            if($this->checkauthuser() == $resource->author_id){
                $request = \Yii::$app->getRequest();
                $data = $request->getBodyParams();
                $resource = $this->modelClass::findOne($id);

                $acceptableFields = array();
                foreach ($resource as $key => $value) {if(!in_array( $key, $this->excludedFields) ){$acceptableFields[] = $key; }}

                foreach ($data as $key=>$value) {
                    if($resource->hasProperty($key)){if(in_array($key, $acceptableFields)){$resource->$key = $value; } }
                    else{throw new BadRequestHttpException("Requested Message Properties is not correct"); }
                }
                $resource->update();
                return $resource;            
            }else{ throw new BadRequestHttpException("Permission denied"); }
        }else{ throw new BadRequestHttpException("Object not found"); }
    }



    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['cors'] = [
            'class' => Cors::class,
        ];

        $behaviors['authenticator'] = [
            'class'  => JwtHttpBearerAuth::class,
            'except' => ['options'],
        ];

        $behaviors['contentNegotiator']['formats'] = [
            'application/json'         => Response::FORMAT_JSON,
            'application/vnd.api+json' => Response::FORMAT_JSON,
        ];

        return $behaviors;
    }

    public function prepareDataProvider(IndexAction $action, $filter) {
        /* @var $modelClass \yii\db\BaseActiveRecord */
        $modelClass = $this->modelClass;
        $query = $modelClass::find();
        $requestParams = \Yii::$app->getRequest()->getQueryParams();

        if ($filter = \Yii::$app->getRequest()->getQueryParam('filter')) {
            foreach ($filter as $key => $value) {
                $schema = $modelClass::getTableSchema();
                $column = $schema->getColumn($key);

                if($column->enumValues){
                    $query->andWhere(['in', $key, $value]);
                    continue;
                }
                if ($column->type == 'integer')
                    $query->andWhere(['=', $key, $value]);
                else
                    $query->andWhere(['ilike', $key, $value]);
            }
        }

        $dataProvider = null;

        try {
            LegacyApiQuery::$queryParams = $requestParams;

            $config = [
                'class' => ActiveDataProvider::class,
                'query' => $query,
                'pagination' => [
                    'class' => 'app\common\components\Pagination',
                    'forcePageParam' => false,
                    'defaultPageSize' => LegacyApiQuery::getPaginationLimit(),
                    'page' => LegacyApiQuery::getPaginationOffset()
                ],
            ];

            if (LegacyApiQuery::getSortField()) {
                $config['sort'] = [
                    'defaultOrder' => [
                        LegacyApiQuery::getSortField() => LegacyApiQuery::getSortDirection()
                    ]
                ];
            } else {
                $config['sort'] = [
                    'defaultOrder' => [
                        'id' => SORT_DESC
                    ]
                ];
            }

            $dataProvider = \Yii::createObject($config);
        } catch (InvalidConfigException $e) {
        }

        return $dataProvider;
    }
}