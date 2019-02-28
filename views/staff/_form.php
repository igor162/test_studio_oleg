<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;

use yii\widgets\ActiveForm;

use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model app\models\Staff */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="staff-form">

    <?php $form = ActiveForm::begin([
        'id' => $model->formName(),
        'enableClientValidation' => true,
        'enableAjaxValidation' => (Yii::$app->request->get('form') === \app\models\Staff::FORM_TYPE_AJAX) ? true : false,
    ]); ?>

    <?= $form->field($model, 'first_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'last_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'patronymic')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'gender')->dropDownList($model->genderList, ['prompt' => '']) ?>

    <?= $form->field($model, 'wage')->textInput() ?>

    <?= $form->field($model, 'departments_data')->dropDownList($model->departmentsList,
            [
                'multiple'=>'multiple',
                'class'=>'chosen-select input-md required',
            ]
        ) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
