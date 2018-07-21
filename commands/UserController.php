<?php

namespace app\commands;


use app\common\models\UserModel;
use yii\console\Controller;
use yii\console\ExitCode;

class UserController extends Controller
{

    public function actionCreate($userName, $password)
    {
        $account = UserModel::findOne(['login' => $userName]);
        if (!empty($account)) {
            $this->stdout(sprintf("User %s already exists.\n", $userName));

            return ExitCode::DATAERR;
        }

        $hash = \Yii::$app->getSecurity()->generatePasswordHash($password);
        $account = new UserModel();
        $account->login = $userName;
        $account->password = $hash;

        if (!$account->save()) {
            $this->stdout("Internal error. Could not create user.\n");

            return ExitCode::UNSPECIFIED_ERROR;
        }

        $this->stdout(
          sprintf(
            "User %s has been created with password \"%s\".\n",
            $userName,
            $password
          )
        );

        return ExitCode::OK;
    }

    public function actionChangePassword($userName, $password)
    {
        $account = UserModel::findOne(['login' => $userName]);
        if (empty($account)) {
            $this->stdout(sprintf("User %s not found.\n", $userName));

            return ExitCode::DATAERR;
        }

        $account->password = \Yii::$app->getSecurity()->generatePasswordHash($password);

        if (!$account->save()) {
            $this->stdout("Internal error.\n");

            return ExitCode::UNSPECIFIED_ERROR;
        }

        $this->stdout(
          sprintf(
            "Password has been changed for the user \"%s\".\n",
            $userName
          )
        );

        return ExitCode::OK;
    }
}