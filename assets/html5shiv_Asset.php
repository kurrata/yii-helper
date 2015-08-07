<?php
namespace kurrata\assets;

use yii\web\AssetBundle;
use yii\web\View;

class html5shiv_Asset extends AssetBundle {
    public $sourcePath = '@vendor/bower/html5shiv/dist';
    public $js = ['html5shiv.min.js'];
    public $jsOptions = [
        'condition' => 'lt IE 9',
        'position'  => View::POS_HEAD,
    ];
}