<?php


namespace kurrata\behaviors;


use yii\base\ActionEvent;
use yii\base\Behavior;
use yii\web\Controller;

/**
 * Sets php execution time limit for actions in seconds
 * ~~~
 * public function behaviors() {
 *     return [
 *         'executionTime' => [
 *             'class' => kurrata\behaviors\ExecutionTimeBehavior::className(),
 *             'time' => [
 *                 'index'  => 60,
 *             ],
 *         ],
 *     ];
 * }
 * ~~~
 * Class ExecutionTimeBehavior
 * @package kurrata\behaviors
 */
class ExecutionTimeBehavior extends Behavior {

    public $time = [];

    public function events() {
        return [Controller::EVENT_BEFORE_ACTION => 'beforeAction'];
    }

    /**
     * @param ActionEvent $event
     *
     * @return bool
     */
    public function beforeAction($event) {
        if (isset($this->time[$event->action->id]))
            set_time_limit($this->time[$event->action->id]);
        return $event->isValid;
    }

}