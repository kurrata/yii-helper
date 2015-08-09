<?php

namespace kurrata\helpers;

class StringHelper extends \yii\helpers\StringHelper {
    /**
     * Makes 1st letter uppercase
     *
     * @param $string
     *
     * @return string
     */
    public static function uppercase1st($string) {
        return ucfirst($string);
    }
}