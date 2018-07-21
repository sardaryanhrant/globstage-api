<?php

namespace app\common\components\entity;

use JsonSchema\Constraints\Constraint;
use JsonSchema\Validator;

class EntityValidator
{

    protected $config;

    protected $errors;

    protected $scheme;

    public function __construct(string $scheme)
    {
        $this->scheme = $scheme;
    }

    /**
     * @param mixed $config
     */
    public function setConfig($config): void
    {
        $this->config = (object)$config;
    }

    public function validate()
    {
        $cnf = json_decode(json_encode($this->config));//костыль для представления объектов в пхп массиве
        $validator = new Validator;
        $validator->validate(
            $cnf,
            $this->getSchemaObject(),
            Constraint::CHECK_MODE_COERCE_TYPES | Constraint::CHECK_MODE_VALIDATE_SCHEMA
        );

        $res = $validator->isValid();

        if (!$res) {
            $this->errors = $validator->getErrors();
        }

        return $res;
    }

    /**
     * @return mixed
     */
    public function getErrors()
    {
        return $this->errors;
    }

    protected function getSchemaObject() {
        return json_decode(file_get_contents($this->scheme));
    }
}