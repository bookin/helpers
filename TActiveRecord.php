<?php
/**
 * Created by PhpStorm.
 * User: Bookin
 * Date: 20.09.2015
 * Time: 16:56
 */

namespace bookin\yii2\helpers;


use yii\db\ActiveQueryInterface;
use yii\db\ActiveRecord;
use Yii;
use yii\helpers\ArrayHelper;

trait TActiveRecord
{
    /**
     * Creates and populates a set of models.
     *
     * <code>
     *
     * $models = Model::createMultiple(Model::className(), $_POST["Model"]);
     *
     * Model::loadMultiple($models, $postForMultiple);
     *
     * Model::validateMultiple($models)
     *
     * </code>
     *
     * @param string $modelClass
     * @param array $data
     * @param array $multipleModels
     * @return array
     */
    public static function createMultiple($modelClass, $data=[], $multipleModels = [])
    {
        /** @var ActiveRecord $model */
        $model    = new $modelClass;
        $formName = $model->formName();
        $post     = !empty($data)?$data:Yii::$app->request->post($formName);
        $models   = [];

        if (! empty($multipleModels)) {
            $keys = array_keys(ArrayHelper::map($multipleModels, 'id', 'id'));
            $multipleModels = array_combine($keys, $multipleModels);
        }

        if ($post && is_array($post)) {
            foreach ($post as $i => $items) {
                if (isset($items['id']) && !empty($items['id']) && isset($multipleModels[$items['id']])) {
                    $models[] = $multipleModels[$items['id']];
                } else {
//                    $relations = $model->getRelationsList();
//                    $model = new $modelClass;
//                    foreach($items as $name=>$value){
//                        /** @var ActiveQuery $query */
//                        $name = substr($name,0, -5);
//                        if(isset($relations[$name])){
//                            $query = $relations[$name];
//                            /* hasMany */
//                            if($query->multiple == true && is_array($value)){
//                                $model->$name = [];
//                                foreach($value as $v){
//                                    $model->$name[] = new $query->modelClass;
//                                }
//                            /* hasOne */
//                            }elseif($query->multiple == false && !is_array($value)){
//                                $model->$name = new $query->modelClass;
//                            }
//                        }
//                    }
                    if(isset($items['id'])&&!empty((int)$items['id'])){
                        $model = $modelClass::find()->where("{$model->primaryKey()[0]}=:id",[':id'=>$items['id']])->one();
                    }

                    if($model){
                        $models[] = $model;
                    }else{
                        $models[] = new $modelClass;
                    }

                }
            }
        }

        unset($model, $formName, $post);

        return $models;
    }

    /**
     * Returns all relations
     * @return ActiveQueryInterface[]
     */
    public function getRelationsList(){
        $relations = [];
        $reflection = new \ReflectionClass($this);
        $methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);
        if($methods){
            /** @var \ReflectionMethod $method */
            foreach($methods as $method){
                if(
                    substr($method->getName(), 0, 3) !== 'get'
                    || $method->getName() == __FUNCTION__
                    || !empty($method->getParameters())
                ){
                    continue;
                }
                $relation = $this->{$method->getName()}();
                if($relation instanceof ActiveQueryInterface){
                    $relations[lcfirst(substr($method->getName(), 3))] = $relation;
                }
            }
        }
        return $relations;
    }

}