<?php
namespace kurrata\behaviors;

use yii\behaviors\AttributeBehavior;
use yii\db\BaseActiveRecord;

class UserBehavior extends AttributeBehavior {
    public $createdByAttribute = 'created_by';
    public $updatedByAttribute = 'updated_by';

    /**
     * @inheritdoc
     */
    public function init() {
        parent::init();

        if (empty($this->attributes)) {
            $this->attributes = [
                BaseActiveRecord::EVENT_BEFORE_INSERT => [$this->updatedByAttribute, $this->createdByAttribute],
                BaseActiveRecord::EVENT_BEFORE_UPDATE => $this->updatedByAttribute,
            ];
        }
    }

    public function touch($attribute) {
        $this->owner->updateAttributes(array_fill_keys((array)$attribute, $this->getValue(null)));
    }

    /**
     * @inheritdoc
     */
    protected function getValue($event) {
        try {
            return \Yii::$app->getUser()->getId();
        } catch (\Exception $e) {
            return null;
        }
    }
}