<?php
namespace kurrata\models;

use app\models\User;
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

    /**
     * @param             $attr array
     * @param bool|string $field
     * @param bool|array  $except
     *
     * @return array|string
     */
    public static function getMode($attr, $field = false, $except = false) {
        if ($field !== false) {
            if (isset($attr[$field]))
                return $attr[$field];
            return $field;
        }
        if ($except !== false) {
            foreach ($except as $i)
                unset($attr[$i]);
        }
        return $attr;
    }

    public function behaviors() {
        return [
            ['class' => TimestampBehavior::className()],
            ['class' => UserBehavior::className()],
        ];
    }

    public function getCreator() {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
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
        if (isset($options['join']))
            $query->joinWith($options['join']);

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
            'query'      => $query,
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

        if (isset($options['filter'])) {
            foreach (array_keys($this->getAttributes()) as $attr) {
                if (in_array($attr, array_keys($options['filter']))) {
                    switch ($options['filter'][$attr]) {
                        case "=":
                            $query->andFilterWhere(['=', $this->tableName() . '.' . $attr, $this->getAttribute($attr)]);
                    }
                } else {
                    $query->andFilterWhere(['like', $this->tableName() . '.' . $attr, $this->getAttribute($attr)]);
                }
            }
        } else {
            foreach (array_keys($this->getAttributes()) as $attr) {
                $query->andFilterWhere(['like', $this->tableName() . '.' . $attr, $this->getAttribute($attr)]);
            }
        }
        return $dataProvider;
    }
}
