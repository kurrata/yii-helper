<?php


namespace kurrata\behaviors;


use yii\base\ActionEvent;
use yii\base\Behavior;
use yii\web\Controller;

/**
 * Sets php memory limit for actions in MB
 * ~~~
 * public function behaviors() {
 *     return [
 *         'memoryLimit' => [
 *             'class' => kurrata\behaviors\MemoryBehavior::className(),
 *             'limit' => [
 *                 'index'  => 1024,
 *             ],
 *         ],
 *     ];
 * }
 * ~~~
 * Class MemoryLimitBehavior
 * @package kurrata\behaviors
 */
class MemoryLimitBehavior extends Behavior {

    public $limit = [];

    public function events() {
        return [Controller::EVENT_BEFORE_ACTION => 'beforeAction'];
    }

    /**
     * @param ActionEvent $event
     *
     * @return bool
     */
    public function beforeAction($event) {
        if (isset($this->limit[$event->action->id]))
            ini_set('memory_limit', $this->limit[$event->action->id] . 'M');
        return $event->isValid;
    }

}