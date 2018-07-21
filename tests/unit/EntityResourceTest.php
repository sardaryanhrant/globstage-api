<?php
use app\common\components\entity\EntityInstance;
use app\common\components\entity\EntityRules;
use app\modules\v1\models\EntityResource;


class EntityResourceTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected $entity_data = [
        'meta' => ['parent' => false],
        'attributes' => [
            ['name' => 'name', 'type' => 'string', 'required' => true, 'unique' => true],
            ['name' => 'form', 'type' => 'string', 'required' => true, 'max' => 255],
            ['name' => 'id_file_packaging_image', 'type' => 'integer'],
            ['name' => 'form_description', 'type' => 'string', 'max' => 255],
            ['name' => 'packaging', 'type' => 'string'],
            ['name' => 'basis', 'type' => 'string'],
            ['name' => 'unit', 'type' => 'string', 'max' => 255],
        ],
        'relations' => [
            ['link' => 'tmc-types', 'property' => 'id_tmc_type', 'propname' => 'tmcType'],
            ['link' => 'measures', 'property' => 'id_measure', 'propname' => 'measure'],
        ],
        'plural_relations' => [
            ['link' => 'content-of-active-substances', 'property' => 'id_tmc'],
            ['link' => 'descriptions', 'property' => 'entity_id',
                'additional_fields' => [
                    'entity_type' => 'drug'
                ]
            ],
        ]
    ];

    /**
     * @var EntityInstance
     */
    protected $entityInstance;

    /**
     * @var EntityResource
     */
    protected $entityResource;
    
    protected function _before()
    {
        $this->entityInstance = new EntityInstance($this->entity_data, 'drugs');
        Yii::$container->set('entityInstance', $this->entityInstance);

        $this->entityResource = Yii::$container->get('entityResource');
        $this->entityResource->setEntityInstance($this->entityInstance);
    }

    protected function _after()
    {
    }

    // tests
    public function testRules()
    {
        $entityRules = new EntityRules();
        $entityRules->init($this->entityInstance);

        $rules = $entityRules->getRules();

        $this->assertNotEmpty($rules);

        $required_fields = ['name', 'form'];
        $uniq_fields = ['name'];
        $str_fields = ['name', 'form', 'form_description', 'packaging', 'basis', 'unit'];
        $int_fields = ['id_tmc_type', 'id_measure', 'id_file_packaging_image'];
        $limits = ['form' => 255, 'form_description' => 255, 'unit' => 255];
        $relations = ['id_tmc_type' => 'tmcType', 'id_measure' => 'measure'];

        foreach ($rules as $rule) {
            $fields = $rule[0];
            $type = $rule[1];

            switch ($type) {
                case 'unique':
                    $this->assertEquals($uniq_fields, $fields, 'Уникальные поля одинаковы', 0.0, 10, true);
                    $this->assertEquals($rule['on'], ['insert', 'update']);
                    break;
                case 'required':
                    $this->assertEquals($required_fields, $fields, 'Обязательные поля одинаковы', 0.0, 10, true);
                    $this->assertEquals($rule['on'], ['insert', 'update']);
                    break;
                case 'integer':
                    $this->assertEquals($int_fields, $fields, 'Числовые поля одинаковы', 0.0, 10, true);
                    break;
                case 'string':
                    if (is_array($fields)) {
                        $this->assertEquals($str_fields, $fields, 'Строковые поля одинаковы', 0.0, 10, true);
                    }
                    else if (is_string($fields)) {
                        $fieldname = $fields;
                        $this->assertEquals($limits[$fieldname], $rule['max']);
                        unset($limits[$fieldname]);
                    }
                    break;
                case 'app\common\components\RelationExistValidator':
                    $fieldname = $fields;
                    $this->assertEquals($relations[$fieldname], $rule['targetRelation']);
                    unset($relations[$fieldname]);

            }
        }

        $this->assertEmpty($limits, 'Указаны все ограничения по макс. длине');
        $this->assertEmpty($relations, 'Указаны все связи');
    }
}