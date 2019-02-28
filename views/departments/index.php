<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\bootstrap\Modal;
use kartik\alert\AlertBlock;
use app\widgets\Helper;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\DepartmentsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Departments');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Employees'), 'url' => ['staff/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?= AlertBlock::widget([
    'useSessionFlash' => true,
    'type' => AlertBlock::TYPE_GROWL,
])
?>

<?php
Modal::begin([
    'size' => Modal::SIZE_LARGE,
    'header' => '<h4 class="text-left" style="color: #000; font-size: 20px; font-weight: 500;"></h4>',
    'closeButton' => false,
    'toggleButton' => false,
    'options' => [
        'id' => 'modal-departments',
        'tabindex' => false // important for Select2 to work properly
    ],
]);
echo "<div id='modalContent-departments'> </div>";
Modal::end();
?>
<div class="departments-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::button('<i class="glyphicon glyphicon-plus-sign"></i> '.\Yii::t('app', 'Add «{attribute}»', ['attribute' => Yii::t('app', 'Department')]), [
            'id' => 'ButtonCreate',
            'value' => Url::toRoute(['create', 'form' => \app\models\Departments::FORM_TYPE_AJAX,  'returnUrl' => Helper::getReturnUrl()]),
            'class' => 'btn btn-success btn-sm',
            'title' => \Yii::t('app', 'Create a «{attribute}»', ['attribute' => \Yii::t('app', 'department')]),
            'onclick' =>
                '   $("#modal-departments").modal("show")
                                        .find(".modal-header h4").text("' . \Yii::t('app', 'Adding data of «{attribute}»', ['attribute' => Yii::t('app', 'department')]) . '")
                                        .end()
                                        .find(".modal-dialog").removeClass().addClass("modal-dialog modal-lg")
                                        .end()
                                        .find("#modalContent-departments")
                                        .load($(this).attr("value"));
                                            ',
        ])
        ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

//            'id',
            'name',
            [// Кол-во сотрудников в отделе
                'attribute'=>'count_staff',
                'format' => 'html',
                'options' => ['width' => '78'],
                'value' => function ($model) {
                    /* @var $model \app\models\Departments; */
                    return $model->countStaff;
                },
            ],

            [// Максимальная заработная плата в отделе
                'attribute'=>'max_wage',
                'format' => ['decimal', 2],
                'options' => ['width' => '78'],
                'value' => function ($model) {
                    /* @var $model \app\models\Departments; */
                    return $model->maxWage;
                },
            ],
            //'updated_by',

//            'created_at',
//            'created_by',
//            'updated_at',
            [
                'class' => 'yii\grid\ActionColumn',
                'options' => ['width' => '5%'],
                'template' => '{update} {delete}',
            ],
        ],
    ]); ?>
</div>
