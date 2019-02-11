<?php

namespace kl83;

use Yii;
use yii\base\BaseObject;
use yii\helpers\Url;
use yii\widgets\Pjax;

/**
 * When the page is generated, only empty pjax tag is included in the HTML.
 * After the page loads, the specified view is loaded via pjax.
 * Example:
 * (new LazyRender([
 *  'view' => 'slow-render-view',
 * ]))->render();
 */
class LazyPjax extends BaseObject
{
    /**
     * @var string View path to render
     */
    public $view;

    /**
     * @var array View params
     */
    public $params = [];

    /**
     * @var object View context
     */
    public $context;

    /**
     * @var array
     */
    public $pjaxConfig = [
        'timeout' => 9999999,
    ];

    /**
     * @var string
     */
    public $getParameter = '_lazy-pjax';

    public function render()
    {
        $viewCmp = Yii::$app->getView();
        $pjax = Pjax::begin($this->pjaxConfig);
        if (Yii::$app->getRequest()->get($this->getParameter)) {
            Yii::$app->getResponse()->getHeaders()->add('X-Pjax-Url', Url::current([
                $this->getParameter => null,
            ]));
            echo $viewCmp->render($this->view, $this->params);
        } else {
            $viewCmp->registerJs('
jQuery(function($){
var l = window.location;
$.pjax.reload("#' . $pjax->id . '", {
    url: l.pathname + (l.search ? l.search + "&" : "?") + "' . $this->getParameter . '=1",
    timeout: ' . $this->pjaxConfig['timeout'] . '
});
});');
        }
        Pjax::end();
    }
}
