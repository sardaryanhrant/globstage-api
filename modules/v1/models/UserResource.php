<?php


namespace app\modules\v1\models;


use app\common\models\UserModel;
use yii\helpers\Inflector;
use yii\helpers\Url;
use yii\web\Link;
use yii\base\Arrayable;

/**
 * Class UserResource
 * @package app\modules\v1\models
 *
 * @property array $links
 * @property string $type
 */
class UserResource extends UserModel
{

    /**
     * @var array
     */
    protected $excludedFields = [
        'user_password',
    ];
    
    protected $alias = 'users';
    protected $relationships = ['friend' => 'friend'];

    public function rules()
    {
        return [
            [['user_name', 'user_password','user_email'], 'required'],
            [['user_email'], 'unique', 'on'=>['insert','update']],  
            [
                [   'user_last_name', 'user_photo','user_status',
                    'user_location', 'user_date_of_birth', 'user_marital_status',
                    'user_country', 'user_city', 'user_city','user_announced', 'user_privacy', 
                    'user_last_visit' 
                ], 
                'string', 'max' => 255
            ], 

            [['user_friends_num',], 'integer'], 
              
        ];
    }

    /**
     * @return string
     */
    public function getType()
    {
        return 'user';
    }

    /**
     * @param array $fields
     * @return array
     */
    public function getResourceAttributes(array $fields = [])
    {
        $attributes = array_diff($this->fields(), $this->excludedFields);

        foreach ($attributes as $key => $attribute) {
            $attribute = Inflector::camel2id(Inflector::variablize($attribute), '_');

            if (!empty($fields) && !in_array($attribute, $fields, true)) {
                unset($attributes[$key]);
            } else {
                $attributes[$key] = $this->$attribute;
            }
        }
        
        return $attributes;
    }

    /**
     * @return array
     */
    public function getLinks()
    {
        return [
            Link::REL_SELF => Url::to(Url::base(true).'/v1/users/'.$this->getId()),
        ];
    }


    public static function tableName()
    {
        return 'users';
    }

    /**
     * @param $name
     * @return array
     */
    public function getRelationshipLinks($name)
    {
        $primaryLinks = $this->getLinks();
        if (!array_key_exists(Link::REL_SELF, $primaryLinks)) {
            return [];
        }

        $resourceLink = is_string($primaryLinks[Link::REL_SELF]) ? rtrim($primaryLinks[Link::REL_SELF], '/') : null;
        if (!$resourceLink) {
            return [];
        }

        return [
            Link::REL_SELF => "{$resourceLink}/relationships/{$name}",
            'related'      => "{$resourceLink}/{$name}",
        ];
    }


    /**
     * @param $name
     * @param $relationship
     */
    public function setResourceRelationship($name, $relationship)
    {
     
    }     


    public function extraFields()
    {
        return ['friend'];
    }

    /**
     * @param array $linked
     * @return array
     */
    public function getResourceRelationships(array $linked = [])
    {
        $fields = [];
        if ($this instanceof Arrayable) {
            $fields = $this->extraFields();
        }
        $resolvedFields = $this->resolveFields($fields);
        $keys = array_keys($resolvedFields);
        $relationships = array_fill_keys($keys, null);
        $linkedFields = array_intersect($keys, $this->relationships);

        foreach ($linkedFields as $name) {
            $definition = $resolvedFields[$name];
            $relationships[$name] = is_string($definition) ? $this->$definition : call_user_func($definition, $this, $name);

        }


        return $relationships;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFriend()
    {
        return $this->hasMany( FriendResource::class, ['user_id' => 'id']);
    }


}