<?php

namespace kurrata\validators;

use yii;
use yii\validators\Validator;

class PhoneValidator extends Validator {
    /** Mobīlais telefons */
    const TYPE_MOBILE_PHONE = 1;
    /** Telefona numurs */
    const TYPE_PHONE = 2;
    /** Parastais telefona numurs */
    const TYPE_LAND_PHONE = 3;
    public $type = 2;
    public $prefix = false;

    public function validateAttribute($model, $attribute) {
        switch ($this->type) {
            case self::TYPE_MOBILE_PHONE:
                if (preg_match('/^2\d{7}$/', $model->{$attribute}) == false)
                    $this->addError($model, $attribute, Yii::t('app', '{attribute} has to be mobile phone number'));
                break;
            case self::TYPE_LAND_PHONE:
                if (preg_match('/^6\d{7}$/', $model->{$attribute}) == false)
                    $this->addError($model, $attribute, Yii::t('app', '{attribute} has to be normal phone number'));

                break;
            case self::TYPE_PHONE:
                if (preg_match('/^(6|2)\d{7}$/', $model->{$attribute}) == false)
                    $this->addError($model, $attribute, Yii::t('app', '{attribute} has to be phone number'));
                break;
        }
    }
}