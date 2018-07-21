<?php

namespace app\common\components\media;

use yii\base\Component;
use yii\helpers\FileHelper;

class UploadFileRepository extends Component implements MediaRepositoryInterface
{
    public $path;
    public $depth;

    /**
     * @param $hash
     * @return string
     */
    public function getPath(string $hash, string $ext) : string
    {
        $relPath = implode(DIRECTORY_SEPARATOR, array_slice(str_split($hash, 2), 0, $this->depth));
        return $this->path . DIRECTORY_SEPARATOR . $relPath . DIRECTORY_SEPARATOR . $hash . '.' . $ext;
    }

    /**
     * @param string $filePath
     * @return string
     */
    public function getFilePath(string $filePath) : string
    {
        return FileHelper::normalizePath(\Yii::getAlias($filePath));
    }

    /**
     * Сохраняет загружаемый файл и возвращает путь относительно корневой директории
     * @param string $file Путь до файла
     * @param string $name Имя сохраняемого файла
     * @param string $ext Расширение сохраняемого файла
     * @return string
     * @throws MediaException
     */
    public function save(string $file, string $name, string $ext) : string
    {
        $filePath = $this->getPath($name, $ext);
        $savePath = \Yii::getAlias('@webroot') . $filePath;

        $path = $this->getFilePath($savePath);
        $dir = dirname($path);
        if (!is_dir($dir)) {
            try {
                FileHelper::createDirectory($dir);
            } catch (\Exception $e) {
                throw new MediaException("Не хватает прав на запись в директорию {$savePath}");
            }
        }

        rename($file, $savePath);

        return $filePath;
    }

    /**
     * @param string $filePath
     */
    public function delete(string $filePath) : void
    {
        if (is_file($filePath)) {
            unlink($filePath);
        }
    }
}