<?php

namespace app\commands;

use app\common\components\entity\EntityException;
use app\common\components\entity\EntityMigration;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Console;

class EntityController extends Controller
{
    public $defaultAction = 'generate-migration';

    /**
     * @return Object|EntityMigration
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\di\NotInstantiableException
     */
    protected function getMigration()
    {
        return \Yii::$container->get('entityMigration', [], [
            'entityManager' => \Yii::$container->get('entityManager')
        ]);
    }

    /**
     * @param string $name
     * @return int
     * @throws \Exception
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\di\NotInstantiableException
     */
    public function actionGenerateMigration(string $name)
    {
        Console::output(Console::ansiFormat("Генерация миграции для сущности: {$name}", [
            Console::FG_GREEN
        ]));

        try {
            $migration = $this->getMigration();
            $migration->create($name);
        } catch (EntityException $e) {
            Console::output(Console::ansiFormat("Ошибка: " . $e->getMessage(), [
                Console::FG_RED
            ]));

            return ExitCode::UNSPECIFIED_ERROR;
        }
    }

    /**
     * @param string $name
     * @return int
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\di\NotInstantiableException
     */
    public function actionDiffMigration(string $name)
    {
        Console::output(Console::ansiFormat("Генерация миграции расхождений для сущности: {$name}", [
            Console::FG_GREEN
        ]));

        try {
            $migration = $this->getMigration();
            $migration->diff($name);
        } catch (EntityException $e) {
            Console::output(Console::ansiFormat("Ошибка: " . $e->getMessage(), [
                Console::FG_RED
            ]));

            return ExitCode::UNSPECIFIED_ERROR;
        }
    }
}