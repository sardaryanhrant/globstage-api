<?php

namespace app\modules\v1\models;

use tuyakhov\jsonapi\LinksInterface;
use tuyakhov\jsonapi\ResourceInterface;


class TmcTypeResource extends BaseResource implements ResourceInterface, LinksInterface
{

    protected $alias = 'tmc-types';
    protected $excludedFields = ['id'];

    public function getType()
    {
        return 'tmcType';
    }

    public static function tableName()
    {
        return 'tmc_types';
    }

    public function rules()
    {
        return [
            [['name'], 'required', 'on' => 'insert'],
            [['name'], 'unique', 'on' => 'insert'],
            [['name', 'description', 'tmc_class'], 'string'],
            ['tmc_class', 'in', 'range' => ['drug', 'equipment', 'vaccine']]
        ];
    }
}