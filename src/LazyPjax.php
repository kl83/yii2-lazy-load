<?php

namespace kl83;

use Yii;
use yii\base\BaseObject;
use yii\widgets\Pjax;

class LazyPjax extends BaseObject
{
    /**
     * @var string
     */
    public $view;

    /**
     * @var array
     */
    public $params = [];

    /**
     * @var object
     */
    public $context;

    /**
     * @var array
     */
    public $pjaxConfig = [];

    /**
     * @var string
     */
    public $getParameter = 'lazy-render';

    public function render()
    {
        $viewCmp = Yii::$app->getView();
        $pjax = Pjax::begin($this->pjaxConfig);
        if (Yii::$app->getRequest()->get($this->getParameter)) {
            echo $viewCmp->render($this->view, $this->params);
        } else {
            $viewCmp->registerJs('
jQuery(function($){
var l = window.location;
var url = l.pathname + (l.search ? l.search + "&" : "?") + "' . $this->getParameter . '=1";
$("#' . $pjax->id . '").reload({url: url});
});');
        }
        Pjax::end();
    }
}
