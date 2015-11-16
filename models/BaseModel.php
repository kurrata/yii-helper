<?php
namespace kurrata\models;

use kurrata\behaviors\UserBehavior;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;

/**
 * Class BaseModel
 * @package app\models\base
 * @property integer $mode
 * @property integer $created_by
 */
class BaseModel extends ActiveRecord {
    const SCENARIO_CREATE = 'SCENARIO_CREATE';
    const SCENARIO_UPDATE = 'SCENARIO_UPDATE';

    public function behaviors() {
        return [
            ['class' => TimestampBehavior::className()],
            ['class' => UserBehavior::className()],
        ];
    }

    /**
     * Returns DataProvider for gridView tables
     * <code>
     * $options = [
     *      'where'=>[
     *          ['=', 'client_id', 5],
     *          ['=', 'client_id', 6],
     *      ]
     *      'join'=>[
     *          'client'
     *       ]
     *      'group'=>[
     *          PackageChange::tableName() . '.package_id',
     *          PackageChange::tableName() . '.action_id'
     *      ]
     *      'pagination'=>5
     *      'sort'=>[
     *          'defaultOrder' => ['created_at' => SORT_DESC],
     *      ]
     *      'params'=>Yii::$app->request->get()
     *      'filter'=>[
     *          'attr'=>'='
     *      ]
     * ]
     *</code>
     *
     * @param array $options
     *
     * @return ActiveDataProvider
     */
    public function search($options = []) {
        $query = $this->find();

        if (isset($options['where']) && is_array($options['where'])) {
            foreach ($options['where'] as $w) {
                $query->andWhere($w);
            }
        }
        if (isset($options['join'])) {
            foreach ($options['join'] as $table => $alias)
                $query->joinWith($table);
        }

        if (isset($options['distinct'])) {
            $query->select($options['distinct']);
            $query->distinct();
        }

        if (isset($options['group'])) {
            $query->groupBy($options['group']);
        }

        if (isset($options['pagination']))
            $pagination = $options['pagination'];
        else
            $pagination = 10;

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $pagination,
            ],
        ]);

        $sort = ['attributes' => []];
        foreach (array_keys($this->getAttributes()) as $attr) {
            $sort['attributes'][$attr] = [];
        }
        $dataProvider->setSort($sort);

        if (isset($options['sort'])) {
            if (isset($options['sort']['attributes'])) {
                foreach ($options['sort']['attributes'] as $attr => $value) {
                    $sort['attributes'][$attr] = $value;
                }
            }
            if (isset($options['sort']['defaultOrder'])) {
                $sort['defaultOrder'] = $options['sort']['defaultOrder'];
            }
        }
        $dataProvider->setSort($sort);

        if (isset($options['params'])) {
            $this->load($options['params']);
        } else {
            $this->load(Yii::$app->request->get());
        }

        foreach (array_keys($this->getAttributes()) as $attr) {
            $operation = 'like';
            if (isset($options['filter'][$attr]))
                $operation = $options['filter'][$attr];
            if ($pos = strpos($attr, '.')) {
                $relation = substr($attr, 0, $pos);
                $query->andFilterWhere([$operation, str_replace($relation, $options['join'][$relation], $attr), $this->getAttribute($attr)]);
            } else {
                $query->andFilterWhere([$operation, $this->tableName() . '.' . $attr, $this->getAttribute($attr)]);
            }
        }
        return $dataProvider;
    }
}
