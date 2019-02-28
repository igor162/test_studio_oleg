<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = \Yii::t('app', 'Authorization');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="form-box" id="login-box">
    <div class="header"><?= Html::encode($this->title) ?></div>
    <?php $form = ActiveForm::begin([
        'id' => 'login-form',
        'options' => ['class' => 'form-horizontal'],
        'fieldConfig' => ['template' => "{input}\n{error}"],
    ]); ?>
    <div class="body bg-gray">
        <?= $form->field($model, 'username')->textInput(['autofocus' => true, 'placeholder'=>$model->getAttributeLabel('username')]) ?>
        <?= $form->field($model, 'password')->passwordInput(['placeholder'=>$model->getAttributeLabel('password')]) ?>
        <?= $form->field($model, 'rememberMe')->checkbox() ?>
    </div>

    <div class="footer">
            <div class="col-lg-offset-14 col-lg-14">
                <?= Html::submitButton(\Yii::t('app', 'Log in'), ['class' => 'btn bg-olive btn-block', 'name' => 'login-button']) ?>
            </div>
        </div>

    <?php ActiveForm::end(); ?>

    <div class="col-lg-offset-1 text-center" style="color:#ed1010;">
        <?= \Yii::t('app', 'You may login with: <strong>admin/admin</strong> or <strong>demo/demo</strong>') ?>
    </div>
</div>
