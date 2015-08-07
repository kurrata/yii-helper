<?php
namespace kurrata\assets;

use yii\web\AssetBundle;
use yii\web\View;

class respond_Asset extends AssetBundle {
    public $sourcePath = '@vendor/bower/respond/dest';
    public $js = ['respond.min.js'];
    public $jsOptions = [
        'condition' => 'lt IE 9',
        'position'  => View::POS_HEAD,
    ];
}