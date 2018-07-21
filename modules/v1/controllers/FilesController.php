<?php

namespace app\modules\v1\controllers;

use app\common\components\FileService;
use app\common\controllers\ApiController;
use app\modules\v1\models\FileResource;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;
use Lcobucci\JWT\Parser;


class FilesController extends BaseController
{
    public $modelClass = 'app\modules\v1\models\FileResource';
    public $excludedFields = ['id','author_id'];

    public function behaviors()
    {
        return [
            'corsFilter' => [
                'class' => \yii\filters\Cors::className(),
                  'cors' => [
                    'Origin' => ['*'],
                    'Access-Control-Request-Method' => ['POST'],
                    'Access-Control-Allow-Credentials' => true,
                    'Access-Control-Max-Age' => 3600,
                ],
            ],
        ];
    }



    public function actionUpload()
    {
        /*
            TODO Smiles. Need to have fixed smile icons on server 

            Need to find out how we must save message if exist attached file 
                1. upload file then save message 
                2. to save it at once
        */


        $model = new FileResource();
        $model->file = UploadedFile::getInstanceByName( 'file');
        $root = glob($_SERVER["DOCUMENT_ROOT"])[0];
        $filepath;
        
        if ($model->file && $model->validate()) {
            $extension = $model->file->extension;
            switch ($extension) {
                case 'jpg':
                $folder = 'images'; 
                $type = 'image';                      
                    break;
                case 'png':
                $folder = 'images';
                $type = 'image';                         
                    break;
                case 'mp3':
                $folder = 'audios'; 
                $type = 'audio';                      
                    break;
                case 'mp4':
                $folder = 'videos';
                $type = 'video';                                         
                    break;
                case 'xls':
                $folder = 'docs';
                $type = 'xls';                                         
                    break;
                case 'pdf':
                $folder = 'docs';
                $type = 'pdf';                                         
                    break;
                case 'doc':
                $folder = 'docs';
                $type = 'doc';                                         
                    break;
                case 'txt':
                $folder = 'docs';
                $type = 'txt';                                         
                    break;
            }

            $filepath = $root.'/upload/'.$folder.'/'.date('Y').'/'.date('m');
                if(!file_exists($filepath)){
                   mkdir($root.'/upload/'.$folder.'/', 0777);
                   mkdir($root.'/upload/'.$folder.'/'.date('Y'), 0777);
                   mkdir($root.'/upload/'.$folder.'/'.date('Y').'/'.date('m'), 0777);
                   $filepath  = $root.'/upload/'.$folder.'/'.date('Y').'/'.date('m');
                }     
            $fileName = md5(strtotime("now")).'.'.$extension;
            $fileSrc = explode('api/web', $filepath.'/'.$fileName)[1];

            $model->type    = $type;
            $model->created = date('Y-m-d H:i:s');
            $model->name    = $fileName;
            $model->path    = $fileSrc;
            $model->author_id = $this->checkauthuser();

            $model->save();
            $model->upload($filepath, $fileName);

            $protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'http://';

            return  ['attachment_id'=>$model->id, 'attachment_src'=> $protocol.$_SERVER['SERVER_NAME'].$model->path];
        }
        
    }
}
