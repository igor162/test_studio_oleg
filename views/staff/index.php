<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\bootstrap\Modal;
use app\widgets\Helper;
use kartik\alert\AlertBlock;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\StaffSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Employees');
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
        'id' => 'modal-staff',
        'tabindex' => false // important for Select2 to work properly
    ],
]);
echo "<div id='modalContent-staff'> </div>";
Modal::end();
?>

<div class="staff-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>

        <?= Html::button('<i class="glyphicon glyphicon-plus-sign"></i> '.\Yii::t('app', 'Add «{attribute}»', ['attribute' => Yii::t('app', 'employee')]), [
            'id' => 'ButtonCreate',
            'value' => Url::toRoute(['/staff/create', 'returnUrl' => Helper::getReturnUrl(), 'form' => \app\models\Staff::FORM_TYPE_AJAX]),
            'class' => 'btn btn-warning btn-sm',
            'title' => \Yii::t('app', 'Create a «{attribute}»', ['attribute' => \Yii::t('app', 'Employee')]),
            'onclick' =>
                '   $("#modal-staff").modal("show")
                                        .find(".modal-header h4").text("' . \Yii::t('app', 'Adding data of «{attribute}»', ['attribute' => Yii::t('app', 'Staff data')]) . '")
                                        .end()
                                        .find(".modal-dialog").removeClass().addClass("modal-dialog modal-lg")
                                        .end()
                                        .find("#modalContent-staff")
                                        .load($(this).attr("value"));
                                            ',
        ])
            ?>

        <?= Html::button('<i class="glyphicon glyphicon-plus-sign"></i> '.\Yii::t('app', 'Add «{attribute}»', ['attribute' => Yii::t('app', 'Department')]), [
            'id' => 'ButtonCreate',
            'value' => Url::toRoute(['/departments/create', 'form' => \app\models\Departments::FORM_TYPE_AJAX,  'returnUrl' => Helper::getReturnUrl()]),
            'class' => 'btn btn-success btn-sm',
            'title' => \Yii::t('app', 'Create a «{attribute}»', ['attribute' => \Yii::t('app', 'department')]),
            'onclick' =>
                '   $("#modal-staff").modal("show")
                                        .find(".modal-header h4").text("' . \Yii::t('app', 'Adding data of «{attribute}»', ['attribute' => Yii::t('app', 'department')]) . '")
                                        .end()
                                        .find(".modal-dialog").removeClass().addClass("modal-dialog modal-lg")
                                        .end()
                                        .find("#modalContent-staff")
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
            'first_name',
            'last_name',
            'patronymic',
            [
                'attribute'=>'gender',
                'value' => function ($model) {
                    /* @var $model \app\models\Staff; */
                    return $model->genderOne;
                },
            ],
            [ //  список отделов
                'attribute'=>'departments_data',
                'format' => 'html',
                'value' => function ($model) {
                    /* @var $model \app\models\Staff; */
                    return $model->departmentsString;
                },
            ],
//            [
//                'attribute'=>'full_name',
//                'format' => 'html',
//                'value' => function ($model) {
//                    /* @var $model \app\models\Staff; */
//                    return $model->fullName;
//                },
//            ],
            [
                'attribute'=>'wage',
                'format' => ['decimal', 2],
            ],
//            'created_at',
            //'created_by',
//            'updated_at',
            //'updated_by',
            [
                'class' => 'yii\grid\ActionColumn',
                'options' => ['width' => '5%'],
                'template' => '{update} {delete}',
            ],
                ],
    ]); ?>
</div>
