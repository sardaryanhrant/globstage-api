<?php

namespace app\common\components;

use Yii;
use yii\web\ErrorHandler as EH;
use yii\web\Response;

class ErrorHandler extends EH {

  /**
   * Renders the exception.
   *
   * @param \Exception $exception the exception to be rendered.
   */
  protected function renderException($exception) {
    if (Yii::$app->has('response')) {
      $response = Yii::$app->getResponse();
      // reset parameters of response to avoid interference with partially created response data
      // in case the error occurred while sending the response.
      $response->isSent = FALSE;
      $response->stream = NULL;
      $response->data = NULL;
      $response->content = NULL;
    }
    else {
      $response = new Response();
    }

    $response->format = 'json';
    $response->setStatusCodeByException($exception);
    $response->data = $this->convertExceptionToArray($exception);
    $response->send();
  }

}